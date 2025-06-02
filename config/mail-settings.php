<?php
return [
    'tabs' => [
        'mail-template' => 'Mặc định',
    ],
    'mail-template' => [
        'default' => [
            'all' => [
                'type' => 'select',
                'options' => [
                    '1' => 'Tất cả email',
                    '2' => 'Group',
                    '3' => 'Email cụ thể',
                ]
            ],
//            'type' => 'send-now',
            'user' => [
                'type' => 'select2',
                'options' => [],
            ],
            'group' => [
                'type' => 'select_group',
                'options' => [],
            ],
            'subject' => 'text',
//            'cc' => 'textarea',
            'body' => 'wysiwyg',
            'schedule' => 'time_picker',
        ],
    ],
];
