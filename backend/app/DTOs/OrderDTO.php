<?php

namespace App\DTOs;

use Carbon\Carbon;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class OrderDTO extends ValidatedDTO
{
    public string $requester_name;
    public string $destination;
    public Carbon $start_date;
    public Carbon $end_date;

    public function __construct(array $data)
    {
        $this->requester_name = $data['requester_name'];
        $this->destination = $data['destination'];
        $this->start_date = Carbon::parse($data['start_date']);
        $this->end_date = Carbon::parse($data['end_date']);
    }

    protected function rules(): array
    {
        return [
            'requester_name' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date_format:Y-m-d H:i:s', 'after_or_equal:today'],
            'end_date' => ['required', 'date_format:Y-m-d H:i:s', 'after:start_date'],
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date'
        ];
    }
}
