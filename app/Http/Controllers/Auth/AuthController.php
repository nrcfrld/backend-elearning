<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Specialtactics\L5Api\Http\Controllers\Features\JWTAuthenticationTrait;

class AuthController extends Controller
{
    use JWTAuthenticationTrait;

    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' =>  'required|confirmed',
        ]);

        $user = User::create(
            $request->all() +
            ['role_id' => Role::where('name', 'end-user')->first()->id]
        );

        $user->sendEmailVerificationNotification();

        return $this->response->created(null, $user);
    }
}
