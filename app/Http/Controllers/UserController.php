<?php

namespace App\Http\Controllers;

use App\User;
use Bouncer;
use Illuminate\Http\Request;
use App\Traits\Authenticatable;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Http\Requests\PaginateRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{
    use Authenticatable;

    /**
     * Display a listing of the resource.
     *
     * @param PaginateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     */
    public function index(PaginateRequest $request)
    {
        $name     = $request->get('name', '');
        $page     = $request->get('page', 1);
        $pageSize = $request->get('pageSize', 20);
        $query    = User::with(['roles']);
        if (!empty($name)) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        $users = $query->paginate($pageSize, ['*'], 'page', $page);

        return $this->jsonResponse(new UserResource($users));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpResponseException
     */
    public function store(UserRequest $request)
    {
        $params = $request->all();
        if(!empty($params['avatar']) && strpos($params['avatar'], DIRECTORY_SEPARATOR) !== 0) {
            $base64 = explode(',',preg_replace("/\s/",'+', $params['avatar']));
            $img = base64_decode($base64[1]);
            $img_name = date('Y/m/d-His-') . uniqid() . '.png';
            $filename = Storage::disk('public')->put($img_name,$img);//上传
            if(!$filename) {
                return $this->jsonResponse(null, '上传失败', 500, 500);
            }
            $params['avatar'] = Storage::disk('public')->url($img_name);
        }

        if(!empty($params['password'])){
            $params['password'] = Hash::make($params['password']);
        }

        $user = User::create($params);
        Bouncer::sync($user)->roles($request->input('roles', []));

        return $this->jsonResponse();
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     */
    public function show($id)
    {
        $user = User::with(['roles', 'abilities'])->findOrFail($id);

        return $this->jsonResponse($user);
    }

    /**
     * Update User in storage.
     *
     * @param UserRequest $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws HttpResponseException
     */
    public function update(UserRequest $request, $id)
    {
        $params = $request->all();
        if(!empty($params['avatar']) && strpos($params['avatar'], DIRECTORY_SEPARATOR) !== 0) {
            $base64 = explode(',',preg_replace("/\s/",'+', $params['avatar']));
            $img = base64_decode($base64[1]);
            $img_name = date('Y/m/d-His-') . uniqid() . '.png';
            $filename = Storage::disk('public')->put($img_name,$img);//上传
            if(!$filename) {
                return $this->jsonResponse(null, '上传失败', 500, 500);
            }
            $params['avatar'] = Storage::disk('public')->url($img_name);
        }

        if(!empty($params['password'])){
            $params['password'] = Hash::make($params['password']);
        }

        $user = User::findOrFail($id);
        Bouncer::sync($user)->roles($request->input('roles', []));

        return $this->jsonResponse();
    }

    /**
     * Remove User from storage.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return $this->jsonResponse();
    }
}
