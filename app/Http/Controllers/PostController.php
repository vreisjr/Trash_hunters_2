<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Show the composer and the feed of posts.
     */
    public function index(): View
    {
        $posts = Post::with([
            'user',
            'categorias',
            'comentarios.user',
            'comentarios.curtidas',
            'curtidas',
        ])->latest()->get();

        $categorias = Categoria::orderBy('nome')->get();

        return view('pages.posts.index', [
            'posts' => $posts,
            'categorias' => $categorias,
        ]);
    }

    /**
     * Show the form to create a new post.
     */
    public function create(): View
    {
        $categorias = Categoria::orderBy('nome')->get();

        return view('pages.posts.create', [
            'categorias' => $categorias,
        ]);
    }

    /**
     * Store a newly created post for the authenticated user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'categoria_ids' => ['nullable', 'array'],
            'categoria_ids.*' => ['exists:categorias,id'],
            'media' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,gif,mp4,mov,webm',
                'max:20480',
            ], // 20MB
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $data = [
            'content' => $validated['content'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'address' => $validated['endereco'] ?? null,
        ];

        if ($request->hasFile('media')) {
            $file = $request->file('media');

            $data['media_path'] = $file->store('posts', 'public');

            $data['media_type'] = str_starts_with(
                $file->getMimeType(),
                'video'
            ) ? 'video' : 'image';
        }

        $post = $request->user()->posts()->create($data);

        if (! empty($validated['categoria_ids'])) {
            $post->categorias()->attach($validated['categoria_ids']);
        }

        return redirect()
            ->route('posts.index')
            ->with('status', 'Postagem publicada com sucesso!');
    }

    /**
     * Show the form to edit a post.
     */
    public function edit(Request $request, Post $post): View
    {
        // Verifica se o usuário é o dono da publicação
        if ($post->user_id !== $request->user()->id) {
            abort(403);
        }

        // Verifica se a publicação já foi editada
        if ($post->edited_at !== null) {
            abort(403, 'Esta publicação já foi editada.');
        }

        return view('pages.posts.edit', [
            'post' => $post,
        ]);
    }

    /**
     * Update a post.
     */
    public function update(
        Request $request,
        Post $post
    ): RedirectResponse {
        // Verifica se o usuário é o dono da publicação
        if ($post->user_id !== $request->user()->id) {
            abort(403);
        }

        // Impede uma segunda edição
        if ($post->edited_at !== null) {
            return redirect()
                ->route('posts.index')
                ->with(
                    'error',
                    'Esta publicação já foi editada e não pode ser alterada novamente.'
                );
        }

        // Valida somente o texto
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        // Atualiza somente o texto e registra a data da edição
        $post->update([
            'content' => $validated['content'],
            'edited_at' => now(),
        ]);

        return redirect()
            ->route('posts.index')
            ->with(
                'status',
                'Publicação editada com sucesso!'
            );
    }

    /**
     * Toggle like on a post.
     */
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
}
