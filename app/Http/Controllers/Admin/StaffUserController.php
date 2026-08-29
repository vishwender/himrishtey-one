<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StaffUserController extends Controller
{
    /**
     * Display staff users.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $staffUsers = Admin::with(['roles', 'sites'])
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('profile_id', 'like', "%{$search}%")
                        ->orWhereHas('roles', function ($roleQuery) use ($search) {
                            $roleQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.staff-users.index', compact(
            'staffUsers',
            'search'
        ));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();

        $sites = Site::where('status', true)
            ->orderBy('name')
            ->get();

        return view('admin.staff-users.create', compact(
            'roles',
            'sites'
        ));
    }

    /**
     * Store staff user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:admins,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role_id' => [
                'required',
                'exists:roles,id',
            ],

            'sites' => [
                'nullable',
                'array',
            ],

            'sites.*' => [
                'integer',
                'exists:sites,id',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            $admin = Admin::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                // The column is currently non-nullable, so use a unique
                // temporary value until the auto-increment ID is available.
                'profile_id' => 'pending-'.Str::uuid(),
                'password' => $validated['password'],
                'status' => true,
            ]);

            $admin->profile_id = $this->generateProfileId($admin->id);
            $admin->save();

            /*
             * Assign role
             */
            $admin->roles()->sync([
                $validated['role_id'],
            ]);

            /*
             * Assign sites
             */
            $admin->sites()->sync(
                $validated['sites'] ?? []
            );
        });

        return redirect()
            ->route('admin.staff-users.index')
            ->with('success', 'Staff user created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Admin $admin)
    {
        $admin->load([
            'roles',
            'sites',
        ]);

        $roles = Role::orderBy('name')->get();

        $sites = Site::where('status', true)
            ->orderBy('name')
            ->get();

        return view('admin.staff-users.edit', compact(
            'admin',
            'roles',
            'sites'
        ));
    }

    /**
     * Update staff user.
     */
    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins', 'email')
                    ->ignore($admin->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'role_id' => [
                'required',
                'exists:roles,id',
            ],

            'sites' => [
                'nullable',
                'array',
            ],

            'sites.*' => [
                'integer',
                'exists:sites,id',
            ],
        ]);

        DB::transaction(function () use ($validated, $admin) {

            $admin->name = $validated['name'];
            $admin->email = $validated['email'];

            /*
             * Only change password if supplied.
             */
            if (! empty($validated['password'])) {
                $admin->password = Hash::make($validated['password']);
            }

            $admin->save();

            /*
             * Update role.
             */
            $admin->roles()->sync([
                $validated['role_id'],
            ]);

            /*
             * Update site access.
             */
            $admin->sites()->sync(
                $validated['sites'] ?? []
            );
        });

        return redirect()
            ->route('admin.staff-users.index')
            ->with('success', 'Staff user updated successfully.');
    }

    /**
     * Build a stable staff profile ID from the admins table auto-increment ID.
     */
    private function generateProfileId(int $adminId): string
    {
        return 'ADM'.str_pad((string) $adminId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Toggle staff user status.
     */
    public function toggleStatus(Admin $admin)
    {
        /*
    |--------------------------------------------------------------------------
    | Prevent disabling yourself
    |--------------------------------------------------------------------------
    */

        if (auth('admin')->id() === $admin->id) {
            return redirect()
                ->back()
                ->with('error', 'You cannot change your own account status.');
        }

        /*
    |--------------------------------------------------------------------------
    | Toggle status
    |--------------------------------------------------------------------------
    */

        $admin->status = ! $admin->status;
        $admin->save();

        $message = $admin->status
            ? 'Staff user activated successfully.'
            : 'Staff user deactivated successfully.';

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Delete staff user.
     */
    public function destroy(Admin $admin)
    {
        /*
         * Prevent accidental deletion of the currently
         * logged-in admin.
         */
        if (auth('admin')->id() === $admin->id) {
            return back()->with(
                'error',
                'You cannot delete your own account.'
            );
        }

        $admin->delete();

        return redirect()
            ->route('admin.staff-users.index')
            ->with('success', 'Staff user deleted successfully.');
    }
}
