<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /** Display a listing of the resource. */
    public function index(){
        
        $users = User::where('id', '!=', 1)
                ->with(['roles', 'permissions'])
                ->orderBy('id', 'desc')
                ->paginate(10);
        // dd($users);
        return view('backend.users.list', compact('users'));
    }

    public function create(){
        $data['roles'] = Role::all();
        $data['managers'] = User::role(['Admin', 'Hostel Warden', 'Mess Manager', 'Student'])->get();
        return view('backend.users.add', $data);
    }


    /** Store a newly created resource in storage. */
    public function store(Request $request){
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'roles' => 'array',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    /** Display the specified resource. */
    public function show(string $id)
    {
        //
    }

    public function edit(string $id){
        $user = User::findOrFail($id); 
        $roles = Role::all();
        $managers = User::role(['Admin', 'Hostel Warden', 'Mess Manager'])->get();
        return view('backend.users.edit', compact('user', 'roles', 'managers'));
    }

    public function update(Request $request, User $user){
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        
        if (isset($data['roles']) && Gate::denies('assignRole', $user)) {
            abort(403, "You don't have permission to assign roles to this user.");
        }

        // Update user data
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->manager_id = $data['manager_id'] ?? null;
        $user->save();

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**  Remove the specified resource from storage. */
    public function destroy(string $id)
    {
        //
    }

    public function permissionsEdit($id)
    {
        $user = User::with(['permissions', 'roles'])->findOrFail($id);

        $data['permissions'] = Permission::all();

        // Use all permissions instead of just direct ones
        $data['userPermissions'] = $user->getAllPermissions()->pluck('id')->toArray();

        $data['user'] = $user;
        dd($data);
        return view('backend.users.permissions', $data);
    }


    public function permissionsUpdate(Request $request, $id){
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        $user = User::findOrFail($id);
        $user->permissions()->sync($request->input('permissions', []));
        return redirect()->route('users.permissions.edit', $user->id)->with('success', 'Permissions updated successfully');
    }

}
