<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\AbilityRequest;
use App\Http\Requests\PaginateRequest;
use App\Models\Ability;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Traits\Authenticatable;
use App\Http\Controllers\Controller;

class AbilityController extends Controller
{
    use Authenticatable;

    /**
     * Display a listing of the abilities.
     *
     * @param PaginateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PaginateRequest $request)
    {
        $name     = $request->get('name', '');
        $page     = $request->get('page', 1);
        $pageSize = $request->get('pageSize', 20);

        $query = Ability::where('name', '<>', '*');
        if (!empty($name)) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        return $this->jsonResponse($query->paginate($pageSize, ['*'], 'page', $page));
    }

    /**
     * Store a newly created ability in storage.
     *
     * @param  AbilityRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AbilityRequest $request)
    {
        Ability::create($request->all());

        return $this->jsonResponse();
    }

    /**
     * Display the specified ability.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $ability = Ability::findOrFail($id);

        return $this->jsonResponse($ability);
    }

    /**
     * get all groups of the ability
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function groups()
    {
        $collection = Ability::select('group')->get()->unique('group');
        $filtered = $collection->filter(function ($value, $key) {
            return $value->group !== '管理员' && !is_null($value->group);
        });

        return $this->jsonResponse(Arr::flatten($filtered->toArray()));
    }

    /**
     * Update the specified ability in storage.
     *
     * @param  AbilityRequest $request
     * @param  int                      $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AbilityRequest $request, $id)
    {
        $ability = Ability::findOrFail($id);
        $ability->update($request->all());

        return $this->jsonResponse();
    }

    /**
     * Remove the specified ability from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $ability = Ability::findOrFail($id);
        $ability->delete();

        return response()->json([]);
    }
}
