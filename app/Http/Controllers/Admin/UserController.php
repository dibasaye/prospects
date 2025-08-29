<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:administrateur,responsable_commercial,commercial,caissier',
        ]);

        $user->role = $request->role;
        $user->save();

        return back()->with('success', 'Rôle mis à jour avec succès.');
    }

    public function toggleActive(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'Statut de l’utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Vous ne pouvez pas vous supprimer vous-même.');
        }

        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }
    public function create()
{
    return view('admin.users.create');
}

public function store(Request $request)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'role' => 'required|in:administrateur,responsable_commercial,commercial,caissier',
    ]);

    $validated['password'] = bcrypt($validated['password']);
    $validated['is_active'] = true;

    User::create($validated);

    return redirect()->route('admin.users.index')->with('success', 'Utilisateur ajouté avec succès.');
}


}
