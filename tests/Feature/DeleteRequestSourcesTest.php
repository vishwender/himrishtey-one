<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\DeleteProfileRequestController;
use App\Models\Admin;
use App\Models\Member;
use App\Models\Role;
use App\Services\AdminActivityLogger;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DeleteRequestSourcesTest extends TestCase
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

    private function seedRequests(): void
    {
        Schema::connection('site')->create('delete_profile_request', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('request_by')->nullable();
            $table->integer('status')->default(0);
            $table->string('date')->nullable();
            $table->string('reason')->nullable();
        });
        DB::connection('site')->table('members')->insert(['id' => 7, 'full_name' => 'Test Member']);
        DB::connection('site')->table('delete_profile_request')->insert([
            ['id' => 1, 'user_id' => 7, 'request_by' => 7, 'date' => '08-09-2026', 'status' => 0],
            ['id' => 2, 'user_id' => 7, 'request_by' => 0, 'date' => '2026-09-08 10:00:00', 'status' => 2],
            ['id' => 3, 'user_id' => 7, 'request_by' => 7, 'date' => '2026-09-08 11:00:00', 'status' => 0],
            ['id' => 4, 'user_id' => 8, 'request_by' => null, 'date' => null, 'status' => 0],
            ['id' => 5, 'user_id' => 9, 'request_by' => 5, 'date' => null, 'status' => 0],
        ]);
    }

    private function listing(string $route): array
    {
        $request = Request::create('/', 'GET', ['status' => 'all']);
        $request->setRouteResolver(fn () => (new Route('GET', '/', []))->name($route));

        return app(DeleteProfileRequestController::class)->index($request)->getData();
    }

    public function test_staff_and_member_pages_have_independent_latest_requests_counts_and_summaries(): void
    {
        $this->seedRequests();
        $staff = $this->listing('admin.members.delete-requests.index');
        $members = $this->listing('admin.delete-profile-requests.index');
        $this->assertSame([5, 1], $staff['requests']->pluck('id')->all());
        $this->assertSame([4, 3], $members['requests']->pluck('id')->all());
        $this->assertSame(1, $staff['requests']->last()->request_count);
        $this->assertSame(2, $members['requests']->last()->request_count);
        $this->assertSame(2, $staff['totalCount']);
        $this->assertSame(2, $members['pendingCount']);
        $this->assertSame(0, $members['rejectedCount']);
        $this->assertSame('admin.members.delete-requests.index', $staff['indexRoute']);
    }

    public function test_member_can_submit_despite_pending_staff_request_and_new_request_uses_zero(): void
    {
        $this->seedRequests();
        DB::connection('site')->table('delete_profile_request')->whereIn('id', [2, 3])->delete();
        $member = Member::findOrFail(7);
        $request = Request::create('/', 'POST', ['reason' => 'Please remove my profile']);
        $request->setUserResolver(fn () => $member);
        $controller = app(\App\Http\Controllers\Api\V1\DeleteProfileRequestController::class);
        $response = $controller->store($request);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertDatabaseHas('delete_profile_request', ['user_id' => 7, 'request_by' => 0, 'reason' => 'Please remove my profile'], 'site');
        $this->assertSame(409, $controller->store($request)->getStatusCode());
        $history = $controller->index($request)->getData(true)['data']['requests'];
        $this->assertCount(1, $history);
        $this->assertSame(0, $history[0]['request_by']);
    }

    public function test_staff_can_raise_request_when_member_has_already_submitted_one(): void
    {
        $this->seedRequests();
        DB::connection('site')->table('delete_profile_request')->where('id', 1)->delete();
        $admin = new Admin(['name' => 'Staff']);
        $admin->id = 7;
        $role = new Role(['slug' => 'super-admin']);
        $role->setRelation('permissions', collect());
        $admin->setRelation('roles', collect([$role]));
        $this->actingAs($admin, 'admin');
        $logger = $this->mock(AdminActivityLogger::class);
        $logger->shouldReceive('log')->once()->andReturnNull();
        $request = Request::create('/', 'POST', ['reason' => 'Staff raised request']);
        app(DeleteProfileRequestController::class)->store($request, 7, $logger);
        $this->assertDatabaseHas('delete_profile_request', ['user_id' => 7, 'request_by' => 7, 'reason' => 'Staff raised request'], 'site');
        $this->assertSame('Staff raised request', $this->listing('admin.members.delete-requests.index')['requests']->first()->reason);
    }

    public function test_relationship_manager_summary_and_list_only_include_assigned_members(): void
    {
        $this->seedRequests();
        DB::connection('site')->table('members')->where('id', 7)->update(['relationship_manager' => 'Manager']);
        $admin = new Admin(['name' => 'Manager']);
        $admin->setRelation('roles', collect([new Role(['slug' => 'relationship-manager'])]));
        $this->actingAs($admin, 'admin');
        $staff = $this->listing('admin.members.delete-requests.index');
        $this->assertSame([1], $staff['requests']->pluck('id')->all());
        $this->assertSame(1, $staff['totalCount']);
    }

    public function test_requester_names_fall_back_to_legacy_staff_without_overwriting_current_admins(): void
    {
        $this->seedRequests();
        Schema::connection('site')->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('display_name')->nullable();
            $table->string('name')->nullable();
        });
        DB::connection('site')->table('users')->insert([
            ['id' => 7, 'display_name' => 'Legacy Staff Name', 'name' => 'Legacy Login'],
            ['id' => 5, 'display_name' => 'Different Legacy Staff', 'name' => null],
        ]);
        DB::table('admins')->insert(['id' => 5, 'name' => 'Current Staff Name']);
        $data = $this->listing('admin.members.delete-requests.index');
        $this->assertSame('Legacy Staff Name', $data['admins'][7]->name);
        $this->assertSame('Current Staff Name', $data['admins'][5]->name);
        DB::connection('site')->table('users')->where('id', 7)->update(['display_name' => '']);
        $this->assertSame('Legacy Login', $this->listing('admin.members.delete-requests.index')['admins'][7]->name);
        $this->assertCount(0, $this->listing('admin.delete-profile-requests.index')['admins']);
    }
}
