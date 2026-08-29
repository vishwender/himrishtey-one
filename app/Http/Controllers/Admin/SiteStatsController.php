<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SiteStatsService;
use Illuminate\Http\Request;

class SiteStatsController extends Controller
{
    public function index(Request $request, SiteStatsService $statsService)
    {
        $months = (int) $request->integer('months', 12);
        $months = in_array($months, [3, 6, 12, 24], true) ? $months : 12;

        return view('admin.site-stats.index', [
            'stats' => $statsService->statistics($months),
            'months' => $months,
        ]);
    }
}
