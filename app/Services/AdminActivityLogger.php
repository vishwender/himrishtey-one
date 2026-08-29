<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;

class AdminActivityLogger
{
    public function log(
        string $action,
        ?string $description = null,
        ?string $module = null,
        ?int $memberId = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
        array $metadata = []
    ): ?AdminActivityLog {
        try {

            $admin = Auth::guard('admin')->user();

            return AdminActivityLog::create([
                'admin_id' => $admin?->id,
                'site_id' => session('admin_site_id'),

                'action' => $action,
                'module' => $module,

                'subject_type' => $subjectType,
                'subject_id' => $subjectId,

                'member_id' => $memberId,

                'description' => $description,

                'metadata' => ! empty($metadata)
                    ? $metadata
                    : null,

                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}
