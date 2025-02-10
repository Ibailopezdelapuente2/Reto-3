<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'dni' => 'required|string|unique:users',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::create([
            'name' => $request->get('name'),
            'lastname' => $request->get('lastname'),
            'email' => $request->get('email'),
            'dni' => $request->get('dni'),
            'password' => Hash::make($request->get('password')),
            'role' => 'user',
        ]);
        return response()->json(['message' => 'Usuario creado', 'data' => $user], 200);
    }
    public function show($id){
        $user = User::findOrFail($id);
        return response()->json(['message' => '', 'data' => $user], 200);
    }
    public function showAll(){
        $user = User::all();
        return response()->json(['message' => '', 'data' => $user], 200);
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|nullable',
            'dni' => 'sometimes|string|unique:users,dni,' . $user->id,
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user->name = $request->get('name', $user->name);
        $user->lastname = $request->get('lastname', $user->lastname);
        $user->email = $request->get('email', $user->email);
        $user->dni = $request->get('dni', $user->dni);
        if ($request->has('password') && $request->get('password') !== null) {
            $user->password = Hash::make($request->get('password'));
        }
        $user->save();
        return response()->json(['message' => 'Usuario actualizado', 'data' => $user], 200);
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado', 'data' => $user], 200);
    }
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'error' => 'Correo o contraseña errónea.',
            ], 401);
        }
        $user = Auth::user();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user,
        ]);
    }
}
