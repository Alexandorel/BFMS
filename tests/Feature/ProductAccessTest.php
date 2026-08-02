<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * DoD #3: the accountant has read-only access (NFR-1), so the product
 * catalogue must reject every write coming from that role.
 */
class ProductAccessTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    public function test_an_accountant_can_view_the_product_list(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');
        $this->makeProduct($company);

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('products.index'))
            ->assertOk();
    }

    public function test_an_accountant_cannot_open_the_create_form(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('products.create'))
            ->assertForbidden();
    }

    public function test_an_accountant_cannot_create_a_product(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->post(route('products.store'), [
                'name' => 'Produs interzis',
                'sku' => 'X-999',
                'unit_measure' => 'buc',
                'unit_price' => 100,
                'vat_rate' => 19,
            ])
            ->assertForbidden();

        $this->assertSame(0, Product::where('sku', 'X-999')->count());
    }

    public function test_an_accountant_cannot_update_a_product(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');
        $product = $this->makeProduct($company);

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->put(route('products.update', $product), [
                'name' => 'Redenumit de contabil',
                'sku' => $product->sku,
                'unit_measure' => 'buc',
                'unit_price' => 1,
                'vat_rate' => 19,
            ])
            ->assertForbidden();

        $this->assertSame($product->name, $product->fresh()->name);
    }

    public function test_an_accountant_cannot_delete_a_product(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');
        $product = $this->makeProduct($company);

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->delete(route('products.destroy', $product))
            ->assertForbidden();

        $this->assertNotNull($product->fresh());
    }

    public function test_an_operator_can_still_manage_products(): void
    {
        [$operator, $company] = $this->userWithCompany('operator');

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->post(route('products.store'), [
                'name' => 'Consultanță',
                'sku' => 'CONS-1',
                'unit_measure' => 'oră',
                'unit_price' => 250,
                'vat_rate' => 19,
            ])
            ->assertRedirect(route('products.index'));

        $this->assertSame(1, Product::where('sku', 'CONS-1')->count());
    }

    /**
     * Route::resource used to generate a products.show route pointing at a
     * controller method that does not exist, which returned a 500.
     */
    public function test_there_is_no_dangling_show_route(): void
    {
        $this->assertNull(app('router')->getRoutes()->getByName('products.show'));
    }

    /**
     * @return array{0: User, 1: Company}
     */
    private function userWithCompany(string $role): array
    {
        $this->sequence++;

        $company = Company::create([
            'name' => 'Firma Test '.$this->sequence,
            'juridical_form' => 'SRL',
            'cui' => 'RO'.(12345670 + $this->sequence),
            'trade_registry_number' => sprintf('J40/%04d/2026', $this->sequence),
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Test nr. '.$this->sequence,
            'social_capital' => 200.00,
            'vat_payer' => true,
        ]);

        $user = User::create([
            'first_name' => 'Utilizator',
            'last_name' => ucfirst($role),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => $role,
        ]);

        $user->companies()->attach($company);

        return [$user, $company];
    }

    private function makeProduct(Company $company): Product
    {
        return Product::create([
            'company_id' => $company->id,
            'name' => 'Produs existent',
            'sku' => 'SKU-'.$this->sequence,
            'unit_measure' => 'buc',
            'unit_price' => 100.00,
            'vat_rate' => 19.00,
        ]);
    }
}
