<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

class RotationAdminOwnership
{
    public function prepare(Connection $connection): int
    {
        $schema = $connection->getSchemaBuilder();
        if (! $schema->hasTable('member_rotations')) {
            return 0;
        }
        if (! $schema->hasColumn('member_rotations', 'admin_id')) {
            $schema->table('member_rotations', function (Blueprint $table) {
                // Admins live in the central database, not the site's users table.
                $table->unsignedBigInteger('admin_id')->nullable()->index();
            });
        }

        if (! $schema->hasTable('users') || ! $schema->hasColumn('users', 'email')) {
            return 0;
        }

        $admins = Admin::query()->get(['id', 'email'])
            ->groupBy(fn ($admin) => strtolower(trim((string) $admin->email)));
        $users = $connection->table('users')->get(['id', 'email'])
            ->groupBy(fn ($user) => strtolower(trim((string) $user->email)));
        $updated = 0;
        foreach ($users as $email => $matches) {
            $central = $admins->get($email);
            // Ambiguous or missing identities must never be matched by numeric ID.
            if ($email === '' || $matches->count() !== 1 || ! $central || $central->count() !== 1) {
                continue;
            }
            $updated += $connection->table('member_rotations')
                ->whereNull('admin_id')->where('user_id', $matches->first()->id)
                ->update(['admin_id' => $central->first()->id]);
        }

        return $updated;
    }
}
