<?php

namespace App\Http\Controllers\Auth;

/*
|--------------------------------------------------------------------------
| CLASS NAME: AuthenticationController
|--------------------------------------------------------------------------
| @author    honvid
| @datetime  2019-06-05 16:23
| @package   App\Http\Controllers\Auth 
| @description:
|
*/

use Hash;
use Log;
use App\User;
use Socialite;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\JsonResponseTrait;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthenticationController
{

    use AuthenticatesUsers, JsonResponseTrait;

    /**
     * 注册一个新用户
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string',
            'confirm'  => 'required|string'
        ]);

        $user = new User([
            'name'     => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->save();

        return response()->json($this->getToken($request, $user, 'Successfully created user!'), 201);
    }

    /**
     * 登录
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'username'    => 'required|string',
            'password'    => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if (!$this->attemptLogin($request)) {
            return $this->JsonResponse(null, "用户名或密码错误", 401);
        }

        return response()->json($this->getToken($request, $request->user()));
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        // 验证用户名登录方式
        $usernameLogin = $this->guard()->attempt([
            'name'     => $credentials['username'],
            'password' => $credentials['password']], $request->filled('remember')
        );
        if ($usernameLogin) {
            return true;
        }
        // 验证手机号登录方式
        $mobileLogin = $this->guard()->attempt([
            'mobile'   => $credentials['username'],
            'password' => $credentials['password']], $request->filled('remember')
        );
        if ($mobileLogin) {
            return true;
        }

        // 验证邮箱登录方式
        $emailLogin = $this->guard()->attempt([
            'email'    => $credentials['username'],
            'password' => $credentials['password']], $request->filled('remember')
        );
        if ($emailLogin) {
            return true;
        }

        return false;
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * 注销登录
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $abilities = $request->user()->abilities()->pluck('name')->all();
        $roles     = $request->user()->roles()->pluck('name')->all();
        $user      = $request->user()->toArray();
        unset($user['roles'], $user['abilities']);
        $user['permissions'] = array_merge($roles, $abilities);

        return response()->json($user);
    }

    /**
     * @param $provider
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        try {
            return Socialite::driver($provider)->redirect();
        } catch (\InvalidArgumentException $e) {
            return redirect('/');
        }
    }

    /**
     * @param $provider
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleProviderCallback($provider)
    {
        // 从第三方 OAuth 回调中获取用户信息
        $socialite = Socialite::driver($provider)->user();
        // 在本地 users 表中查询该用户来判断是否已存在
        $user = User::where('provider_id', '=', $socialite->getId())
            ->where('provider', '=', $provider)
            ->first();
        if ($user == null) {
            // 如果该用户不存在则将其保存到 users 表
            $newUser = new User();

            $newUser->name        = $socialite->getName();
            $newUser->email       = $socialite->getEmail() == '' ? '' : $socialite->getEmail();
            $newUser->avatar      = $socialite->getAvatar();
            $defaultPassword      = Str::random(6);
            $newUser->password    = Hash::make($defaultPassword);
            $newUser->provider    = $provider;
            $newUser->provider_id = $socialite->getId();

            $newUser->save();
            Log::info("user {$newUser->name}'s password is {$defaultPassword}.");
            $user = $newUser;
        } else {
            $user->email  = $socialite->getEmail() == '' ? $user->email : $socialite->getEmail();
            $user->avatar = $socialite->getAvatar();
            $user->save();
        }

        // 手动登录该用户

        $tokenResult       = $user->createToken('Personal Access Token');
        $token             = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        Log::info($tokenResult->accessToken);


        // 登录成功后将用户重定向到首页
        return redirect('/')->withCookie('Admin-Token', $tokenResult->accessToken, 60 * 24 * 7, null, null, false, false);
    }

    /**
     * @param        $request
     * @param User   $user
     * @param string $message
     *
     * @return array
     */
    private function getToken($request, $user, $message = 'Login success.')
    {
        $tokenResult = $user->createToken('Personal Access Token');
        $token       = $tokenResult->token;

        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);

        $token->save();

        $abilities = $user->abilities()->pluck('name')->all();
        $roles     = $user->roles()->pluck('name')->all();
        $user      = $user->toArray();
        unset($user['roles'], $user['abilities']);
        $user['permissions'] = array_merge($roles, $abilities);
        $user['message'] = $message;
        //$user['avatar']       = 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif';
        $user['access_token'] = $tokenResult->accessToken;
        $user['token_type']   = 'Bearer';
        $user['expires_at']   = Carbon::parse(
            $tokenResult->token->expires_at
        )->toDateTimeString();

        return $user;
    }
}