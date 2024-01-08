<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        /*Imprimir campo especifico dos dodos de entrada
        Nesse caso estou verificando o email que foi enviado via post
            *dd($request->all(['email']));*
        */

        try {
            /*
            Estou realizando uma validação pelo email e senha.
            O método attempt retorna um token quando a validação
            é realizada com sucesso
        */
            $token = auth('api')->attempt(  
    array(
                    'email'    => $request->email,
                    'password' => $request->password
                )); 
        } catch (\Throwable $th) {
            return response()->json(['Message' => $th->getMessage()], $th->getCode());
        }

        if(!$token) {
            /* 
                Nesse exemplo utilizei o 403
                Pois, o 401 está relacionado autorização
                já o 403 está relacionado a autenticação.
            */

            return response()->json(['Message' => 'Usuário ou senha inválido.'], 403);
        }

        /* 
            Retornando o response informando o token e o status da requisição.
             *return response($token, 201);*

            Nesse exemplo vou retornar um
        */
        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['msg' => 'logout realizado com sucesso'], 200);
    }

    public function refresh()
    {
        $tokenRefresh = auth('api')->refresh();

        return response()->json([
            'token' =>  $tokenRefresh
        ], 200);
    }

    public function me()
    {
        return response()->json(Auth::user(), 200);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 201);
    }
}
