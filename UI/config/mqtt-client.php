<?php

return [
    'default_connection' => 'default',

    'connections' => [
        'default' => [
            'host' => env('MQTT_HOST'),
            'port' => env('MQTT_PORT', 8883),
            'protocol' => \PhpMqtt\Client\MqttClient::MQTT_3_1_1, // Wymagane przez AWS
            'client_id' => env('MQTT_CLIENT_ID', 'LaravelBackend'),
            'use_clean_session' => true,
            'enable_logging' => true,
            'log_channel' => null,
            'repository' => \PhpMqtt\Client\Repositories\MemoryRepository::class,
            'connection_settings' => [
                'tls' => [
                    'enabled' => true,
                    'allow_self_signed_certificate' => false,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'ca_file' => env('MQTT_CA_FILE'),
                    'client_certificate_file' => env('MQTT_CERT_FILE'),
                    'client_certificate_key_file' => env('MQTT_KEY_FILE'),
                    'client_certificate_key_passphrase' => null,
                ],
                'auth' => [
                    'username' => null,
                    'password' => null,
                ],
                'connect_timeout' => 10,
                'socket_timeout' => 5,
                'resend_timeout' => 10,
                'keep_alive_interval' => 60,
                'auto_reconnect' => [
                    'enabled' => false,
                    'max_reconnect_attempts' => 3,
                    'delay_between_reconnect_attempts' => 0,
                ],
            ],
        ],
    ],
];