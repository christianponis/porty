<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $nameParts = explode(' ', $this->name ?? '', 2);

        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'first_name'   => $nameParts[0] ?? '',
            'last_name'    => $nameParts[1] ?? '',
            'email'        => $this->email,
            'phone'        => $this->phone,
            'role'         => $this->role->value,
            'avatar'       => $this->avatar ? Storage::url($this->avatar) : null,
            'nodi_balance' => $this->nodiWallet?->balance ?? 0,
            'is_active'    => $this->is_active,
            'created_at'   => $this->created_at?->toISOString(),
        ];
    }
}
