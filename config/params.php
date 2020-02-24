<?php
return [
    'file.storage' => [
        'local' => [
            'root' => '/var/www/html/tests/static',
            'permissions' => [
                'file' => [
                    'public' => 0644,
                    'private' => 0600,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0700,
                ],
            ]
        ],
    ]
];
