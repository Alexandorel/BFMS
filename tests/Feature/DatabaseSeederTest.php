<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceLines;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_business_data_is_shared_by_all_three_accounts_without_duplicates(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $company = Company::where('cui', '12345678')->firstOrFail();

        foreach (['admin@gmail.com', 'operator@gmail.com', 'contabil@gmail.com'] as $email) {
            $user = User::where('email', $email)->firstOrFail();

            $this->assertTrue($user->companies()->whereKey($company->id)->exists());
        }

        $this->assertSame(2, Client::where('company_id', $company->id)->count());
        $this->assertSame(3, Product::where('company_id', $company->id)->count());
        $this->assertSame(5, Invoice::where('company_id', $company->id)->count());
        $this->assertSame(9, InvoiceLines::whereHas('invoice', fn ($query) => $query->where('company_id', $company->id))->count());
        $this->assertSame(2, Payment::where('company_id', $company->id)->count());
    }
}
