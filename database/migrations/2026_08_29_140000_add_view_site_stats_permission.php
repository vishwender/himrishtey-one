<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')->updateOrInsert(
            ['slug' => 'view-site-stats'],
            [
                'name' => 'View Site Stats',
                'description' => 'View site analytics and business statistics',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('permissions')->where('slug', 'view-site-stats')->delete();
    }
};
