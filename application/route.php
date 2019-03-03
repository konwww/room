<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
//    '__alias__' => [
//        'admin' => 'index/Admin',
//    ],
    '[index]' => [
        'index' => [
            "index/Room/index", ["method" => "get"]
        ]
        , 'mark' => [
            "index/Room/mark", ["method" => "put"]
        ]
        , "detail/:cid" => [
            'index/Room/detail', ["method" => "get"]
        ]
        , 'room-list' => [
            'index/Room/readRoomList', ['method' => 'get']
        ]
        , 'history/:cid' => [
            'index/Room/getSectionHistory', ['method' => "get"]
        ]
        , 'section-info/:cid' => [
            'index/Room/getSectionInfo', ["method" => "get"]
        ]
        , 'room-info/:cid' => [
            'index/Room/getRoomBaseInfo', ["method" => 'get']
        ],
        '__miss__' => [
            'index/Room/readRoomList'
        ]
    ]
    , '[user]' => [
        'index' => ['index/User/index', ['method' => 'get']]
        , 'function-history' => ['index/User/usageHistoryForBorrowFunction', ['method' => 'get']]
        , 'read' => ['index/User/getUserList', ['method' => 'get']]
    ]
    , '[old]' => [
        'index' => [
            'index/index/emptyRoom'
        ]
        , 'detail' => [
            'index/index/detail'
        ]
    ]
    , '[oauth]' => [
        'index' => ["index/Oauth/index"]
        , 'login/[:referer]' => ["index/Oauth/login"]
    ]
    , '[admin]' => [
        'config' => ["index/Config/read"]
        , 'index' => ["index/Admin/index"]
        , 'welcome' => ["index/Admin/welcome"]
        , 'setting' => ["index/Admin/siteSetting"]
        , 'user-add' => ["index/Admin/userAdd"]
        , 'user-list' => ["index/Admin/userList"]
        , 'section-list' => ["index/Admin/sectionList"]
        , 'borrow-history' => ["index/Admin/borrowHistory"]
    ]
    , '[section]' => [
        'read' => 'index/Section/sectionList'
        , 'toggle' => 'index/Section/toggle'
        , 'log' => "index/Section/usageHistory"
    ]
    , '[test]' => [
        'index' => ["index/Test/index"]
    ]
    , '__miss__' => 'index/Room/readRoomList'

];
