<?php

return [
    'dump' => [
        'html' => [
            'dumper_class' => \Symfony\Component\VarDumper\Dumper\HtmlDumper::class,
            'dumper_options' => [
                'search_input_attributes' => [
                    'id' => 'dump-search',
                    'name' => 'dump-search',
                ],
            ],
        ],
    ],
];
