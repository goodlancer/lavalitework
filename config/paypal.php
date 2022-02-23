<?php 
return [ 
    'client_id' => env('PAYPAL_CLIENT_ID','AbMqcf4dQHBlGdS1kpNQlnqa1-0l_h4yAkGlnBKMKfAMtKd5Uwc59O73_rJ68nTSje3pUfI8yasGI3bm'),
    'secret' => env('PAYPAL_SECRET','EPjjSfGmBObSbW66CPoLyVDCj_6EHy73Gx8zwLWCFn_J1p3gS3i5oOKr25sV2Eo5eZbCE3LamNUnoKMk'),
    'settings' => array(
        'mode' => env('PAYPAL_MODE','sandbox'),
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path() . '/logs/paypal.log',
        'log.LogLevel' => 'ERROR'
    ),
];