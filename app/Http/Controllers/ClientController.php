<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Rules\RomanianCnpRule;
use App\Rules\RomanianCuiRule;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Database\QueryException;

class ClientController extends Controller
{

    private function activeCompanyId(): int
    {
        $companyController = new CompanyController();
        $companies = $companyController->getUserCompanies();
        $activeCompanyId = Session::get('active_company_id');
        $company = $companies->firstWhere('id', $activeCompanyId) ?? $companies->first();

        return $company->id;
    }

    public function index(): View
    {
        $companyController = new CompanyController();
        $companies = $companyController->getUserCompanies();
        $activeCompanyId = Session::get('active_company_id');
        $company = $companies->firstWhere('id', $activeCompanyId) ?? $companies->first();

        $clients = Client::where('company_id', $company->id)->get();

        return view('clients.index', compact('clients', 'companies', 'company'));
    }


    public function create(): View
    {
        return view('clients.create', ['client' => new Client()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateClient($request);

        return DB::transaction(function () use ($validated, $request) {
            $client = Client::create([
                'company_id' => $this->activeCompanyId(),
                'client_type' => $validated['client_type'],
                'name' => $validated['name'] ?? null,
                'cui' => $validated['cui'] ?? null,
                'trade_registry_number' => $validated['trade_registry_number'] ?? null,
                'vat_number' => $validated['vat_number'] ?? null,
                'first_name' => $validated['first_name'] ?? null,
                'last_name' => $validated['last_name'] ?? null,
                'cnp' => $validated['cnp'] ?? null,
                'county' => $validated['county'],
                'city' => $validated['city'],
                'address' => $validated['address'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
            ]);

            if ($request->boolean('add_contact') && $request->filled('contacts.0.name')) {
                $client->contacts()->create($validated['contacts'][0]);
            }

            return redirect()->route('clients.index')->with('status', 'Client adaugat cu succes.');
        });
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $this->validateClient($request);

        return DB::transaction(function () use ($validated, $request, $client) {
            $client->update([
                'client_type' => $validated['client_type'],
                'name' => $validated['name'] ?? null,
                'cui' => $validated['cui'] ?? null,
                'trade_registry_number' => $validated['trade_registry_number'] ?? null,
                'vat_number' => $validated['vat_number'] ?? null,
                'first_name' => $validated['first_name'] ?? null,
                'last_name' => $validated['last_name'] ?? null,
                'cnp' => $validated['cnp'] ?? null,
                'county' => $validated['county'],
                'city' => $validated['city'],
                'address' => $validated['address'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
            ]);

            if ($request->boolean('add_contact') && $request->filled('contacts.0.name')) {
                $client->contacts()->updateOrCreate(
                    ['id' => $client->contacts()->first()?->id],
                    $validated['contacts'][0]
                );
            } else {
                $client->contacts()->delete();
            }

            return redirect()->route('clients.index')->with('status', 'Client actualizat cu succes.');
        });
    }

    public function destroy(Client $client): RedirectResponse
    {
        try {
            $client->delete();
        } catch (QueryException $e) {
            // Codul 23000 = integrity constraint violation (FK RESTRICT)
            if ($e->getCode() === '23000') {
                return redirect()
                    ->route('clients.index')
                    ->with('error', 'Acest client nu poate fi șters deoarece are facturi asociate.');
            }

            throw $e; // orice altă eroare de DB rămâne vizibilă, nu o ascundem silențios
        }

        return redirect()->route('clients.index')->with('status', 'Client șters cu succes.');
    }


    private function validateClient(Request $request): array
    {
        return $request->validate([
            'client_type' => 'required|in:individual,company',

            'first_name' => 'required_if:client_type,individual|nullable|string|max:255',
            'last_name' => 'required_if:client_type,individual|nullable|string|max:255',
            // F-201: validare format CNP (13 cifre + cifra de control) doar pt persoane fizice
            'cnp' => [
                'nullable', 'string', 'max:20',
                Rule::when($request->input('client_type') === 'individual', [new RomanianCnpRule()]),
            ],

            'name' => 'required_if:client_type,company|nullable|string|max:255',
            // F-201: validare format CUI (prefix RO + cifra de control) doar pt persoane juridice
            'cui' => [
                'nullable', 'string', 'max:20', 'required_if:client_type,company',
                Rule::when($request->input('client_type') === 'company', [new RomanianCuiRule()]),
            ],
            'trade_registry_number' => 'nullable|string|max:20',
            'vat_number' => 'nullable|string|max:20',

            'county' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'delivery_address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',

            'add_contact' => 'nullable|boolean',
            'contacts' => 'nullable|array|max:1',
            'contacts.0.name' => 'required_if:add_contact,1|nullable|string|max:255',
            'contacts.0.role' => 'required_if:add_contact,1|nullable|string|max:100',
            'contacts.0.email' => 'required_if:add_contact,1|nullable|email|max:255',
            'contacts.0.phone' => 'required_if:add_contact,1|nullable|string|max:20',
        ]);
    }
}
