<?php

return [
    'variables' => [
        '{nume_client}',
        '{nume_firma}',
        '{numar_factura}',
        '{total}',
        '{moneda}',
        '{data_scadenta}',
        '{rest_de_plata}',
    ],
    'defaults' => [
        'invoice_issued' => [
            'subject' => 'Factura {numar_factura} de la {nume_firma}',
            'body' => "Bună ziua, {nume_client},\n\nVă transmitem atașat factura {numar_factura} în valoare de {total} {moneda}, cu scadența la {data_scadenta}.\n\nVă mulțumim,\n{nume_firma}",
        ],
        'due_reminder' => [
            'subject' => 'Reamintire: factura {numar_factura} ajunge la scadență',
            'body' => "Bună ziua, {nume_client},\n\nVă reamintim că factura {numar_factura} are scadența la {data_scadenta}, cu un rest de plată de {rest_de_plata} {moneda}.\n\nDacă ați efectuat deja plata, vă rugăm să ignorați acest mesaj.\n\n{nume_firma}",
        ],
        'overdue_alert' => [
            'subject' => 'Factura {numar_factura} este restantă',
            'body' => "Bună ziua, {nume_client},\n\nFactura {numar_factura}, scadentă la {data_scadenta}, figurează cu un rest de plată de {rest_de_plata} {moneda}.\n\nVă rugăm să efectuați plata în cel mai scurt timp sau să ne contactați pentru clarificări.\n\n{nume_firma}",
        ],
    ],
];