<?php
defined('MOODLE_INTERNAL') || die();

$definitions = [
    'iareport_cache' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => false,
        'ttl' => 3600, // cache por 1 hora
    ]
];