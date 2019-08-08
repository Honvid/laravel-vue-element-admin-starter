<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $response = $this->resource->toArray();
        $users = [];
        foreach ($response['data'] as &$value) {
            $user = [
                'id' => $value['id'],
                'name' => $value['name'],
                'email' => $value['email'],
                'mobile' => $value['mobile'],
                'avatar' => $value['avatar'],
                'roles' => [],
                'provider' => $value['provider'],
                'created_at' => $value['created_at'],
                'updated_at' => $value['updated_at'],
            ];
            foreach ($value['roles'] as $role) {
                $user['roles'][] = $role['name'];
            }
            $users[] = $user;
        }
        $response['data'] = $users;
        return $response;
    }
}