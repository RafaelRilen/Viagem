<?php

namespace App\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class UserDTO extends ValidatedDTO
{
    public string $name;
    public string $email;
    public string $password;
    public ?string $password_confirmation;

    protected function rules(): array
    {
        $rules = [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ];

        if ($this->isRegisterRequest()) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'][] = 'unique:users';
            $rules['password_confirmation'] = ['required', 'string', 'same:password'];
        }

        return $rules;
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [];
    }

    private function isRegisterRequest(): bool
    {
        return isset($this->name) && isset($this->password_confirmation);
    }

    public function credentials(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password
        ];
    }
}
