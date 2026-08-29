<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Services\MemberPhotoService;
use App\Services\SiteDatabaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SharedProfileController extends Controller
{
    public function show(
        int $site,
        int $member,
        SiteDatabaseService $siteDatabase,
        MemberPhotoService $photoService
    ) {
        $siteModel = Site::query()
            ->whereKey($site)
            ->where('status', true)
            ->firstOrFail();

        $siteDatabase->connect($siteModel);

        $profile = DB::connection('site')
            ->table('members')
            ->where('id', $member)
            ->first();

        abort_unless($profile, 404, 'Profile not found.');

        $profile->photo_url = $photoService->url($profile->photo ?? null);
        $profile->age = $this->age($profile->birth_date_time ?? null);

        return view('shared-profile.show', [
            'profile' => $profile,
            'site' => $siteModel,
        ]);
    }

    private function age(?string $birthDate): ?int
    {
        if (! $birthDate) {
            return null;
        }

        try {
            return Carbon::parse($birthDate)->age;
        } catch (\Throwable) {
            return null;
        }
    }
}
