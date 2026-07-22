<?php

namespace Database\Seeders;

use App\Enums\DocumentType;
use App\Models\Client;
use App\Models\DocumentSeries;
use App\Models\Invoice;
use App\Models\InvoiceLines;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    /**
     * 5 facturi pentru compania
     * user-ului user@example.com. 
     * Necesită ClientSeeder și
     * ProductSeeder rulate în prealabil.
     */
    public function run(): void
    {
        $user = User::where('email', 'user@example.com')->first();

        if (! $user) {
            $this->command->warn('User-ul user@example.com nu există. Rulează mai întâi DatabaseSeeder.');
            return;
        }

        $company = $user->companies()->first();

        if (! $company) {
            $this->command->warn('User-ul user@example.com nu are nicio companie asociată.');
            return;
        }

        // Clienții creați de ClientSeeder
        $clientCompany = Client::where('company_id', $company->id)->where('cui', 'RO45678901')->first();
        $clientIndividual = Client::where('company_id', $company->id)->where('cnp', '1900101223344')->first();

        // Produsele create de ProductSeeder
        $consultanta = Product::where('company_id', $company->id)->where('sku', 'SRV-CONS')->first();
        $dezvoltare = Product::where('company_id', $company->id)->where('sku', 'SRV-DEV')->first();
        $licenta = Product::where('company_id', $company->id)->where('sku', 'PRD-LIC')->first();

        if (! $clientCompany || ! $clientIndividual || ! $consultanta || ! $dezvoltare || ! $licenta) {
            $this->command->warn('Lipsesc clienți sau produse. Rulează ClientSeeder și ProductSeeder înainte.');
            return;
        }

        DB::transaction(function () use ($user, $company, $clientCompany, $clientIndividual, $consultanta, $dezvoltare, $licenta) {
            // Seria de documente pentru facturi
            $series = DocumentSeries::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'document_type' => DocumentType::Invoice,
                    'prefix' => 'BFMS',
                ],
                [
                    'start_number' => 1,
                    'current_number' => 0,
                ]
            );

            // Definirea celor 5 facturi
            $invoicesData = [
                [
                    'client' => $clientCompany,
                    'status' => 'draft',
                    'issue_date' => '2026-07-01',
                    'due_date' => '2026-07-31',
                    'lines' => [[$consultanta, 10], [$dezvoltare, 5]],
                    'paid' => null,
                ],
                [
                    'client' => $clientIndividual,
                    'status' => 'issued',
                    'issue_date' => '2026-07-05',
                    'due_date' => '2026-08-04',
                    'lines' => [[$licenta, 1]],
                    'paid' => null,
                ],
                [
                    'client' => $clientCompany,
                    'status' => 'fully_paid',
                    'issue_date' => '2026-07-08',
                    'due_date' => '2026-08-07',
                    'lines' => [[$dezvoltare, 8]],
                    'paid' => 'full',
                ],
                [
                    'client' => $clientIndividual,
                    'status' => 'partially_paid',
                    'issue_date' => '2026-07-12',
                    'due_date' => '2026-08-11',
                    'lines' => [[$consultanta, 4], [$licenta, 2]],
                    'paid' => 'partial',
                ],
                [
                    'client' => $clientCompany,
                    'status' => 'issued',
                    'issue_date' => '2026-07-18',
                    'due_date' => '2026-08-17',
                    'lines' => [[$consultanta, 6], [$dezvoltare, 3], [$licenta, 1]],
                    'paid' => null,
                ],
            ];

            foreach ($invoicesData as $data) {
                $series->increment('current_number');
                $number = $series->current_number;

                $invoice = Invoice::create([
                    'company_id' => $company->id,
                    'client_id' => $data['client']->id,
                    'document_series_id' => $series->id,
                    'document_type' => DocumentType::Invoice,
                    'series' => $series->prefix,
                    'number' => $number,
                    'status' => $data['status'],
                    'issue_date' => $data['issue_date'],
                    'due_date' => $data['due_date'],
                    'currency' => 'RON',
                    'exchange_rate' => 1,
                    'subtotal' => 0,
                    'vat_total' => 0,
                    'total' => 0,
                    'created_by' => $user->id,
                ]);

                $subtotal = 0;
                $vatTotal = 0;
                $position = 1;

                foreach ($data['lines'] as [$product, $quantity]) {
                    $lineSubtotal = round($product->unit_price * $quantity, 2);
                    $lineVat = round($lineSubtotal * $product->vat_rate / 100, 2);
                    $lineTotal = round($lineSubtotal + $lineVat, 2);

                    InvoiceLines::create([
                        'invoice_id' => $invoice->id,
                        'product_id' => $product->id,
                        'product_name_snapshot' => $product->name,
                        'sku_snapshot' => $product->sku,
                        'unit_measure_snapshot' => $product->unit_measure,
                        'unit_price_snapshot' => $product->unit_price,
                        'vat_rate_snapshot' => $product->vat_rate,
                        'quantity' => $quantity,
                        'line_subtotal' => $lineSubtotal,
                        'line_vat' => $lineVat,
                        'line_total' => $lineTotal,
                        'position' => $position++,
                    ]);

                    $subtotal += $lineSubtotal;
                    $vatTotal += $lineVat;
                }

                $total = round($subtotal + $vatTotal, 2);

                $invoice->update([
                    'subtotal' => $subtotal,
                    'vat_total' => $vatTotal,
                    'total' => $total,
                ]);

                // Plăți pentru facturile marcate ca încasate
                if ($data['paid'] === 'full') {
                    $this->createPayment($invoice, $company->id, $user->id, $total, $data['issue_date']);
                } elseif ($data['paid'] === 'partial') {
                    $this->createPayment($invoice, $company->id, $user->id, round($total / 2, 2), $data['issue_date']);
                }
            }
        });

        $this->command->info('5 facturi create pentru user@example.com.');
    }

    private function createPayment(Invoice $invoice, int $companyId, int $userId, float $amount, string $date): void
    {
        Payment::create([
            'invoice_id' => $invoice->id,
            'company_id' => $companyId,
            'payment_date' => $date,
            'amount' => $amount,
            'currency' => 'RON',
            'exchange_rate' => 1,
            'payment_method' => 'bank_transfer',
            'reference' => 'OP-' . $invoice->series . $invoice->number,
            'created_by' => $userId,
        ]);
    }
}
