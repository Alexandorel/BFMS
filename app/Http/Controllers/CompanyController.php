<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;


class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function getCurrentCompany(){
        return auth()->user()->companies()->first();
    }
    public function getUserCompanies()
    {
        return Auth::user()->companies()->get();
    }

    public function switchCompany($id)
    {
        Session::put('active_company_id', $id);
        return redirect()->route('dashboard.administrator');
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('administrator.settings.addcompany');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $company = DB::transaction(function () use ($request, $validated) {
            $company = Company::create(Arr::only($validated, [
                'name',
                'juridical_form',
                'cui',
                'trade_registry_number',
                'county',
                'city',
                'address',
                'social_capital',
                'vat_payer',
            ]));

            $company->users()->attach($request->user()->id);

            $ibans = $validated['iban'] ?? [];
            $bankNames = $validated['bank_name'] ?? [];

            foreach ($ibans as $index => $iban) {
                $bankName = $bankNames[$index] ?? null;

                //formularul afiseaza implicit un rand gol
                if (blank($iban) && blank($bankName)) {
                    continue;
                }

                $company->bankAccounts()->create([
                    'iban' => $iban,
                    'bank_name' => $bankName,
                    'currency' => 'RON',
                ]);
            }

            return $company;
        });

        $request->session()->put('active_company_id', $company->id);

        return to_route('administrator.settings.company')
            ->with('success', 'Firma a fost adaugata cu succes.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        //
    }
}
