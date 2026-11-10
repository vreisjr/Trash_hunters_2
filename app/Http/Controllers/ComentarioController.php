<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Post;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ComentarioController extends Controller
{
    public function store(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'texto' => ['required', 'string', 'max:500'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('comentarios', 'id')->where(
                    fn ($query) => $query->where('post_id', $post->id)
                ),
            ],
        ]);

        $comentario = $post->comentarios()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'texto' => $validated['texto'],
        ]);

        $comentario->load('user');

        $recipients = collect([$post->user_id]);

        if ($comentario->parent_id !== null) {
            $recipients->push($comentario->pai()->value('user_id'));
        }

        User::query()
            ->whereIn('id', $recipients->filter()->unique())
            ->where('id', '!=', $request->user()->id)
            ->each(fn (User $user) => $user->notify(new SystemNotification(
                'comment',
                $request->user()->name.' comentou "'.Str::limit($comentario->texto, 120).'" na sua postagem.',
                route($post->post_type === 'marketplace' ? 'dashboard' : 'posts.index'),
            )));

        return response()->json([
            'id' => $comentario->id,
            'parent_id' => $comentario->parent_id,
            'texto' => $comentario->texto,
            'user_name' => $comentario->user->name,
            'user_is_moderator' => $comentario->user->isModerator(),
            'user_profile_photo_url' => $comentario->user->profilePhotoUrl(),
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

            if ($comentario->user_id !== $userId) {
                $comentario->user->notify(new SystemNotification(
                    'comment_like',
                    $request->user()->name.' curtiu seu comentário "'.Str::limit($comentario->texto, 120).'".',
                    route($comentario->post->post_type === 'marketplace' ? 'dashboard' : 'posts.index'),
                ));
            }
        }

        return response()->json([
            'curtido' => $curtido,
            'total' => $comentario->curtidas()->count(),
        ]);
    }
}
