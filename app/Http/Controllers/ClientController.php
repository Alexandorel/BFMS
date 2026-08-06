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

    /**
     * Method to verify client. (DoD#3)
     */
    private function authorizeClient(Client $client): void
    {
        abort_unless(
            $client->company_id === $this->activeCompanyId(),
            403,
            'Clientul nu aparține firmei active.'
        );
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

        return DB::transaction(function () use ($validated) {
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

            // F-202: salvam toate persoanele de contact trimise (0..N)
            foreach ($validated['contacts'] ?? [] as $contact) {
                $client->contacts()->create($contact);
            }

            return redirect()->route('clients.index')->with('status', 'Client adaugat cu succes.');
        });
    }

    public function edit(Client $client): View
    {
        $this->authorizeClient($client);

        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $this->authorizeClient($client);

        $validated = $this->validateClient($request, $client);

        return DB::transaction(function () use ($validated, $client) {
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

            // F-202: sincronizam lista de contacte — stergem tot si recreem din formular
            $client->contacts()->delete();
            foreach ($validated['contacts'] ?? [] as $contact) {
                $client->contacts()->create($contact);
            }

            return redirect()->route('clients.index')->with('status', 'Client actualizat cu succes.');
        });
    }

    public function destroy(Client $client): RedirectResponse
    {
        $this->authorizeClient($client);

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


    private function validateClient(Request $request, ?Client $client = null): array
    {
        $companyId = $this->activeCompanyId();
        $ignoreId = $client?->id;

        // Unicitate pe firma (constrangeri DB), ignorand clientul curent la editare.
        // Pe valori null (ex. cnp la persoana juridica) regula 'nullable' o sare.
        $uniqueInCompany = fn (string $column) => Rule::unique('clients', $column)
            ->where('company_id', $companyId)
            ->ignore($ignoreId);

        return $request->validate([
            'client_type' => 'required|in:individual,company',

            'first_name' => 'required_if:client_type,individual|nullable|string|max:255',
            'last_name' => 'required_if:client_type,individual|nullable|string|max:255',
            // F-201: validare format CNP (13 cifre + cifra de control) doar pt persoane fizice
            'cnp' => [
                'nullable', 'string', 'max:20',
                Rule::when($request->input('client_type') === 'individual', [
                    new RomanianCnpRule(),
                    $uniqueInCompany('cnp'),
                ]),
            ],

            'name' => 'required_if:client_type,company|nullable|string|max:255',
            // F-201: validare format CUI (prefix RO + cifra de control) doar pt persoane juridice
            'cui' => [
                'nullable', 'string', 'max:20', 'required_if:client_type,company',
                Rule::when($request->input('client_type') === 'company', [
                    new RomanianCuiRule(),
                    $uniqueInCompany('cui'),
                ]),
            ],
            'trade_registry_number' => ['nullable', 'string', 'max:20', $uniqueInCompany('trade_registry_number')],
            'vat_number' => 'nullable|string|max:20',

            'county' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'delivery_address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',

            // F-202: relație 1:N
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'required|string|max:255',
            'contacts.*.role' => 'required|string|max:100',
            // email unic pe client
            'contacts.*.email' => 'required|email|max:255|distinct',
            'contacts.*.phone' => 'required|string|max:20',
        ], [
            'cnp.unique' => 'Există deja un client cu acest CNP.',
            'cui.unique' => 'Există deja un client cu acest CUI.',
            'trade_registry_number.unique' => 'Există deja un client cu acest număr de registru al comerțului.',
        ]);
    }
}
