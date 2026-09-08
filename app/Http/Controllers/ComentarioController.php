<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    public function store(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'texto' => ['required', 'string', 'max:500'],
        ]);

        $comentario = $post->comentarios()->create([
            'user_id' => $request->user()->id,
            'texto' => $validated['texto'],
        ]);

        $comentario->load('user');

        return response()->json([
            'id' => $comentario->id,
            'texto' => $comentario->texto,
            'user_name' => $comentario->user->name,
            'created_at' => $comentario->created_at->diffForHumans(),
        ]);
    }

    public function update(Request $request, Comentario $comentario): JsonResponse
    {
        abort_unless($comentario->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'texto' => ['required', 'string', 'max:500'],
        ]);

        $comentario->update($validated);

        return response()->json(['texto' => $comentario->texto]);
    }

    public function destroy(Request $request, Comentario $comentario): JsonResponse
    {
        abort_unless($comentario->user_id === $request->user()->id, 403);

        $comentario->delete();

        return response()->json(['deleted' => true]);
    }

    public function toggleLike(Request $request, Comentario $comentario): JsonResponse
    {
        $userId = $request->user()->id;
        $curtida = $comentario->curtidas()->where('user_id', $userId)->first();

        if ($curtida) {
            $curtida->delete();
            $curtido = false;
        } else {
            $comentario->curtidas()->create(['user_id' => $userId]);
            $curtido = true;
        }

        return response()->json([
            'curtido' => $curtido,
            'total' => $comentario->curtidas()->count(),
        ]);
    }
}