<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class ClientController extends Controller
{
    //Get the id of the company active in the current session. If no company is active -> uses the 1'st company of the user
    private function activeCompanyId(): int
    {
        $companyController = new CompanyController();
        $companies = $companyController->getUserCompanies();
        $activeCompanyId = Session::get('active_company_id');
        $company = $companies->firstWhere('id', $activeCompanyId) ?? $companies->first();

        return $company->id;
    }
    /**
     * //Display a listing of the resource.
     */
    public function index(): View
    {
        $companyController = new CompanyController();
        $companies = $companyController->getUserCompanies();
        $activeCompanyId = Session::get('active_company_id');
        $company = $companies->firstWhere('id', $activeCompanyId) ?? $companies->first();

        $clients = Client::where('company_id', $company->id)->get();

        return view('clients.index', compact('clients', 'companies', 'company'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cif' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $validated['company_id'] = $this->activeCompanyId();
        $validated['is_vat_exempt'] = $request->has('is_vat_exempt');

        Client::create($validated);

        return redirect()->route('clients.index')->with('status', 'Client adaugat cu succes.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cif' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $validated['is_vat_exempt'] = $request->has('is_vat_exempt');

        $client->update($validated);

        return redirect()->route('clients.index')->with('status', 'Client actualizat cu succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();
        return redirect()->route('clients.index')->with('status', 'Client sters cu succes.');
    }
}
