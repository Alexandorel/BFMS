<?php

namespace Tests\Feature;

use App\Models\Audit;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * F-101: the audit log answers "who changed what". NFR-1 adds that it may
 * never reach across companies, so most of these tests are about isolation.
 */
class AuditLogAccessTest extends TestCase
{
    use RefreshDatabase;

    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // Auditable::isAuditingEnabled() returns false in console unless this
        // is on, and the whole suite runs in console.
        config(['audit.console' => true]);
    }

    public function test_an_accountant_can_open_the_audit_log(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index'))
            ->assertOk();
    }

    public function test_an_administrator_can_open_the_audit_log(): void
    {
        [$admin, $company] = $this->userWithCompany('administrator');

        $this->actingAs($admin)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index'))
            ->assertOk();
    }

    public function test_an_operator_cannot_open_the_audit_log(): void
    {
        [$operator, $company] = $this->userWithCompany('operator');

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index'))
            ->assertForbidden();
    }

    public function test_a_guest_is_sent_to_the_login_page(): void
    {
        $this->get(route('audit-log.index'))
            ->assertRedirect(route('login'));
    }

    /**
     * The one that matters: company B changes must not surface in company A.
     */
    public function test_the_log_is_limited_to_the_active_company(): void
    {
        [$contabil, $companyA] = $this->userWithCompany('contabil');
        $companyB = $this->userWithCompany('administrator')[1];

        $this->makeProduct($companyA, 'Produs firma A');
        $this->makeProduct($companyB, 'Produs firma B');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $companyA->id])
            ->get(route('audit-log.index'))
            ->assertOk()
            ->assertSee('Produs firma A')
            ->assertDontSee('Produs firma B');
    }

    /**
     * switchCompany() writes the session straight from the URL, so a forged id
     * has to be stopped when the log is read.
     */
    public function test_a_session_pointing_at_a_foreign_company_is_rejected(): void
    {
        [$contabil] = $this->userWithCompany('contabil');
        $foreign = $this->userWithCompany('administrator')[1];

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $foreign->id])
            ->get(route('audit-log.index'))
            ->assertForbidden();
    }

    public function test_the_event_filter_narrows_the_list(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');
        $this->makeProduct($company, 'Produs filtrat');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index', ['event' => 'created']))
            ->assertOk()
            ->assertSee('Produs filtrat');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index', ['event' => 'deleted']))
            ->assertOk()
            ->assertSee('Nicio înregistrare pentru filtrele alese.');
    }

    public function test_an_invented_event_value_is_refused(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index', ['event' => 'drop-table']))
            ->assertSessionHasErrors('event');
    }

    /**
     * Without the company_id stamp every row would be invisible to the scope.
     */
    public function test_writing_an_audited_model_stamps_the_company(): void
    {
        $company = $this->userWithCompany('contabil')[1];

        $product = $this->makeProduct($company, 'Produs auditat');
        $product->update(['unit_price' => 250.00]);

        $audits = Audit::where('auditable_type', Product::class)
            ->where('auditable_id', $product->id)
            ->get();

        $this->assertCount(2, $audits, 'creation and update should both be logged');
        $this->assertSame(
            [$company->id, $company->id],
            $audits->pluck('company_id')->all()
        );
    }

    public function test_the_diff_shows_the_changed_field_in_romanian(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');

        $product = $this->makeProduct($company, 'Produs modificat');
        $product->update(['unit_price' => 250.00]);

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index'))
            ->assertOk()
            ->assertSee('Modificare')
            ->assertSee('Preț unitar');
    }

    public function test_the_administrator_dashboard_previews_the_audit_log(): void
    {
        [$admin, $company] = $this->userWithCompany('administrator');
        $this->makeProduct($company, 'Produs de pe dashboard');

        $this->actingAs($admin)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('dashboard.administrator'))
            ->assertOk()
            ->assertSee('Activitate recentă')
            ->assertSee('Produs de pe dashboard');
    }

    public function test_the_administrator_dashboard_preview_stays_inside_the_company(): void
    {
        [$admin, $companyA] = $this->userWithCompany('administrator');
        $companyB = $this->userWithCompany('administrator')[1];

        $this->makeProduct($companyB, 'Produs al altei firme');

        $this->actingAs($admin)
            ->withSession(['active_company_id' => $companyA->id])
            ->get(route('dashboard.administrator'))
            ->assertOk()
            ->assertDontSee('Produs al altei firme');
    }

    public function test_the_sidebar_links_to_the_audit_log_for_an_administrator(): void
    {
        [$admin, $company] = $this->userWithCompany('administrator');

        $this->actingAs($admin)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee('Jurnal de audit');
    }

    /**
     * The sidebar is shared with the product screens the operator can open,
     * so the link has to stay hidden there instead of leading to a 403.
     */
    public function test_the_sidebar_hides_the_audit_log_from_an_operator(): void
    {
        [$operator, $company] = $this->userWithCompany('operator');

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('products.index'))
            ->assertOk()
            ->assertDontSee('Jurnal de audit');
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

    private function makeProduct(Company $company, string $name): Product
    {
        $this->sequence++;

        return Product::create([
            'company_id' => $company->id,
            'name' => $name,
            'sku' => 'SKU-'.$this->sequence,
            'unit_measure' => 'buc',
            'unit_price' => 100.00,
            'vat_rate' => 19.00,
        ]);
    }
}
