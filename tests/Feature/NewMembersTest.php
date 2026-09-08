<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\MemberController;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NewMembersTest extends TestCase
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

    public function test_new_members_exclude_deactivated_and_active_profiles_and_filter_gender(): void
    {
        DB::connection('site')->table('members')->insert([
            ['id' => 1, 'active' => '', 'gender' => 'Male'],
            ['id' => 2, 'active' => null, 'gender' => 'Female'],
            ['id' => 3, 'active' => 'No', 'gender' => 'Male'],
            ['id' => 4, 'active' => 'Yes', 'gender' => 'Male'],
        ]);
        $request = Request::create('/admin/members/new', 'GET', ['gender' => 'Male', 'per_page' => 10]);
        $request->setRouteResolver(fn () => (new Route('GET', '/admin/members/new', []))->name('admin.members.new'));
        $members = app(MemberController::class)->index($request)->getData()['members'];
        $this->assertSame([1], $members->pluck('id')->all());
        $this->assertSame(10, $members->perPage());
    }

    public function test_new_members_search_and_pagination_preserve_filters(): void
    {
        for ($id = 1; $id <= 12; $id++) {
            DB::connection('site')->table('members')->insert(['id' => $id, 'active' => '', 'full_name' => 'Match', 'assigned_to' => 'Staff']);
        }
        DB::connection('site')->table('members')->insert(['id' => 13, 'active' => '', 'full_name' => 'Other']);
        $request = Request::create('/admin/members/new', 'GET', ['search' => 'Match', 'assigned_to' => 'Staff', 'per_page' => 10]);
        $request->setRouteResolver(fn () => (new Route('GET', '/admin/members/new', []))->name('admin.members.new'));
        $members = app(MemberController::class)->index($request)->getData()['members'];
        $this->assertSame(12, $members->total());
        $this->assertSame(10, $members->count());
        $this->assertSame(12, $members->first()->id);
    }

    public function test_assignment_updates_both_staff_fields_and_rejects_stale_selection(): void
    {
        Schema::table('admins', fn (Blueprint $table) => $table->boolean('status')->default(true));
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
        });
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->integer('admin_id');
            $table->integer('role_id');
        });
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->boolean('status');
        });
        DB::table('admins')->insert(['id' => 1, 'name' => 'Assigned Staff', 'status' => true]);
        DB::table('roles')->insert(['id' => 1, 'slug' => 'super-admin']);
        DB::table('admin_roles')->insert(['admin_id' => 1, 'role_id' => 1]);
        DB::table('sites')->insert(['id' => 1, 'status' => true]);
        DB::connection('site')->table('members')->insert([
            ['id' => 1, 'active' => ''], ['id' => 2, 'active' => 'Yes'],
        ]);
        $admin = new Admin(['name' => 'Admin']);
        $role = new Role(['slug' => 'super-admin']);
        $role->setRelation('permissions', collect());
        $admin->setRelation('roles', collect([$role]));
        $this->actingAs($admin, 'admin')->withSession(['admin_site_id' => 1]);
        \Illuminate\Support\Facades\Route::post('/test-new-member-assignment', [MemberController::class, 'assignNewMembers']);
        $this->post('/test-new-member-assignment', ['member_ids' => [1, 2], 'staff_id' => 1])->assertUnprocessable();
        $this->assertDatabaseHas('members', ['id' => 1, 'assigned_to' => null], 'site');
        $this->post('/test-new-member-assignment', ['member_ids' => [1], 'staff_id' => 1])->assertRedirect();
        $this->assertDatabaseHas('members', ['id' => 1, 'assigned_to' => 'Assigned Staff', 'relationship_manager' => 'Assigned Staff'], 'site');
        DB::table('admins')->where('id', 1)->update(['status' => false]);
        $this->post('/test-new-member-assignment', ['member_ids' => [1], 'staff_id' => 1])->assertUnprocessable();
    }

    public function test_dropdown_and_assignment_include_active_staff_without_site_assignments(): void
    {
        Schema::table('admins', fn (Blueprint $table) => $table->boolean('status')->default(true));
        DB::table('admins')->insert([
            ['id' => 1, 'name' => 'Super Admin', 'status' => true],
            ['id' => 2, 'name' => 'Staff Member', 'status' => true],
            ['id' => 3, 'name' => 'Disabled Staff', 'status' => false],
        ]);
        $admin = new Admin(['name' => 'Super Admin']);
        $role = new Role(['slug' => 'super-admin']);
        $role->setRelation('permissions', collect());
        $admin->setRelation('roles', collect([$role]));
        $this->actingAs($admin, 'admin')->withSession(['admin_site_id' => 1]);

        $request = Request::create('/admin/members/new', 'GET');
        $request->setRouteResolver(fn () => (new Route('GET', '/admin/members/new', []))->name('admin.members.new'));
        $staff = app(MemberController::class)->index($request)->getData()['assignmentStaff'];
        $this->assertSame(['Staff Member', 'Super Admin'], $staff->pluck('name')->all());

        DB::connection('site')->table('members')->insert(['id' => 1, 'active' => '']);
        \Illuminate\Support\Facades\Route::post('/test-new-member-assignment', [MemberController::class, 'assignNewMembers']);
        $this->post('/test-new-member-assignment', ['member_ids' => [1], 'staff_id' => 2])->assertRedirect();
        $this->assertDatabaseHas('members', ['id' => 1, 'assigned_to' => 'Staff Member', 'relationship_manager' => 'Staff Member'], 'site');
    }

    public function test_staff_assignment_rejects_unprivileged_admin(): void
    {
        $admin = new Admin(['name' => 'Staff']);
        $admin->setRelation('roles', collect());
        $this->actingAs($admin, 'admin');
        \Illuminate\Support\Facades\Route::post('/test-new-member-assignment', [MemberController::class, 'assignNewMembers']);
        $this->post('/test-new-member-assignment', ['member_ids' => [1], 'staff_id' => 1])->assertForbidden();
    }

    public function test_assignment_requires_member_selection(): void
    {
        $admin = new Admin(['name' => 'Admin']);
        $role = new Role(['slug' => 'super-admin']);
        $role->setRelation('permissions', collect());
        $admin->setRelation('roles', collect([$role]));
        $this->actingAs($admin, 'admin');
        \Illuminate\Support\Facades\Route::post('/test-new-member-assignment', [MemberController::class, 'assignNewMembers']);
        $this->postJson('/test-new-member-assignment', [])->assertUnprocessable()->assertJsonValidationErrors(['member_ids', 'staff_id']);
    }
}
