<?php

return [
    'default' => [
        'host'      => env('MQTT_HOST'),
        'port'      => env('MQTT_PORT'),
        'client_id' => env('MQTT_CLIENT_ID'),
        'connection_settings' => [
            'tls' => [
                'ca_file'   => env('MQTT_CA_PATH'),
                'cert_file' => env('MQTT_CERT_PATH'),
                'private_key_file' => env('MQTT_KEY_PATH'),
                'verify_peer' => true,
            ],
        ],
    ],
];