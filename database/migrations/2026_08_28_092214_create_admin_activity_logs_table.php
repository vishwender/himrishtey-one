<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_activity_logs', 'subject_type')) {
                $table->string('subject_type', 100)->nullable()->index()->after('action');
            }
            if (! Schema::hasColumn('admin_activity_logs', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->index()->after('subject_type');
            }
            if (! Schema::hasColumn('admin_activity_logs', 'member_id')) {
                $table->unsignedBigInteger('member_id')->nullable()->index()->after('subject_id');
            }
            if (! Schema::hasColumn('admin_activity_logs', 'metadata')) {
                $table->json('metadata')->nullable()->after('description');
            }
            if (! Schema::hasColumn('admin_activity_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->dropColumn([
                'subject_type',
                'subject_id',
                'member_id',
                'metadata',
                'user_agent',
            ]);
        });
    }
};
