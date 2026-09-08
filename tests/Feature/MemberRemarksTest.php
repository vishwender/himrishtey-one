<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\MemberController;
use App\Services\AdminActivityLogger;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class MemberRemarksTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.connections.site' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]]);

        Schema::connection('site')->create('members', function (Blueprint $table) {
            $table->id();
            $table->string('profile_id')->nullable();
            $table->string('full_name')->nullable();
            $table->string('active')->nullable();
            $table->text('remarks')->nullable();
        });
    }

    public function test_saving_remark_updates_member_and_records_history(): void
    {
        DB::connection('site')->table('members')->insert([
            'id' => 7,
            'profile_id' => 'HIM10007',
            'full_name' => 'Test Member',
            'active' => 'Yes',
            'remarks' => 'Previous remark',
        ]);

        $logger = Mockery::mock(AdminActivityLogger::class);
        $logger->shouldReceive('log')
            ->once()
            ->withArgs(function (...$arguments): bool {
                $metadata = $arguments[6];

                return $arguments[0] === 'remarks_updated'
                    && $arguments[3] === 7
                    && $metadata['old_remarks'] === 'Previous remark'
                    && $metadata['new_remarks'] === 'Call again tomorrow'
                    && $metadata['remark_type'] === 'RM Remarks';
            })
            ->andReturnNull();

        $this->app->instance(AdminActivityLogger::class, $logger);

        app(MemberController::class)->updateRemarks(
            Request::create('/', 'POST', ['remarks' => 'Call again tomorrow']),
            7
        );

        $this->assertDatabaseHas('members', [
            'id' => 7,
            'remarks' => 'Call again tomorrow',
        ], 'site');
    }

    public function test_remark_is_required(): void
    {
        $this->expectException(ValidationException::class);

        app(MemberController::class)->updateRemarks(
            Request::create('/', 'POST', ['remarks' => '']),
            7
        );
    }
}
