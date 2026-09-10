<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\MemberController;
use App\Models\Admin;
use App\Models\Role;
use App\Services\AdminActivityLogger;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BannedMembersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.site' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        Schema::connection('site')->create('members', function (Blueprint $table) {
            $table->id();
            foreach (['active', 'gender', 'full_name', 'profile_id', 'mobile_number', 'email', 'assigned_to', 'relationship_manager'] as $column) {
                $table->string($column)->nullable();
            }
            $table->integer('plan_id')->nullable();
        });
        Schema::connection('site')->create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name');
        });
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('profile_id')->nullable();
        });
    }

    private function listing(array $filters = [], string $route = 'admin.members.banned'): LengthAwarePaginator
    {
        $request = Request::create('/admin/members/banned', 'GET', $filters);
        $request->setRouteResolver(fn () => (new Route('GET', '/admin/members/banned', []))->name($route));

        return app(MemberController::class)->index($request)->getData()['members'];
    }

    public function test_banned_listing_searches_sorts_and_paginates_only_banned_members(): void
    {
        for ($id = 1; $id <= 12; $id++) {
            DB::connection('site')->table('members')->insert(['id' => $id, 'active' => 'Banned', 'full_name' => 'Match', 'gender' => 'Male']);
        }
        DB::connection('site')->table('members')->insert(['id' => 13, 'active' => 'Yes', 'full_name' => 'Match']);
        $members = $this->listing(['search' => 'Match', 'gender' => 'Male', 'per_page' => 10]);
        $this->assertSame(12, $members->total());
        $this->assertSame(10, $members->count());
        $this->assertSame(12, $members->first()->id);
        $this->assertSame(0, $this->listing(['gender' => 'Female'])->total());
        $this->assertSame(25, $this->listing(['per_page' => 999])->perPage());
    }

    public function test_not_banned_filter_includes_blank_and_inactive_statuses(): void
    {
        foreach (['Banned', 'Yes', 'No', '', null] as $index => $status) {
            DB::connection('site')->table('members')->insert(['id' => $index + 1, 'active' => $status]);
        }
        $this->assertSame([5, 4, 3, 2], $this->listing(['banned' => 'no'], 'admin.members.index')->pluck('id')->all());
    }

    public function test_relationship_manager_only_sees_assigned_banned_members(): void
    {
        $admin = new Admin(['name' => 'Assigned Manager']);
        $admin->setRelation('roles', collect([new Role(['slug' => 'relationship-manager'])]));
        $this->actingAs($admin, 'admin');
        DB::connection('site')->table('members')->insert([
            ['id' => 1, 'active' => 'Banned', 'relationship_manager' => 'Assigned Manager'],
            ['id' => 2, 'active' => 'Banned', 'relationship_manager' => 'Other Manager'],
        ]);
        $this->assertSame([1], $this->listing()->pluck('id')->all());
    }

    #[DataProvider('banRoles')]
    public function test_administrator_can_ban_and_unban_a_member(string $role): void
    {
        $admin = new Admin(['name' => 'Admin']);
        $admin->setRelation('roles', collect([new Role(['slug' => $role])]));
        $this->actingAs($admin, 'admin')->withoutMiddleware();
        DB::connection('site')->table('members')->insert(['id' => 1, 'active' => 'Yes']);
        $this->mock(AdminActivityLogger::class)->shouldReceive('log')->twice()->andReturnNull();
        $this->post(route('admin.members.ban.update', 1), ['banned' => 1])->assertRedirect();
        $this->assertDatabaseHas('members', ['id' => 1, 'active' => 'Banned'], 'site');
        $this->post(route('admin.members.ban.update', 1), ['banned' => 0])->assertRedirect();
        $this->assertDatabaseHas('members', ['id' => 1, 'active' => 'Yes'], 'site');
    }

    public static function banRoles(): array
    {
        return [['super-admin'], ['member-manager']];
    }

    public function test_non_administrator_cannot_ban_or_bypass_unban_control(): void
    {
        $admin = new Admin(['name' => 'Staff']);
        $admin->setRelation('roles', collect());
        $this->actingAs($admin, 'admin')->withoutMiddleware();
        DB::connection('site')->table('members')->insert(['id' => 1, 'active' => 'Banned']);
        $this->post(route('admin.members.ban.update', 1), ['banned' => 0])->assertForbidden();
        $this->post(route('admin.members.toggle-status', 1))->assertForbidden();
        $this->assertDatabaseHas('members', ['id' => 1, 'active' => 'Banned'], 'site');
    }

    public function test_ban_request_rejects_invalid_status_and_missing_member(): void
    {
        $admin = new Admin(['name' => 'Admin']);
        $admin->setRelation('roles', collect([new Role(['slug' => 'super-admin'])]));
        $this->actingAs($admin, 'admin')->withoutMiddleware();
        $this->postJson(route('admin.members.ban.update', 1), ['banned' => 'invalid'])->assertUnprocessable()->assertJsonValidationErrors('banned');
        $this->postJson(route('admin.members.ban.update', 999), ['banned' => 1])->assertNotFound();
    }
}
