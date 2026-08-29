<?php

namespace App\Services;

use App\Models\Site;
use Illuminate\Support\Facades\Auth;

class CurrentSiteService
{
    public function get(): ?Site
    {
        $siteId = session('admin_site_id');

        if (! $siteId) {
            return null;
        }

        return Site::find($siteId);
    }

    public function set(Site $site): void
    {
        session([
            'admin_site_id' => $site->id,
        ]);
    }

    public function clear(): void
    {
        session()->forget('admin_site_id');
    }

    public function getForAdmin(): ?Site
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return null;
        }

        $site = $this->get();

        if (! $site) {
            return null;
        }

        if (! $admin->hasSiteAccess($site->id)) {
            $this->clear();

            return null;
        }

        return $site;
    }
}
