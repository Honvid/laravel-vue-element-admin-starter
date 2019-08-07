<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\PaginateRequest;
use App\Http\Requests\RoleRequest;
use App\Traits\Authenticatable;
use Bouncer;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;

class RoleController extends Controller
{
    use Authenticatable;

    /**
     * Display a listing of the roles.
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
        $query    = Role::with('abilities');
        if (!empty($name)) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        $roles = $query->paginate($pageSize, ['*'], 'page', $page);

        return $this->jsonResponse($roles);
    }

    /**
     * Store a newly role in storage.
     *
     * @param RoleRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpResponseException
     */
    public function store(RoleRequest $request)
    {
        $role = Role::create($request->all());

        $role->allow($request->get('abilities'));

        return $this->jsonResponse();
    }

    /**
     * Display the specified role.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $role = Role::with('abilities')->findOrFail($id);

        return $this->jsonResponse($role);
    }

    /**
     * Update the specified role in storage.
     *
     * @param RoleRequest $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpResponseException
     */
    public function update(RoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update($request->all());
        Bouncer::sync($role)->abilities($request->get('abilities'));

        return $this->jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return $this->jsonResponse();
    }
}