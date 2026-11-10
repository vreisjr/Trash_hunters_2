<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Coleta;
use App\Models\Post;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::where('post_type', 'feed')->with([
            'user',
            'categorias',
            'comentarios.user',
            'comentarios.curtidas',
            'comentarios.filhos.user',
            'comentarios.filhos.curtidas',
            'curtidas',
            'savedByUsers' => fn ($query) => $query->whereKey(auth()->id()),
            'attachments',
        ])->latest()->get();

        $categorias = Categoria::orderBy('nome')->get();

        // Cada usuário só deve ver como coletados os itens que ele próprio coletou.
        $coletados = Coleta::whereIn('post_id', $posts->pluck('id'))
            ->where('coletor_id', auth()->id())
            ->pluck('post_id')
            ->all();

        return view('pages.posts.index', [
            'posts' => $posts,
            'categorias' => $categorias,
            'coletados' => $coletados,
        ]);
    }

    public function dashboard(): View
    {
        $posts = Post::where('post_type', 'marketplace')->with([
            'user',
            'categorias',
            'attachments',
            'savedByUsers' => fn ($query) => $query->whereKey(auth()->id()),
        ])
            ->latest()
            ->get();

        $categorias = Categoria::orderBy('nome')->get();

        // Cada usuário só deve ver como coletados os itens que ele próprio coletou.
        $coletados = Coleta::whereIn('post_id', $posts->pluck('id'))
            ->where('coletor_id', auth()->id())
            ->pluck('post_id')
            ->all();

        return view('dashboard', [
            'posts' => $posts,
            'categorias' => $categorias,
            'coletados' => $coletados,
        ]);
    }

    public function create(): View
    {
        $categorias = Categoria::orderBy('nome')->get();

        return view('pages.posts.create', [
            'categorias' => $categorias,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'post_type' => ['required', 'in:feed,marketplace'],
            'price_type' => ['nullable', 'in:free,paid'],
            'price' => ['nullable', 'required_if:price_type,paid', 'numeric', 'min:0.01', 'max:99999999.99'],
            'categoria_ids' => ['nullable', 'array'],
            'categoria_ids.*' => ['exists:categorias,id'],
            'media' => ['nullable', 'array', 'max:5'],
            'media.*' => ['file', 'max:102400'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
        ]);

        $data = [
            'content' => $validated['content'],
            'post_type' => $validated['post_type'],
            'price_type' => $validated['post_type'] === 'marketplace' ? ($validated['price_type'] ?? 'free') : 'free',
            'price' => $validated['post_type'] === 'marketplace' && ($validated['price_type'] ?? 'free') === 'paid'
                ? $validated['price']
                : null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'address' => $validated['address'] ?? null,
            'district' => $request->user()->district,
        ];

        $videoCount = 0;
        $imageCount = 0;

        foreach ($request->file('media', []) as $index => $file) {
            $isVideo = str_starts_with((string) $file->getMimeType(), 'video/');

            $request->validate([
                "media.{$index}" => $isVideo
                    ? ['file', 'mimes:mp4,mov,webm', 'max:102400']
                    : ['file', 'mimes:jpg,jpeg,png,gif', 'max:10240'],
            ]);

            if (str_starts_with((string) $file->getMimeType(), 'video/')) {
                $videoCount++;
            } else {
                $imageCount++;
            }
        }

        if ($videoCount > 1 || $imageCount > 4) {
            throw ValidationException::withMessages([
                'media' => ['Cada postagem pode conter até 4 imagens e 1 vídeo.'],
            ]);
        }

        $post = $request->user()->posts()->create($data);

        User::query()
            ->where('id', '!=', $request->user()->id)
            ->each(fn (User $user) => $user->notify(new SystemNotification(
                'new_post',
                $request->user()->name.' publicou uma nova postagem: "'.Str::limit($post->content, 120).'".',
                route($validated['post_type'] === 'marketplace' ? 'dashboard' : 'posts.index'),
            )));

        if (! empty($validated['categoria_ids'])) {
            $post->categorias()->attach($validated['categoria_ids']);
        }

        foreach (Arr::wrap($request->file('media')) as $file) {
            $isVideo = str_starts_with((string) $file->getMimeType(), 'video/');

            $post->attachments()->create([
                'file_path' => $file->store('posts', 'public'),
                'file_type' => $isVideo ? 'video' : 'image',
                'file_size' => $file->getSize(),
            ]);
        }

        return redirect()
            ->route($validated['post_type'] === 'marketplace' ? 'dashboard' : 'posts.index')
            ->with('status', 'Postagem publicada com sucesso!');
    }

    public function edit(Request $request, Post $post): View
    {
        $user = $request->user();
        $isPrivileged = $user->isModerator();

        if (! $isPrivileged && $post->user_id !== $user->id) {
            abort(403);
        }

        if (! $isPrivileged && $post->edited_at !== null) {
            abort(403, 'Esta publicação já foi editada.');
        }

        return view('pages.posts.edit', [
            'post' => $post,
        ]);
    }

    public function update(
        Request $request,
        Post $post
    ): RedirectResponse {
        $user = $request->user();
        $isPrivileged = $user->isModerator();

        if (! $isPrivileged && $post->user_id !== $user->id) {
            abort(403);
        }

        if (! $isPrivileged && $post->edited_at !== null) {
            return redirect()
                ->route('posts.index')
                ->with(
                    'error',
                    'Esta publicação já foi editada e não pode ser alterada novamente.'
                );
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $post->update([
            'content' => $validated['content'],
            'edited_at' => now(),
        ]);

        return redirect()
            ->route('posts.index')
            ->with('status', 'Publicação atualizada com sucesso!');
    }

    // Coleta um item "Para doação": grava no histórico de quem doou e de quem coletou
    public function coletar(Request $request, Post $post): RedirectResponse
    {
        $user = $request->user();

        if ($post->user_id === $user->id) {
            return back()->withErrors(['coleta' => 'Você não pode coletar o seu próprio item.']);
        }

        $coleta = Coleta::firstOrCreate(
            ['post_id' => $post->id],
            [
                'doador_id' => $post->user_id,
                'coletor_id' => $user->id,
                'item' => Str::limit($post->content, 80),
                'data_hora' => now(),
            ]
        );

        if (! $coleta->wasRecentlyCreated) {
            return back()->withErrors(['coleta' => 'Este item já foi coletado por outra pessoa.']);
        }

        $post->user->notify(new SystemNotification(
            'collection',
            $user->name.' demonstrou interesse em coletar seu item "'.Str::limit($post->content, 120).'".',
            route('historico.doacoes'),
        ));

        return back()->with('status', 'Item coletado! Ele já aparece no seu histórico de coletas.');
    }

    public function toggleLike(
        Request $request,
        Post $post
    ): JsonResponse {
        $userId = $request->user()->id;

        $curtida = $post->curtidas()
            ->where('user_id', $userId)
            ->first();

        if ($curtida) {
            $curtida->delete();
            $curtido = false;
        } else {
            $post->curtidas()->create([
                'user_id' => $userId,
            ]);

            $curtido = true;

            if ($post->user_id !== $userId) {
                $post->user->notify(new SystemNotification(
                    'post_like',
                    $request->user()->name.' curtiu sua postagem "'.Str::limit($post->content, 120).'".',
                    route($post->post_type === 'marketplace' ? 'dashboard' : 'posts.index'),
                ));
            }
        }

        return response()->json([
            'curtido' => $curtido,
            'total' => $post->curtidas()->count(),
        ]);
    }

    public function toggleSave(Request $request, Post $post): JsonResponse
    {
        $salvo = $post->salvos()
            ->where('user_id', $request->user()->id)
            ->first();

        if ($salvo) {
            $salvo->delete();
            $salvoAgora = false;
        } else {
            $post->salvos()->create([
                'user_id' => $request->user()->id,
            ]);
            $salvoAgora = true;
        }

        return response()->json([
            'salvo' => $salvoAgora,
        ]);
    }

    public function saved(Request $request): View
    {
        $posts = $request->user()
            ->savedPosts()
            ->with(['user', 'categorias', 'attachments'])
            ->orderByPivot('created_at', 'desc')
            ->paginate(10);

        return view('pages.posts.saved', [
            'posts' => $posts,
        ]);
    }

    public function destroy(
        Request $request,
        Post $post
    ): RedirectResponse {
        $user = $request->user();

        if (! $user->isModerator()) {
            abort(403);
        }

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('status', 'Publicação excluída com sucesso!');
    }
}
