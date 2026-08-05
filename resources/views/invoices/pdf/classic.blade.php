<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $invoice->number ? $invoice->series.'-'.$invoice->number : 'ciorna' }}</title>
    <style>
        @page { margin: 40px 42px 56px; }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: #111111;
            font-family: "DejaVu Serif", serif;
            font-size: 10px;
            line-height: 1.5;
        }

        .header, .parties, .summary { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: top; }
        .brand { font-size: 20px; font-weight: 700; letter-spacing: .5px; color: #111111; }
        .document-title { font-size: 20px; font-weight: 700; text-align: right; text-transform: uppercase; letter-spacing: 1px; }
        .document-number { margin-top: 3px; font-size: 12px; text-align: right; }

        .status {
            display: inline-block;
            margin-top: 8px;
            padding: 3px 9px;
            border: 1px solid #111111;
            color: #111111;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .divider { margin: 14px 0; border-top: 3px double #111111; }

        .parties { margin-top: 4px; }
        .parties td { width: 50%; padding: 12px 14px; vertical-align: top; border: 1px solid #111111; }
        .parties td + td { border-left: none; }
        .section-label { margin-bottom: 6px; font-size: 8px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; text-decoration: underline; }
        .party-name { margin-bottom: 4px; font-size: 12px; font-weight: 700; }
        .muted { color: #333333; }

        .meta { width: 100%; margin: 16px 0; border-collapse: collapse; }
        .meta td { width: 25%; padding: 8px 10px; border: 1px solid #111111; }
        .meta-label { display: block; margin-bottom: 2px; font-size: 8px; text-transform: uppercase; }
        .meta-value { font-weight: 700; }

        .lines { width: 100%; border-collapse: collapse; border: 1px solid #111111; }
        .lines thead { display: table-header-group; }
        .lines tr { page-break-inside: avoid; }
        .lines th {
            padding: 8px 6px;
            border: 1px solid #111111;
            background: #eeeeee;
            font-size: 8px;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .lines td { padding: 7px 6px; border: 1px solid #111111; vertical-align: top; }
        .number { text-align: right !important; white-space: nowrap; }
        .description { width: 34%; }
        .sku { margin-top: 2px; color: #555555; font-size: 8px; }

        .summary { margin-top: 14px; }
        .summary-spacer { width: 58%; }
        .summary-totals { width: 42%; }
        .summary-table { width: 100%; border-collapse: collapse; border: 1px solid #111111; }
        .summary-table td { padding: 6px 8px; border-bottom: 1px solid #cccccc; }
        .summary-table .total td {
            border-top: 2px solid #111111;
            border-bottom: none;
            font-size: 12px;
            font-weight: 700;
        }

        .payment-details {
            margin-top: 18px;
            padding: 10px 12px;
            border: 1px solid #111111;
            page-break-inside: avoid;
        }
        .payment-details p { margin: 2px 0; }
        .footer { position: fixed; right: 0; bottom: -34px; left: 0; color: #555555; font-size: 8px; border-top: 1px solid #cccccc; padding-top: 4px; }
        .footer-right { float: right; }
    </style>
</head>
<body>
@include('invoices.pdf._body')
</body>
</html>
