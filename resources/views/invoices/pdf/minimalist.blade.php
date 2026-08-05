<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $invoice->number ? $invoice->series.'-'.$invoice->number : 'ciorna' }}</title>
    <style>
        @page { margin: 48px 46px 58px; }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: #222222;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10px;
            line-height: 1.6;
            font-weight: normal;
        }

        .header, .parties, .summary { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: top; }
        .brand { font-size: 18px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: #222222; }
        .document-title { font-size: 15px; font-weight: 400; text-align: right; text-transform: uppercase; letter-spacing: 4px; color: #888888; }
        .document-number { margin-top: 4px; font-size: 13px; text-align: right; font-weight: 700; }

        .status {
            display: inline-block;
            margin-top: 8px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #888888;
        }

        .divider { margin: 22px 0; border-top: 1px solid #e5e5e5; }

        .parties { margin-top: 6px; }
        .parties td { width: 50%; padding: 0 20px 0 0; vertical-align: top; }
        .parties td + td { padding: 0 0 0 20px; }
        .section-label { margin-bottom: 6px; color: #aaaaaa; font-size: 8px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        .party-name { margin-bottom: 4px; font-size: 12px; font-weight: 700; }
        .muted { color: #999999; }

        .meta { width: 100%; margin: 24px 0; border-collapse: collapse; }
        .meta td { width: 25%; padding: 4px 12px 4px 0; }
        .meta-label { display: block; margin-bottom: 3px; color: #aaaaaa; font-size: 8px; letter-spacing: 1px; text-transform: uppercase; }
        .meta-value { font-weight: 700; }

        .lines { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .lines thead { display: table-header-group; }
        .lines tr { page-break-inside: avoid; }
        .lines th {
            padding: 6px 6px;
            border-bottom: 1px solid #222222;
            color: #999999;
            font-size: 8px;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .lines td { padding: 9px 6px; border-bottom: 1px solid #eeeeee; vertical-align: top; }
        .number { text-align: right !important; white-space: nowrap; }
        .description { width: 34%; }
        .sku { margin-top: 2px; color: #bbbbbb; font-size: 8px; }

        .summary { margin-top: 18px; }
        .summary-spacer { width: 58%; }
        .summary-totals { width: 42%; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 6px 4px; }
        .summary-table .total td {
            padding-top: 10px;
            border-top: 1px solid #222222;
            font-size: 13px;
            font-weight: 700;
        }

        .payment-details { margin-top: 24px; padding: 0; page-break-inside: avoid; }
        .payment-details p { margin: 3px 0; color: #555555; }
        .footer { position: fixed; right: 0; bottom: -36px; left: 0; color: #bbbbbb; font-size: 8px; letter-spacing: .5px; }
        .footer-right { float: right; }
    </style>
</head>
<body>
@include('invoices.pdf._body')
</body>
</html>
