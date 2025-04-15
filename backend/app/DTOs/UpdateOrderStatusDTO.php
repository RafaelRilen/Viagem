<?php

namespace App\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class UpdateOrderStatusDTO extends ValidatedDTO
{
    public string $status;

    protected function rules(): array
    {
        return [
            'status' => ['required', 'in:solicitado,aprovado,cancelado'],
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [];
    }
}
