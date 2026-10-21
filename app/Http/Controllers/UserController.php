<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $moderator = $request->user();

        abort_unless($moderator->isModerator(), 403);
        abort_if($moderator->is($user) || $user->isModerator(), 403);

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->delete();

        return redirect()
            ->route('posts.index')
            ->with('status', 'Conta do usuário excluída com sucesso!');
    }
}
