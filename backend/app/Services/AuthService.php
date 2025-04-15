<?php
namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{

    public function createToken(User $user): ?string
    {
        try {
            return JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            logger()->error('Erro ao gerar token JWT: ' . $e->getMessage());
            return null;
        }
    }

    public function attemptLogin(UserDTO $dto): ?string
    {
        $credentials = [
            'email' => $dto->email,
            'password' => $dto->password
        ];

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                logger()->warning('Tentativa de login falhou para o email: ' . $dto->email);
                return null;
            }

            return $token;

        } catch (JWTException $e) {
            logger()->error('Erro ao tentar autenticar o usuário: ' . $e->getMessage());
            return null;
        }
    }

    public function logout(): bool
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return true;
        } catch (JWTException $e) {
            logger()->error('Erro ao deslogar o usuário: ' . $e->getMessage());
            return false;
        }
    }

    public function getUserFromToken(string $token): ?User
    {
        try {
            $user = JWTAuth::setToken($token)->toUser();
            return $user;
        } catch (JWTException $e) {
            logger()->error('Erro ao obter usuário do token JWT: ' . $e->getMessage());
            return null;
        }
    }
}
