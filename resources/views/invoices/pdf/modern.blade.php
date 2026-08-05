<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $invoice->number ? $invoice->series.'-'.$invoice->number : 'ciorna' }}</title>
    <style>
        @page { margin: 34px 38px 52px; }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: #1e293b;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10px;
            line-height: 1.45;
        }

        .header, .parties, .summary { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: top; }
        .brand { color: #0369a1; font-size: 22px; font-weight: 700; letter-spacing: .5px; }
        .document-title { font-size: 21px; font-weight: 700; text-align: right; text-transform: uppercase; }
        .document-number { margin-top: 3px; color: #475569; font-size: 12px; text-align: right; }

        .status {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 8px;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            background: #f0f9ff;
            color: #0369a1;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .divider { margin: 16px 0; border-top: 2px solid #0ea5e9; }

        .parties td { width: 50%; padding: 0 14px 0 0; vertical-align: top; }
        .parties td + td { padding: 0 0 0 14px; border-left: 1px solid #e2e8f0; }
        .section-label { margin-bottom: 5px; color: #64748b; font-size: 8px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; }
        .party-name { margin-bottom: 4px; color: #0f172a; font-size: 12px; font-weight: 700; }
        .muted { color: #64748b; }

        .meta { width: 100%; margin: 18px 0; border-collapse: collapse; background: #f8fafc; }
        .meta td { width: 25%; padding: 9px 10px; border: 1px solid #e2e8f0; }
        .meta-label { display: block; margin-bottom: 2px; color: #64748b; font-size: 8px; text-transform: uppercase; }
        .meta-value { color: #0f172a; font-weight: 700; }

        .lines { width: 100%; border-collapse: collapse; }
        .lines thead { display: table-header-group; }
        .lines tr { page-break-inside: avoid; }
        .lines th {
            padding: 8px 6px;
            border-bottom: 2px solid #cbd5e1;
            background: #f1f5f9;
            color: #475569;
            font-size: 8px;
            text-align: left;
            text-transform: uppercase;
        }
        .lines td { padding: 8px 6px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .number { text-align: right !important; white-space: nowrap; }
        .description { width: 34%; }
        .sku { margin-top: 2px; color: #94a3b8; font-size: 8px; }

        .summary { margin-top: 14px; }
        .summary-spacer { width: 58%; }
        .summary-totals { width: 42%; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 5px 7px; }
        .summary-table .total td {
            padding-top: 8px;
            border-top: 2px solid #0ea5e9;
            color: #0f172a;
            font-size: 12px;
            font-weight: 700;
        }

        .payment-details {
            margin-top: 18px;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background: #f8fafc;
            page-break-inside: avoid;
        }
        .payment-details p { margin: 2px 0; }
        .footer { position: fixed; right: 0; bottom: -30px; left: 0; color: #94a3b8; font-size: 8px; }
        .footer-right { float: right; }
    </style>
</head>
<body>
@include('invoices.pdf._body')
</body>
</html>
