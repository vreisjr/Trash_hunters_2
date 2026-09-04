<?php

namespace App\Http\Controllers;

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
        $posts = Post::with('user')->latest()->get();

        return view('pages.posts.index', ['posts' => $posts]);
    }

    /**
     * Show the form to create a new post.
     */
    public function create(): View
    {
        return view('pages.posts.create');
    }

    /**
     * Store a newly created post for the authenticated user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $request->user()->posts()->create($validated);

        return redirect()->route('posts.index')->with('status', 'Post publicado com sucesso!');
    }
}
