<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // ensure migrations run
        $this->artisan('migrate', ['--database' => 'sqlite']);
        $this->app['config']->set('app.url', 'http://localhost');
    }

    public function test_first_admin_created_is_protected()
    {
        $controller = new UserController();
        $req = Request::create('/admin/users', 'POST', [
            'username' => 'firstadmin',
            'email' => 'first@admin.com',
            'full_name' => 'First Admin',
            'role' => 'admin',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        // call store directly, validation should pass
        $controller->store($req);

        $user = User::where('username', 'firstadmin')->first();
        $this->assertNotNull($user, 'User should have been created');
        $this->assertTrue($user->is_protected, 'Newly created first admin should be protected');
    }

    public function test_subsequent_admins_are_not_auto_protected()
    {
        // seed the initial admin so the next one is not "first"
        User::factory()->create(["role" => "admin", "is_protected" => true]);

        $controller = new UserController();
        $req = Request::create('/admin/users', 'POST', [
            'username' => 'secondadmin',
            'email' => 'second@admin.com',
            'full_name' => 'Second Admin',
            'role' => 'admin',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $controller->store($req);
        $user = User::where('username', 'secondadmin')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->is_protected, 'Additional admins should not be protected by default');
    }

    public function test_model_creating_observer_marks_first_admin_protected()
    {
        // directly create via model factory/instance without controller
        $admin = User::create([
            'username' => 'observeradmin',
            'email' => 'obs@admin.com',
            'full_name' => 'Observer Admin',
            'role' => 'admin',
            'password_hash' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->assertTrue($admin->is_protected, 'Model boot listener should protect the first admin');
    }

    public function test_cannot_delete_last_admin_even_if_unprotected()
    {
        // create a sole admin without protection flag
        $only = User::factory()->create(["role" => "admin", "is_protected" => false]);

        $controller = new UserController();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $controller->destroy($only);
    }

    public function test_cannot_deactivate_last_active_admin()
    {
        $only = User::factory()->create(["role" => "admin", "is_active" => true, "is_protected" => false]);

        $controller = new UserController();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $controller->toggle($only);
    }
}
