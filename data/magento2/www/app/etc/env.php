<?php
return [
    'backend' => [
        'frontName' => 'ecoadmin'
    ],
    'crypt' => [
        'key' => '5db98e6476a8abe11535267c1ea48e60'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => '78.129.186.234',
                'dbname' => 'magento_eco',
                'username' => 'magento_eco',
                'password' => '6l27u?Au',
                'active' => '1'
            ]
        ]
    ],
    'remote_storage' => [
        'driver' => 'file'
    ],
    'queue' => [
        'consumers_wait_for_messages' => 1
    ],
    'cryptXXX' => [
        'key' => 'a112333d06324033e37c8c32bf5b779f'
    ],
    'dbXXX' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'mysql',
                'dbname' => 'magento',
                'username' => 'magento',
                'password' => 'magento',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
                'driver_options' => [
                    1014 => false
                ]
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files'
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'id_prefix' => 'f29_'
            ],
            'page_cache' => [
                'id_prefix' => 'f29_'
            ]
        ],
        'allow_parallel_generation' => false
    ],
    'lock' => [
        'provider' => 'db',
        'config' => [
            'prefix' => ''
        ]
    ],
    'directories' => [
        'document_root_is_pub' => true
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'compiled_config' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 0,
        'config_webservice' => 1,
        'translate' => 1,
        'vertex' => 1
    ],
    'downloadable_domains' => [
        'magento2.dev.com'
    ],
    'install' => [
        'date' => 'Sun, 06 Mar 2022 20:53:40 +0000'
    ],
    'system' => [
        'default' => [
            'catalog' => [
                'search' => [
                    'elasticsearch5_server_hostname' => 'elasticsearch',
                    'elasticsearch5_server_port' => '9200',
                    'elasticsearch5_index_prefix' => 'magento2',
                    'elasticsearch5_enable_auth' => '0',
                    'elasticsearch5_server_timeout' => '15',
                    'elasticsearch6_server_hostname' => 'elasticsearch',
                    'elasticsearch6_server_port' => '9200',
                    'elasticsearch6_index_prefix' => 'magento2',
                    'elasticsearch6_enable_auth' => '0',
                    'elasticsearch6_server_timeout' => '15',
                    'elasticsearch7_server_hostname' => 'elasticsearch',
                    'elasticsearch7_server_port' => '9200',
                    'elasticsearch7_index_prefix' => 'eco_',
                    'elasticsearch7_enable_auth' => '1',
                    'elasticsearch7_server_timeout' => '15',
                    'elasticsearch7_username' => 'eco',
                    'elasticsearch7_password' => 'eco'
                ]
            ]
        ]
    ]
];
