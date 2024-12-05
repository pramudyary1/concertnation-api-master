<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try{
            $data = User::where('role' , '<>', 'admin')->get();
            return response( [
                'message' => 'success',
                'data' => $data
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong',
            ],400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try{
            $data = $request->validate([
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|confirmed'
            ]);

            $data['password'] = bcrypt($data['password']);
            $data['role'] = 'user';

            $data = User::create($data);

            return response( [
                'message' => 'success',
                'data' => $data
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong'.$e->getMessage(),
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try{
            $data = User::find($id);
            return response( [
                'message' => 'success',
                'data' => $data
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong',
            ],400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try{
            $data = $request->validate([
                'last_name' => 'required|string',
                'first_name' => 'required|string',
                'phone' => 'required|string',

            ]);

            $data = User::where('id', $id)->update($data);
            return response( [
                'message' => 'success',
                'data' => User::find($id)
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong'.$e,
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try{
            $data = User::where('id', $id)->delete();
            return response( [
                'message' => 'success',
                'data' => $data
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong',
            ],400);
        }
    }
}
