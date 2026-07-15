<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'two_factor_enabled' => ! is_null($this->two_factor_secret),
            // 'roles' => $this->whenLoaded('roles', function () {
            //     return $this->roles->map(function ($role) {
            //         return [
            //             'id' => $role->id,
            //             'name' => $role->name,
            //             'permissions' => $this->whenLoaded('permissions',
            //                 $role->permissions->pluck('name')
            //             ),
            //         ];
            //     });
            // }),
            // 'permissions' => $this->whenLoaded('roles', function () {
            //     return $this->getAllPermissions()->pluck('name');
            // }),
            'orders' => $this->whenLoaded('orders'),
            'subscriptions' => $this->whenLoaded('subscriptions'),
            'transactions' => $this->whenLoaded('transactions'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
