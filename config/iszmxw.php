<?php

return [

    'AccessKeyId' => env('AccessKeyId', ''),

    'AccessKeySecret' => env('AccessKeySecret', ''),

    'BucketName' => env('BucketName', ''),

    'admin_role' => [
        'public_route' => [
            'admin/dashboard',
            'admin/login',
            'admin/login_check',
            'admin/error_page',
            'admin/system/tree_data',
            'admin/system/role_modal_add',
            'admin/quit'
        ]
    ]

];
