<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Display permissions.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $permissions = Permission::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.permissions.index', compact(
            'permissions',
            'search'
        ));
    }

    /**
     * Store permission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:permissions,slug',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        if (Permission::where('slug', $slug)->exists()) {
            return back()
                ->withInput()
                ->withErrors([
                    'slug' => 'This permission slug already exists.',
                ]);
        }

        Permission::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Update permission.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('permissions', 'slug')
                    ->ignore($permission->id),
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        if (
            Permission::where('slug', $slug)
                ->where('id', '!=', $permission->id)
                ->exists()
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'slug' => 'This permission slug already exists.',
                ]);
        }

        $permission->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Delete permission.
     */
    public function destroy(Permission $permission)
    {
        /*
         * Do not allow deletion if this permission
         * is currently assigned to roles.
         */
        if ($permission->roles()->exists()) {
            return redirect()
                ->route('admin.permissions.index')
                ->with('error', 'This permission is assigned to one or more roles and cannot be deleted.');
        }

        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
