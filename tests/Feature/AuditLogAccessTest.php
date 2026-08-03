<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Models\Audit;
use App\Models\Client;
use App\Models\Company;
use App\Models\DocumentSeries;
use App\Models\Invoice;
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
     * getModified() pushes the values back through the model casts, so an
     * invoice diff carries an InvoiceStatus instance where a product diff only
     * ever carried scalars. Casting that to a string killed the page.
     */
    public function test_an_invoice_diff_renders_a_casted_enum(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');
        $invoice = $this->makeInvoice($company, $contabil);

        $invoice->update(['status' => InvoiceStatus::FullyPaid]);

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index'))
            ->assertOk()
            ->assertSee('Stare')
            ->assertSee('Emisă')
            ->assertSee('Încasată total');
    }

    /**
     * getModified() serializes dates with serializeDate(), which hands the
     * blade an ISO-8601 string rather than a Carbon.
     */
    public function test_a_date_change_is_readable(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');
        $invoice = $this->makeInvoice($company, $contabil);

        $invoice->update(['due_date' => '2026-09-15']);

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index'))
            ->assertOk()
            ->assertSee('Dată scadență')
            ->assertSee('15.09.2026')
            ->assertDontSee('2026-09-15T');
    }

    /**
     * audit.strict is false, so $hidden is not honoured and nothing keeps the
     * password hash out of the audit trail by default.
     */
    public function test_a_password_change_never_reaches_the_audit_trail(): void
    {
        [$user] = $this->userWithCompany('administrator');

        $user->update(['password' => 'a-brand-new-password']);

        $audits = Audit::where('auditable_type', User::class)
            ->where('auditable_id', $user->id)
            ->get();

        foreach ($audits as $audit) {
            $this->assertArrayNotHasKey('password', (array) $audit->old_values);
            $this->assertArrayNotHasKey('password', (array) $audit->new_values);
        }
    }

    /**
     * The users table has no company_id, so User::auditCompanyId() falls back
     * to the company the actor had active when the change was made.
     */
    public function test_a_profile_change_is_filed_under_the_active_company(): void
    {
        [$admin, $company] = $this->userWithCompany('administrator');

        $this->actingAs($admin)
            ->withSession(['active_company_id' => $company->id])
            ->put(route('administrator.profile.update'), [
                'first_name' => 'Prenume nou',
            ])
            ->assertRedirect(route('administrator.settings.profile'));

        $audit = Audit::where('auditable_type', User::class)
            ->where('auditable_id', $admin->id)
            ->where('event', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($audit, 'the profile change should be audited');
        $this->assertSame($company->id, $audit->company_id);
    }

    public function test_a_profile_change_shows_up_in_the_company_log(): void
    {
        [$admin, $company] = $this->userWithCompany('administrator');

        $this->actingAs($admin)
            ->withSession(['active_company_id' => $company->id])
            ->put(route('administrator.profile.update'), ['first_name' => 'Prenume nou']);

        $this->actingAs($admin)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index'))
            ->assertOk()
            ->assertSee('Utilizator')
            ->assertSee('Prenume nou');
    }

    public function test_the_accountant_gets_the_sidebar_on_the_audit_log(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index'))
            ->assertOk()
            ->assertSee('Jurnal de audit')
            ->assertSee(route('dashboard.contabil'));
    }

    /**
     * The sidebar used to send everyone to the administrator dashboard, which
     * answers 403 for the other two roles.
     */
    public function test_the_sidebar_dashboard_link_follows_the_role(): void
    {
        [$operator, $company] = $this->userWithCompany('operator');

        $this->actingAs($operator)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee(route('dashboard.operator'))
            ->assertDontSee(route('dashboard.administrator'));
    }

    public function test_the_entity_filter_narrows_the_list(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');
        $this->makeProduct($company, 'Produs de filtrat');
        $this->makeInvoice($company, $contabil);

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index', ['auditable_type' => Product::class]))
            ->assertOk()
            ->assertSee('Produs de filtrat')
            ->assertDontSee('FCT');
    }

    public function test_the_user_filter_narrows_the_list(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');

        // acting user is recorded on the audit, so the product below is his
        $this->actingAs($contabil);
        $this->makeProduct($company, 'Produs al contabilului');

        $other = User::create([
            'first_name' => 'Alt',
            'last_name' => 'Utilizator',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'operator',
        ]);
        $other->companies()->attach($company);

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index', ['user_id' => $other->id]))
            ->assertOk()
            ->assertDontSee('Produs al contabilului');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index', ['user_id' => $contabil->id]))
            ->assertOk()
            ->assertSee('Produs al contabilului');
    }

    public function test_the_date_filters_narrow_the_list(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');
        $this->makeProduct($company, 'Produs de azi');

        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index', ['from' => $today]))
            ->assertOk()
            ->assertSee('Produs de azi');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index', ['from' => $tomorrow]))
            ->assertOk()
            ->assertDontSee('Produs de azi');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index', ['to' => now()->subDay()->toDateString()]))
            ->assertOk()
            ->assertDontSee('Produs de azi');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index', ['from' => $today, 'to' => $today]))
            ->assertOk()
            ->assertSee('Produs de azi');
    }

    /**
     * to before from fails validation, and the screen has to say so instead of
     * silently redirecting back to an unchanged list.
     */
    public function test_an_inverted_date_range_is_reported_to_the_user(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');

        $response = $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index', [
                'from' => now()->toDateString(),
                'to' => now()->subDays(5)->toDateString(),
            ]));

        $response->assertSessionHasErrors('to');
        $response->assertRedirect(route('audit-log.index'));
    }

    /**
     * Mirrors the browser: the user is already on the log, then submits the
     * filter form with a bad range. Laravel redirects to the previous url.
     */
    public function test_an_inverted_range_does_not_bounce_back_to_itself(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');

        $bad = route('audit-log.index', [
            'from' => now()->toDateString(),
            'to' => now()->subDays(5)->toDateString(),
        ]);

        $this->actingAs($contabil)->withSession(['active_company_id' => $company->id]);

        $this->get(route('audit-log.index'))->assertOk();
        $this->get($bad)->assertRedirect()->assertSessionHasErrors('to');

        $this->assertNotSame($bad, $this->get($bad)->headers->get('Location'));
    }

    /**
     * The form submits every input, so empty ones must not narrow anything.
     */
    public function test_empty_filters_are_ignored(): void
    {
        [$contabil, $company] = $this->userWithCompany('contabil');
        $this->makeProduct($company, 'Produs vizibil');

        $this->actingAs($contabil)
            ->withSession(['active_company_id' => $company->id])
            ->get(route('audit-log.index', [
                'from' => '',
                'to' => '',
                'user_id' => '',
                'auditable_type' => '',
                'event' => '',
            ]))
            ->assertOk()
            ->assertSee('Produs vizibil');
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

    private function makeInvoice(Company $company, User $user): Invoice
    {
        $this->sequence++;

        $client = Client::create([
            'company_id' => $company->id,
            'client_type' => 'company',
            'name' => 'Client Test SRL',
            'cui' => 'RO8765432'.$this->sequence,
            'county' => 'Cluj',
            'city' => 'Cluj-Napoca',
            'address' => 'Strada Client nr. '.$this->sequence,
        ]);

        $series = DocumentSeries::create([
            'company_id' => $company->id,
            'document_type' => DocumentType::Invoice,
            'prefix' => 'FCT',
            'start_number' => 1,
            'current_number' => 1,
            'is_default' => true,
            'is_active' => true,
        ]);

        return Invoice::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'document_series_id' => $series->id,
            'document_type' => DocumentType::Invoice,
            'series' => 'FCT',
            'number' => $this->sequence,
            'status' => InvoiceStatus::Issued,
            'issue_date' => '2026-08-01',
            'due_date' => '2026-08-31',
            'currency' => 'RON',
            'exchange_rate' => 1,
            'subtotal' => 100,
            'vat_total' => 19,
            'total' => 119,
            'created_by' => $user->id,
        ]);
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
