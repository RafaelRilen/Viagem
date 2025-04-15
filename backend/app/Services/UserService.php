<?php

namespace App\Services;


use App\DTOs\UserCreateDTO;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;

class UserService
{

    public function __construct(private UserRepositoryInterface $repository)
    {
    }

    public function findByEmail(string $email): UserResource | null
    {
        $user = $this->repository->findByEmail($email);

        if ($user == false) {
            return $user;
        }

        return UserResource::make($user);
    }

    public function userCreate(UserCreateDTO $dto): User | null
    {

        try {
            $user =  User::create([
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => bcrypt($dto->password),
                'is_admin' => 0
            ]);

            return $user;
        } catch (QueryException $e) {
            logger()->error('Erro ao criar usuário: ' . $e->getMessage(), [
                'email' => $dto->email,
                'exception' => $e
            ]);
            return null;
        } catch (Exception $e) {
            logger()->error('Erro inesperado ao criar usuário: ' . $e->getMessage(), [
                'email' => $dto->email,
                'exception' => $e
            ]);
            return null;
        }
    }

}
