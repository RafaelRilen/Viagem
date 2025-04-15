<?php
namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): User | null
    {
        $fields = [
            'id',
            'name',
            'email',
            'is_admin'
        ];

        return User::on('mysql')
            ->select($fields)
            ->where('email', $email)
            ->first();

    }

}
