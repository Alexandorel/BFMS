<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Requests\UpdateBankAccountRequest;
use App\Models\BankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    public function index(Request $request): View
    {
        $companies = $request->user()
            ->companies()
            ->orderBy('name')
            ->get();

        $requestedId = $request->integer('firma')
            ?: Session::get('active_company_id');

        $company = $companies->firstWhere('id', $requestedId)
            ?? $companies->first();

        $bankAccounts = $company
            ? $company->bankAccounts()
                ->orderBy('bank_name')
                ->get()
            : collect();

        return view('administrator.settings.bank-accounts', [
            'companies' => $companies,
            'company' => $company,
            'bankAccounts' => $bankAccounts,
        ]);
    }

    public function store(StoreBankAccountRequest $request): RedirectResponse
    {
        $bankAccount = BankAccount::create($request->validated());

        return to_route('administrator.bank-accounts.index', [
            'firma' => $bankAccount->company_id,
        ])->with('success', 'Contul bancar a fost adăugat.');
    }

    public function update(
        UpdateBankAccountRequest $request,
        BankAccount $bankAccount
    ): RedirectResponse {
        $bankAccount->update($request->validated());

        return to_route('administrator.bank-accounts.index', [
            'firma' => $bankAccount->company_id,
        ])->with('success', 'Contul bancar a fost actualizat.');
    }

    public function destroy(
        Request $request,
        BankAccount $bankAccount
    ): RedirectResponse {
        abort_unless(
            $request->user()
                ->companies()
                ->whereKey($bankAccount->company_id)
                ->exists(),
            403
        );

        $companyId = $bankAccount->company_id;
        $bankAccount->delete();

        return to_route('administrator.bank-accounts.index', [
            'firma' => $companyId,
        ])->with('success', 'Contul bancar a fost șters.');
    }
}
