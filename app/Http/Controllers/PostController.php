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
            'attachments',
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
            'video' => ['nullable', 'file', 'mimes:mp4,mov,webm', 'max:102400'], // 100MB
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,gif', 'max:10240'], // 10MB cada
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $data = [
            'content' => $validated['content'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'address' => $validated['address'] ?? null,
        ];

        $post = $request->user()->posts()->create($data);

        if (! empty($validated['categoria_ids'])) {
            $post->categorias()->attach($validated['categoria_ids']);
        }

        if ($request->hasFile('video')) {
            $file = $request->file('video');

            $post->attachments()->create([
                'file_path' => $file->store('posts', 'public'),
                'file_type' => 'video',
                'file_size' => $file->getSize(),
            ]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imagem) {
                $post->attachments()->create([
                    'file_path' => $imagem->store('posts', 'public'),
                    'file_type' => 'image',
                    'file_size' => $imagem->getSize(),
                ]);
            }
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