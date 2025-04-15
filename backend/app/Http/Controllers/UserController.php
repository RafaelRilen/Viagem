<?php

namespace App\Http\Controllers;

use App\DTOs\UserCreateDTO;
use App\DTOs\UserDTO;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function __construct(
        private UserService $userservice,
        private AuthService $authService
    ){}

    public function register(Request $request): JsonResponse
    {
        $dto = UserCreateDTO::fromRequest($request);

        $userExists = $this->userservice->findByEmail($dto->email);

        if ($userExists) {
            logger()->error('Erro ao cadastrar usuário: ' . 'Email já cadastrado para o usuário', [
                'email' => $dto->email,
                'user_id' => $userExists->id,
            ]);

            return response()->json([
                'message' => 'Email já cadastrado!',
            ], 422);
        }

        $user = $this->userservice->userCreate($dto);
        if (!$user) {
            return response()->json([
                'message' => 'Erro Interno, tente novamente mais tarde.',
            ], 500);
        }

        $token = $this->authService->createToken($user);
        if (!$token) {
            return response()->json([
                'message' => 'Erro Interno, tente novamente mais tarde.',
            ], 500);
        }

        return response()->json([
            'message' => 'Usuário cadastrado com sucesso.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ],
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ], 201);
    }


    public function login(UserDTO $dto): JsonResponse
    {
        $token = $this->authService->attemptLogin($dto);

        if (!$token) {
            return response()->json(['error' => 'Credenciais inválidas ou erro ao gerar token'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout(): JsonResponse
    {
        $logout = $this->authService->logout();

        if (!$logout) {
            return response()->json(['error' => 'Falha ao deslogar'], 500);
        }

        return response()->json(['message' => 'Deslogado com sucesso']);
    }


    protected function respondWithToken(string $token): JsonResponse
    {
        $user = $this->authService->getUserFromToken($token);

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado ou token inválido!',
            ], 400);
        }


        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }
}
