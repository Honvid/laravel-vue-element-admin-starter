<?php

namespace App\Traits;

/*
|--------------------------------------------------------------------------
| TRAIT NAME: Authenticatable
|--------------------------------------------------------------------------
| @author    honvid
| @datetime  2019-06-18 16:37
| @package   App\Traits
| @description:
|
*/

use App\User;
use Bouncer;
use Route;

trait Authenticatable
{
    /**
     * Override of callAction to perform the authorization before
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function callAction($method, $parameters)
    {
        $ability = Route::currentRouteName();
        if (!is_null($ability) && Bouncer::cannot($ability)) {
            $this->jsonResponse(null, trans('errors.permissionJson'), 403, 403);
        }

        return parent::callAction($method, $parameters);
    }
}