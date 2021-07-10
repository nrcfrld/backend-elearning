<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public static $model = User::class;

    public function updateProfile(User $user ,Request $request)
    {
        $request->validate([
            'image' => 'image|max:5120'
        ]);

        $filename = $request->photo->store('image', 'public');

        $user->update($request->all() + ['avatar' => $filename]);

        return $user;
    }
}
