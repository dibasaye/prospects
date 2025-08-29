<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des Contrats
    |--------------------------------------------------------------------------
    |
    | Configuration centralisée pour la gestion des contrats
    |
    */

    'statuses' => [
        'draft' => 'brouillon',
        'signed' => 'signe',
        'cancelled' => 'annule',
        'completed' => 'complete',
    ],

    'default_settings' => [
        'payment_duration_months' => 24,
        'default_price' => 7000000, // 7 millions FCFA
        'processing_fee' => 100000, // 100k FCFA
        'penalty_rate' => 0.05, // 5%
        'cancellation_penalty' => 0.10, // 10%
    ],

    'company_info' => [
        'name' => 'YAYE DIA BTP',
        'legal_form' => 'Société par actions simplifiée (SAS)',
        'address' => 'Cité Keur-Gorgui lot 33 et 34',
        'registration_number' => 'SN DKR 2024 B 31686',
        'ninea' => '011440188',
        'representative' => 'Fatou Faye',
        'representative_title' => 'Gérante',
        'phone' => '+221 78 192 00 00 / +221 33 827 00 65',
        'email' => 'yayediasarl@gmail.com',
        'website' => 'www.groupeyaye.com',
        'bank_accounts' => [
            'SN039 01001 067615921200 05',
            'SN012 01201 036206462201 47'
        ],
    ],

    'project_info' => [
        'location' => 'LELO SERERE',
        'total_area' => '324ha 69a 80ca',
        'deliberation_number' => '002 /AKM',
        'deliberation_date' => '26-01-2019',
        'lot_area' => '225m2', // par lot
    ],

    'images' => [
        'header' => 'images/yayedia.png',
        'footer' => 'images/footer-image.png',
        'watermark' => 'images/image.png',
    ],

    'pdf_settings' => [
        'font_family' => 'dejavu sans',
        'font_size_base' => '12.2pt',
        'font_size_small' => '11pt',
        'font_size_tiny' => '10pt',
        'line_height' => '1.45',
        'margin' => '18mm 16mm 18mm 16mm',
        'dpi' => 96,
    ],

    'word_settings' => [
        'font_family' => 'Times New Roman',
        'font_size' => 12,
        'margins' => [
            'left' => 1134,   // 2cm en twips
            'right' => 1134,
            'top' => 1134,
            'bottom' => 1134,
        ],
    ],

    'validation_rules' => [
        'content' => 'required|string|max:50000',
        'contract_number' => 'required|string|unique:contracts,contract_number',
        'total_amount' => 'required|numeric|min:1',
        'payment_duration_months' => 'required|integer|min:1|max:120',
    ],

    'auto_save' => [
        'enabled' => true,
        'delay_seconds' => 2,
        'max_content_length' => 50000,
    ],

    'export' => [
        'max_generation_time' => 300, // 5 minutes
        'memory_limit' => '512M',
        'temp_file_prefix' => 'contract_',
    ],
];