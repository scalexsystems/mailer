<?php return [
    // MailGun configuration.
    'default' => 'mailgun',

    'drivers' => [
        'smtp' => [
            'host' => 'localhost',
            'port' => 587,
            'encryption' => 'tls',
            'user' => 'foo@example.com',
            'pass' => '*********',
        ],

        'mailgun' => [
            'key' => 'key-xxxxxxxxxxxx',
            'domain' => 'example.com',
        ]
    ],

    // Sender configuration.
    'from' => ['foo@example.com', 'Foo Bar',],

    // Reply To configuration.
    // 'reply' => ['bar@example.com', 'Bar Baz',],

    // Mail configuration.
    'subject' => 'An email from Foo',
];
