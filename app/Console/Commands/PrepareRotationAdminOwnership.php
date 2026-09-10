<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\RotationAdminOwnership;
use App\Services\SiteDatabaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PrepareRotationAdminOwnership extends Command
{
    protected $signature = 'rotations:prepare-admin-ownership {--site=* : Site IDs; defaults to all active sites}';

    protected $description = 'Add central admin ownership to site rotations while preserving legacy users';

    public function handle(SiteDatabaseService $databases, RotationAdminOwnership $ownership): int
    {
        $sites = Site::query()->when($this->option('site'),
            fn ($query) => $query->whereIn('id', $this->option('site')),
            fn ($query) => $query->where('status', true)
        )->get();
        foreach ($sites as $site) {
            $databases->connect($site);
            $updated = $ownership->prepare(DB::connection('site'));
            $this->info("{$site->name}: ready; {$updated} legacy rotations matched to admin accounts.");
        }

        return self::SUCCESS;
    }
}
