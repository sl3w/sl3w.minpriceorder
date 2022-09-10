<?php

CModule::AddAutoloadClasses(
    'sl3w.minpriceorder',
    [
        'Sl3w\MinPriceOrder\Settings' => 'lib/classes/Settings.php',
        'Sl3w\MinPriceOrder\Events' => 'lib/classes/Events.php',
    ]
);