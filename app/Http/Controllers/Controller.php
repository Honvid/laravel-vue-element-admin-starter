<?php

namespace App\Http\Controllers;

use Bouncer;
use App\Traits\JsonResponseTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, JsonResponseTrait;

    /**
     * Checks for a permission.
     *
     * @param string $permissionName
     *
     * @return void
     * @throws HttpResponseException
     */
    protected function checkPermission($permissionName)
    {
        if (Bouncer::cannot($permissionName)) {
            $this->jsonResponse(null, trans('errors.permissionJson'), 403, 403);
        }
    }
}
