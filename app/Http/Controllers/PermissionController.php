<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class PermissionController extends Controller
{
    
    public function index()
    {
        $data['permissions'] = Permission::paginate(10);
        return view('backend.permissions.list', $data);
    }

    public function create()
    {
        $data['roles'] = Role::all();
        return view('backend.permissions.add', $data);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:permissions,name',
            'roles' => 'array'
        ]);

        $permission = Permission::create(['name' => $data['name']]);

        if (!empty($data['roles'])) {
            foreach ($data['roles'] as $roleId) {
                $role = Role::findById($roleId);
                $role->givePermissionTo($permission);
            }
        }

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created and assigned.');
    }

    public function edit(Permission $permission) {
        $data = [
            'permission' => $permission,
            'roles' => Role::all(),
            'permissions' => Permission::paginate(10),
        ];

        return view('backend.permissions.edit', $data);
    }


    // Handle the update request
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
            'roles' => 'array'
        ]);

        $permission->name = $data['name'];
        $permission->save();

        // Sync roles with this permission
        if (!empty($data['roles'])) {
            foreach (Role::all() as $role) {
                if (in_array($role->id, $data['roles'])) {
                    $role->givePermissionTo($permission);
                } else {
                    $role->revokePermissionTo($permission);
                }
            }
        }

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->back()->with('success', 'Permission deleted.');
    }

    public function role_list(){
        $data['roles'] = Role::all();
        return view('backend.roles.list', $data);
    }




}
