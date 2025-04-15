<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JWTMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['message' => 'Usuário não encontrado'], 404);
            }

            $request->merge(['user' => $user]);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => 'Token expirado',
                'error' => $e->getMessage()
            ], 401);

        } catch (TokenInvalidException $e) {
            return response()->json([
                'message' => 'Token inválido',
                'error' => $e->getMessage()
            ], 401);

        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token não encontrado',
                'error' => $e->getMessage()
            ], 401);
        }

        return $next($request);
    }
}
