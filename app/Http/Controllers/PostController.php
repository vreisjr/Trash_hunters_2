<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::with([
            'user',
            'categorias',
            'comentarios.user',
            'comentarios.curtidas',
            'comentarios.filhos.user',
            'comentarios.filhos.curtidas',
            'curtidas',
            'attachments',
        ])->latest()->get();

        $categorias = Categoria::orderBy('nome')->get();

        return view('pages.posts.index', [
            'posts' => $posts,
            'categorias' => $categorias,
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
            'price_type' => ['required', 'in:free,paid'],
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
            'price_type' => $validated['price_type'],
            'price' => $validated['price_type'] === 'paid' ? $validated['price'] : null,
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
            ->route('posts.index')
            ->with('status', 'Postagem publicada com sucesso!');
    }

    public function edit(Request $request, Post $post): View
    {
        if ($post->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($post->edited_at !== null) {
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
        if ($post->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($post->edited_at !== null) {
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
        }

        return response()->json([
            'curtido' => $curtido,
            'total' => $post->curtidas()->count(),
        ]);
    }

    public function destroy(
        Request $request,
        Post $post
    ): RedirectResponse {
        $user = $request->user();

        $isAdmin = $user->role === 'admin';

        $isResponsiblePremium =
            $user->role === 'premium'
            && $user->district !== null
            && $user->district === $post->district;

        if (! $isAdmin && ! $isResponsiblePremium) {
            abort(403);
        }

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('status', 'Publicação excluída com sucesso!');
    }
}
