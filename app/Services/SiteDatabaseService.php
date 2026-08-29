<?php

namespace App\Services;

use App\Models\Site;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SiteDatabaseService
{
    /**
     * Connect to the selected site's database.
     */
    public function connect(Site $site): void
    {
        $connection = [

            'driver' => 'mysql',

            'host' => $site->database_host,

            'port' => $site->database_port,

            'database' => $site->database_name,

            'username' => $site->database_username,

            'password' => $site->database_password,

            'unix_socket' => '',

            'charset' => 'utf8mb4',

            'collation' => 'utf8mb4_unicode_ci',

            'prefix' => '',

            'prefix_indexes' => true,

            'strict' => true,

            'engine' => null,

        ];

        Config::set('database.connections.site', $connection);
        Config::set('database.connections.application', $connection);

        /*
        |--------------------------------------------------------------------------
        | Remove existing connection
        |--------------------------------------------------------------------------
        */

        DB::purge('site');
        DB::purge('application');

        /*
        |--------------------------------------------------------------------------
        | Create fresh connection
        |--------------------------------------------------------------------------
        */

        DB::reconnect('site');
        DB::reconnect('application');
    }

    /**
     * Test whether the selected site's database is accessible.
     */
    public function testConnection(Site $site): bool
    {
        try {

            $this->connect($site);

            DB::connection('site')->getPdo();

            return true;
        } catch (\Throwable $e) {

            Log::error('Site database connection failed.', [
                'site_id' => $site->id,
                'site_name' => $site->name,
                'database' => $site->database_name,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Disconnect the site database.
     */
    public function disconnect(): void
    {
        DB::disconnect('site');

        DB::purge('site');
    }
}
