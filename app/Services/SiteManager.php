<?php

namespace App\Services;

use App\Models\Site;

class SiteManager
{
    /**
     * Get the currently selected site.
     */
    public function current(): ?Site
    {
        $siteId = session('admin_site_id');

        if (! $siteId) {
            return null;
        }

        return Site::find($siteId);
    }

    /**
     * Get the currently selected site ID.
     */
    public function id(): ?int
    {
        return session('admin_site_id');
    }

    /**
     * Check whether a site has been selected.
     */
    public function hasSite(): bool
    {
        return session()->has('admin_site_id');
    }

    /**
     * Set the selected site.
     */
    public function set(Site $site): void
    {
        session()->put('admin_site_id', $site->id);
    }

    /**
     * Clear selected site.
     */
    public function clear(): void
    {
        session()->forget('admin_site_id');
    }
}
