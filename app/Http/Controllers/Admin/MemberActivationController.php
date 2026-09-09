<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\SiteMember;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MemberActivationController extends Controller
{
    private function authorizeActivation(): void
    {
        abort_unless(auth('admin')->user()?->hasAnyPermission([
            'manage-member-status',
            'edit-member',
            'edit-members',
        ]), 403);
    }

    private function ensureEligible(SiteMember $member): void
    {
        abort_if(in_array($member->active, ['Banned', 'deleted'], true), 403, 'This profile cannot be activated here.');
        abort_if($member->active === 'Yes', 409, 'This member is already active.');
    }

    public function create(int $id): View
    {
        $this->authorizeActivation();
        $member = SiteMember::findOrFail($id);
        $this->ensureEligible($member);
        $db = DB::connection('site');

        return view('admin.members.activate', [
            'member' => $member,
            'plans' => $db->table('membership_plans')->orderBy('plan_name')->get(),
            'staff' => Admin::query()->where('status', true)->whereNotNull('name')->where('name', '<>', '')->orderBy('name')->get(),
            'ranges' => $db->table('member_profile_range')->where('member_id', $id)->orderByRaw('CAST(range_from AS UNSIGNED)')->get(),
            'walletBalance' => $db->table('member_wallet')->where('member_id', $id)->latest('id')->value('wallet_balance') ?? 0,
        ]);
    }

    public function store(Request $request, int $id): RedirectResponse
    {
        $this->authorizeActivation();
        $this->ensureEligible(SiteMember::findOrFail($id));
        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'min:0'],
            'plan_activation_date' => ['required', 'date_format:Y-m-d'],
            'staff_id' => ['required', 'integer'],
            'profile_hide' => ['required', Rule::in(['Yes', 'No'])],
            'profile_view_count' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'wallet_amount' => ['required', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
            'ranges' => ['required', 'array', 'min:1', 'max:20'],
            'ranges.*.range_from' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'ranges.*.range_to' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'ranges.*.price' => ['required', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
        ]);
        $staff = Admin::query()->where('status', true)->whereNotNull('name')->where('name', '<>', '')->find($data['staff_id']);
        if (! $staff) {
            throw ValidationException::withMessages(['staff_id' => 'Select an active staff user.']);
        }
        $db = DB::connection('site');
        if ((int) $data['plan_id'] !== 0 && ! $db->table('membership_plans')->where('id', $data['plan_id'])->exists()) {
            throw ValidationException::withMessages(['plan_id' => 'Select a valid membership plan.']);
        }
        $ranges = collect($data['ranges'])->sortBy('range_from')->values();
        $previousEnd = 0;
        foreach ($ranges as $range) {
            if ($range['range_from'] > $range['range_to'] || $range['range_from'] <= $previousEnd) {
                throw ValidationException::withMessages(['ranges' => 'Ranges must have a valid start and end and must not overlap.']);
            }
            $previousEnd = (int) $range['range_to'];
        }

        $lockName = 'member-activation-'.sha1($db->getDatabaseName());
        $usesAdvisoryLock = in_array($db->getDriverName(), ['mysql', 'mariadb'], true);
        if ($usesAdvisoryLock) {
            abort_unless((int) $db->selectOne('SELECT GET_LOCK(?, 10) AS acquired', [$lockName])->acquired === 1, 409, 'Another activation is being saved. Please try again.');
        }
        try {
            $member = $db->transaction(function () use ($id, $db, $data, $ranges, $staff) {
                $member = SiteMember::query()->whereKey($id)->lockForUpdate()->firstOrFail();
                $this->ensureEligible($member);
                $number = (int) $member->activation_number;
                if (! $number || (string) $member->plan_activation_date !== $data['plan_activation_date']) {
                    $number = (int) $db->table('members')->max('activation_number') + 1;
                }
                $member->update([
                    'active' => 'Yes',
                    'activation_number' => $number,
                    'plan_id' => $data['plan_id'],
                    'plan_activation_date' => $data['plan_activation_date'],
                    'relationship_manager' => $staff->name,
                    'profile_hide' => $data['profile_hide'] === 'Yes' ? 'Yes' : '',
                    'profile_view_count' => $data['profile_view_count'],
                ]);
                $db->table('member_profile_range')->where('member_id', $id)->delete();
                $db->table('member_profile_range')->insert($ranges->map(fn ($range) => [
                    'member_id' => $id,
                    'range_from' => $range['range_from'],
                    'range_to' => $range['range_to'],
                    'price' => $range['price'],
                ])->all());

                if ((float) $data['wallet_amount'] > 0) {
                    $balance = $db->table('member_wallet')->where('member_id', $id)->latest('id')->lockForUpdate()->value('wallet_balance') ?? 0;
                    $db->table('member_wallet')->insert([
                        'member_id' => $id,
                        'wallet_balance' => number_format((float) $balance + (float) $data['wallet_amount'], 2, '.', ''),
                        'amount_added' => $data['wallet_amount'],
                        'amount_deducted' => '0',
                        'added_by' => auth('admin')->user()->name,
                        'created_at' => now(),
                        'update_at' => now(),
                    ]);
                }
                if (! $db->table('member_logs')->where('member_id', $id)->where('skey', 'Relationship Manager')->where('purpose', $staff->name)->exists()) {
                    $db->table('member_logs')->insert([
                        'member_id' => $id, 'skey' => 'Relationship Manager',
                        'purpose' => $staff->name, 'user' => $staff->name,
                        'assigned_date' => now()->format('Y-m-d H:i:s'),
                    ]);
                }

                return $member;
            });
        } finally {
            if ($usesAdvisoryLock) {
                $db->selectOne('SELECT RELEASE_LOCK(?)', [$lockName]);
            }
        }
        app(AdminActivityLogger::class)->log(
            action: 'member_activated', description: "Activated {$member->profile_id} and assigned {$staff->name}.",
            module: 'members', memberId: $id, subjectType: 'member', subjectId: $id,
            metadata: ['activation_number' => $member->activation_number, 'relationship_manager' => $staff->name, 'plan_id' => $data['plan_id'], 'wallet_amount' => $data['wallet_amount']],
        );

        return redirect()->route('admin.members.show', $id)->with('success', 'Member activated and activation settings saved successfully.');
    }
}
