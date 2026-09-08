<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Post;
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
        $posts = Post::with(['user', 'categoria', 'comentarios.user', 'comentarios.curtidas'])->latest()->get();
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

        return view('pages.posts.create', ['categorias' => $categorias]);
    }

    /**
     * Store a newly created post for the authenticated user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,mp4,mov,webm', 'max:20480'], // 20MB
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'endereco' => ['nullable', 'string', 'max:255'],
        ]);

        $data = [
            'content' => $validated['content'],
            'categoria_id' => $validated['categoria_id'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'endereco' => $validated['endereco'] ?? null,
        ];

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $data['media_path'] = $file->store('posts', 'public');
            $data['media_type'] = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
        }

        $request->user()->posts()->create($data);

        return redirect()->route('posts.index')->with('status', 'Postagem publicada com sucesso!');
    }
}