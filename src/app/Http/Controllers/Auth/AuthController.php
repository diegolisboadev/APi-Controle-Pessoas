<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'registrar']]);
    }

    /**
     * Cadastrar um novo usuário
     *
     * @param Request $request
     * @return Response
     */
    public function registrar(Request $request) {

        $validatedData = Validator::make($request->only('name', 'email', 'password', 'password_confirmation'), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users|max:80',
            'password' => 'required|min:8|confirmed'
        ],
        [
            'name.required' => 'Ops! Informe um nome',
            'name.required' => 'Informe somente caracteres',
            'email.required' => 'Ops! Informe um email',
            'email.unique' => 'Email já cadastrado!',
            'email.email' => 'Ops! Tipo deve ser um email',
            'email.max' => 'Somente é permitido 80 caracteres',
            'password.required' => 'Ops! Informe uma senha',
            'password.min' => 'Informe no mínimo 8 caracteres',
            'password.confirmed' => 'Senha não Confere! Pf. Confirme a Senha!',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['status' => 501, 'mensagem' => $validatedData->errors()->first()], 501);
        }

        try {

            $usuario = new User();
            $usuario->name = $request->name;
            $usuario->email = $request->email;
            $usuario->password = Hash::make($request->password);
            $usuario->save();

            return response()->json([
                'status' => 201,
                'acao' => 'criação',
                'resultado' => 'sucesso'
            ], 201);

        } catch(Exception $e) {
            return response()->json([
                'status' => 500,
                'acao' => 'criacao',
                'resultado' => 'erro'
            ], 500);
        }
    }

    /**
     * Login na API
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request) {
        $validatedData = Validator::make($request->only(['email', 'password']), [
            'email' => 'required|max:80',
            'password' => 'required|min:8'
        ],
        [
            'email.required' => 'Ops! Informe um email',
            'email.max' => 'Somente é permitido 80 caracteres',
            'password.required' => 'Ops! Informe uma senha',
            'password.min' => 'Informe no mínimo 8 caracteres',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['status' => 501, 'mensagem' => $validatedData->errors()->first()], 501);
        }

        try {

            if(!$token = Auth::attempt($request->only(['email', 'password']))) {

                return response()->json([
                    'status' => 401,
                    'acao' => 'login',
                    'resultado' => 'Não Autorizado!'
                ], 401);
            }

            return response()->json([
                'status' => 200,
                'acao' => 'login',
                'resultado' => 'Login Realizado com Sucesso!',
                'token' => $token
            ], 200);

        } catch(Exception $e) {
            return response()->json([
                'status' => 500,
                'acao' => 'login',
                'resultado' => $e->getMessage()
            ], 500);
        }

    }
}
