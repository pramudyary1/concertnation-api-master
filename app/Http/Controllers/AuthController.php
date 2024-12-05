<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Mail\VerificationEmail;
use Illuminate\Auth\Events\Registered;



use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function register(Request $request){
        try{
            $data = $request->validate([
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|confirmed',
                'role' => 'required|string|'
            ]);
            $data['password'] = bcrypt($data['password']);
            $user = User::create($data);
            // \Mail::to($user->email)->send(new VerificationEmail($user));
            event(new Registered($user));

            return response([
                'message' => 'success',
                'data' => $user
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong'.$e,
            ],400);
        }

    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required',
            'password' => 'required',
            'role' => 'required',
        ]);
        $role = $validatedData['role'];
        unset($validatedData['role']);
        if (!Auth::attempt($validatedData)) {
            return response()->json(['message' => 'IInvalid credentials'], 401);
        }

        $user = Auth::user();

        if (!$user->email_verified_at) {
            return response()->json(['message' => 'Account not vverified'], 401);
        }

        if ($user->role !== "admin" && $user->role != $role) {
            return response()->json(['message' => 'invalid role'], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response([
            'message' => 'success',
            'data' => ['user' => $user, 'token' => $token]
        ],200);
    }
}
