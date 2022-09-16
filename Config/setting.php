<?php

/**
 * [Config] setting
 *
 * @category baserCMS Plugin
 * @package  Operation.Config
 * @author   Ryosuke Momoi
 * @link     https://github.com/momofff/baserCMS-Operation
 */

/**
 * Config
 */
$config = [];
$config['Operation'] = [
    'admin' => [
        'adminsName' => [
            'admins'
        ],
        'allowedAdminAllOperation'     => TRUE,
        'allowedAllUserGroupUploads'   => FALSE,
        'allowedAllUserGroupDblogs'    => FALSE,
        'allowedAllUserGroupBlogPosts' => TRUE
    ]
];
