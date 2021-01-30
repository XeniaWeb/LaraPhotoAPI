<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle an incoming registration request.
     * Registration new user for api.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     *  @throws \Illuminate\Validation\ValidationException
     */

    public function registerUserApi(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $validatedData['password'] = bcrypt($request->password);

        Auth::login($user = User::create($validatedData));

//        $accessToken = $user->createToken('authToken')->accessToken;

        event(new Registered($user));


        return response(['user' => $user], 201);
    }

    /**
     * Login user in api.
     *
     * @param Request $request
     * @return Response
     */

    public function loginApi(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }

    /**
     * Logout user from api.
     *
     * @param Request $request
     */

    public function logoutApi(Request $request)
    {
        $request->user()->token()->revoke();
    }


}
