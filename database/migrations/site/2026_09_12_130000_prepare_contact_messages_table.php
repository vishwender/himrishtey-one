<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contact_messages')) {
            Schema::create('contact_messages', function (Blueprint $table) {
                $table->id();
                $table->string('site_key')->nullable();
                $table->string('name');
                $table->string('email');
                $table->string('phone', 30)->nullable();
                $table->string('profile_id', 50)->nullable();
                $table->string('subject');
                $table->text('message');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('contact_messages', 'read_at')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // Preserve the inbox and existing messages, including the pre-existing base table.
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropIndex(['read_at']);
            $table->dropColumn('read_at');
        });
    }
};
