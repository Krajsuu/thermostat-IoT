<?php
// config/mqtt.php
return [
    'default' => [
        'host' => env('MQTT_HOST'),
        'port' => env('MQTT_PORT'),
        'protocol_version' => \PhpMqtt\Client\MqttClient::MQTT_3_1_1, // <-- KLUCZOWA ZMIANA
        'connection_settings' => [
            'tls' => [
                'ca_file' => env('MQTT_CA_FILE'),
                'cert_file' => env('MQTT_CERT_FILE'),
                'private_key_file' => env('MQTT_KEY_FILE'),
                'verify_peer' => true,
            ],
            'connect_timeout' => 50, // Skróć timeout, żeby szybciej widzieć błędy
        ],
    ],
];