<?php

use App\Models\Menu;
use App\Models\Role;
use App\User;
use App\Models\Ability;
use Illuminate\Routing\Router;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class DatabaseSeeder extends Seeder
{
	/**
	 *
	 * @var Faker
	 */
	protected $faker;

    /**
     * @var Router
     */
	protected $router;

    /**
     * @param Faker  $faker
     */
	public function __constructor(Faker $faker)
	{
		$this->faker = $faker;
	}

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->createdAdminUser();
        $this->createAbilities();
        $this->createRole();
        $this->createMenu();
    }

    /**
     * 创建超级管理员
     */
    private function createdAdminUser()
    {
        User::truncate();
        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);
    }

    /**
     * 添加系统权限
     */
    private function createAbilities()
    {
        Ability::query()->delete();

        Ability::create(['name' => '*', 'title' => '所有权限', 'group' => '管理员']);

        $this->router = app(Router::class);

        if (empty($this->router->getRoutes())) {
            return;
        }

        $routes = collect($this->router->getRoutes())->map(function (\Illuminate\Routing\Route $route) {
            $name = $route->getName();
            if (!is_null($name) && strpos($name, 'passport') === false) {
                $tempTitle = explode('.', $name);
                $titles    = collect($tempTitle)->map(function ($t) {
                    return ucfirst($t);
                })->toArray();

                return ['name' => $name, 'title' => implode(' ', $titles), 'group' => ucfirst($tempTitle[0])];
            }

            return null;
        })->filter()->all();

        foreach ($routes as $route) {
            Ability::create($route);
        }

        Bouncer::allow(User::find(1))->to('*');
    }

    /**
     * add Role
     */
    private function createRole()
    {
        Role::query()->delete();
        $role = Role::create([
            'name' => 'admin',
            'title' => '系统管理员'
        ]);
        $abilities = Ability::query()
            ->select('name')
            ->where('name', '<>', '*')
            ->get()
            ->pluck('name')
            ->toArray();

        Bouncer::allow($role)->to($abilities);
    }

    /**
     * @author moell<moel91@foxmail.com>
     */
    private function createMenu()
    {
        Menu::truncate();
        Menu::insert([
            [
                'id'        => 1,
                'parent_id' => 0,
                'title'      => 'backend',
                'uri'       => 'backend',
                'icon'      => 'backend'
            ],
            [
                'id'        => 2,
                'parent_id' => 0,
                'title'      => 'frontend',
                'uri'       => 'frontend',
                'icon'      => 'frontend'
            ],
            [
                'id'        => 3,
                'parent_id' => 1,
                'title'      => 'Dashboard',
                'uri'       => '/dashboard',
                'icon'      => 'dashboard'
            ],
            [
                'id'        => 4,
                'parent_id' => 1,
                'title'      => 'UserManage',
                'uri'       => '/users',
                'icon'      => 'people'
            ],
            [
                'id'        => 5,
                'parent_id' => 4,
                'title'      => 'User',
                'uri'       => '/users/user',
                'icon'      => ''
            ],
            [
                'id'        => 6,
                'parent_id' => 4,
                'title'      => 'Permission',
                'uri'       => '/users/permission',
                'icon'      => ''
            ],
            [
                'id'        => 7,
                'parent_id' => 4,
                'title'      => 'Role',
                'uri'       => '/users/role',
                'icon'      => ''
            ],
            [
                'id'        => 8,
                'parent_id' => 4,
                'title'      => 'Menu',
                'uri'       => '/users/menu',
                'icon'      => ''
            ],
        ]);
    }
}
