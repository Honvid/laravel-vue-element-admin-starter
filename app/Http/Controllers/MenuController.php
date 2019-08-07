<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Traits\Authenticatable;
use Bouncer;

class MenuController extends Controller
{
    use Authenticatable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = Menu::query()->orderBy('priority', 'asc')->get();

        return $this->jsonResponse(new MenuResource($data));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $data = Menu::query()->where('show', true)->orderBy('priority', 'asc')->get();

        return $this->jsonResponse($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function my()
    {
        $menus = Menu::query()->where('show', true)
            ->orderBy('priority', 'asc')->get()
            ->filter(function ($item) {
                return !$item->permissions || Bouncer::can($item->permissions);
            });

        return $this->jsonResponse(new MenuResource($menus));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MenuRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MenuRequest $request)
    {
        $menu = Menu::create($request->all());

        return $this->jsonResponse($menu);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $menu = Menu::findOrFail($id);

        return $this->jsonResponse($menu);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MenuRequest $request
     * @param  int        $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MenuRequest $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update($request->all());

        return $this->jsonResponse($menu);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return $this->jsonResponse();
    }
}