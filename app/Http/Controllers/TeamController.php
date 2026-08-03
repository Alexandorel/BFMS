<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    public function store(Request $request)
    {
        $adminCompanyIds = $request->user()->companies()->pluck('companies.id');

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'role'       => 'required|in:operator,contabil',
            'password'   => 'required|string|min:8|confirmed',
            'company_id' => ['required', Rule::in($adminCompanyIds)],
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => $data['role'],
        ]);

        $user->companies()->syncWithoutDetaching([$data['company_id']]);

        return redirect()->route('administrator.settings.team')
            ->with('status', "Cont creat pentru {$user->email}.");
    }

    private function authorizeCompanyAccess(Request $request, User $user): void
    {
        $sharesCompany = $user->companies()
            ->whereIn('companies.id', $request->user()->companies()->pluck('companies.id'))
            ->exists();

        abort_unless($sharesCompany, 403, 'Nu ai acces la acest cont.');
    }

    public function edit(Request $request, User $user)
    {
        $this->authorizeCompanyAccess($request, $user);
        $companies = $request->user()->companies;
        return view('administrator.team.edit', compact('user', 'companies'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeCompanyAccess($request, $user);
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role'       => 'required|in:operator,contabil',
            'companies'   => 'required|array|min:1',
            'companies.*' => 'exists:companies,id',
        ]);

        $user->update([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
        ]);

        $user->companies()->sync($data['companies']);

        return back()->with('status', 'Cont actualizat.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403, 'Nu îți poți revoca propriul acces.');
        $this->authorizeCompanyAccess($request, $user);
        $user->companies()->detach();
        return back()->with('status', 'Acces revocat.');
    }

    public function index(Request $request)
    {
        $companies = $request->user()->companies()->orderBy('name')->get();
        $company = $companies->firstWhere('id', session('active_company_id')) ?? $companies->first();

        $allUsers = $company->users()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($u) => [
                'id'         => $u->id,
                'name'       => trim("{$u->first_name} {$u->last_name}"),
                'email'      => $u->email,
                'role'       => $u->role,
                'initials'   => Str::of($u->first_name . ' ' . $u->last_name)
                    ->explode(' ')
                    ->map(fn ($p) => Str::substr($p, 0, 1))
                    ->join(''),
                'created_at' => $u->created_at->toISOString(),
            ]);

        return view('administrator.settings.team', [
            'user'      => $request->user(),
            'companies' => $companies,
            'company'   => $company,
            'allUsers'  => $allUsers,
        ]);
    }
}