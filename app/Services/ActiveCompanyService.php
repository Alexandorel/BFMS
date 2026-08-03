<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class ActiveCompanyService
{
    /**
     *returneaza firma activa doar daca utilizatorul are acces la ea
    **/
    public function get(User $user, Request $request): ?Company
    {
        $activeCompanyId = $request->session()->get('active_company_id');

        if ($activeCompanyId !== null) {
            $company = $user->companies()
                ->whereKey($activeCompanyId)
                ->first();

            abort_unless(
                $company,
                403,
                'Nu aveți acces la firma selectată.'
            );

            return $company;
        }

        $company = $user->companies()
            ->orderBy('companies.id')
            ->first();

        if ($company) {
            $request->session()->put('active_company_id', $company->id);
        }

        return $company;
    }

    /**
     * Returneaza firma activa sau opreste cererea daca utilizatorul nu are
     * nicio firma asociata.
     */
    public function require(User $user, Request $request): Company
    {
        $company = $this->get($user, $request);

        abort_unless(
            $company,
            403,
            'Nu aveti nicio firma asociata contului.'
        );

        return $company;
    }

    /**
     * Schimba firma activa numai daca aceasta apartine utilizatorului.
     */
    public function switchTo(User $user, Request $request, int $companyId): Company
    {
        $company = $user->companies()
            ->whereKey($companyId)
            ->first();

        abort_unless(
            $company,
            403,
            'Nu aveti acces la firma selectata.'
        );

        $request->session()->put('active_company_id', $company->id);

        return $company;
    }
}
