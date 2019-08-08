<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
        return $this->menuTree($this->resource->toArray());
    }

    protected function menuTree(array $menus, int $parentId = 0)
    {
        $response = [];
        if (empty($menus)) {
            return $response;
        }

        $tmp = [];
        foreach ($menus as $menu) {
            $tmp[$menu['id']] = $menu;
        }

        foreach ($tmp as $menu) {
            if ($parentId == $menu['parent_id']) {
                $response[] = &$tmp[$menu['id']];
            } elseif (isset($tmp[$menu['parent_id']])) {
                $tmp[$menu['parent_id']]['children'][] = &$tmp[$menu['id']];
            }
        }

        return $response;
    }
}