<?php

return [

    // 阿里大鱼信息
    'DaYuAccessKey' => env('DA_YU_ACCESS_KEY', ''),

    'DaYuAccessSecret' => env('DA_YU_ACCESS_SECRET', ''),

    'TemplateCode' => env('TEMPLATE_CODE', ''),

    // 对象存储信息
    'AccessKeyId' => env('AccessKeyId', ''),

    'AccessKeySecret' => env('AccessKeySecret', ''),

    'BucketName' => env('BucketName', ''),

    'OSS_CNAME' => env('OSS_CNAME', ''),
    
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
