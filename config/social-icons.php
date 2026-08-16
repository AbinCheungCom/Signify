<?php

/*
|--------------------------------------------------------------------------
| 社交链接域名 → 黑色图标
|--------------------------------------------------------------------------
|
| 键为主域名（子域名自动匹配），值为 public/icons/ 下的图标文件名；
| 未命中域名使用 default 图标。
|
| PHP 端（Entrepreneur::socialIconForUrl）与前端（个人中心预览，
| 经 @json 注入同一份数据）共用此配置，保证两端判定一致。
|
*/

return [
    'default' => 'google.svg',

    'map' => [
        // 品牌站（统一使用官方 logo）
        'abincheung.com' => 'logo.svg',
        'foxfun.cn'      => 'logo.svg',
        '61ml.com'       => 'logo.svg',
        'voue.cn'        => 'logo.svg',
        'vour.cn'        => 'logo.svg',
        'ihote.com'      => 'logo.svg',
        '61lm.com'       => 'logo.svg',

        // 常见社交平台
        'xiaohongshu.com' => 'xiaohongshu.svg',
        'weibo.com'       => 'weibo.svg',
        'weibo.cn'        => 'weibo.svg',
        'douyin.com'      => 'douyin.svg',
        'facebook.com'    => 'facebook.svg',
        'fb.com'          => 'facebook.svg',
        'instagram.com'   => 'instagram.svg',
        'telegram.me'     => 'telegram.svg',
        't.me'            => 'telegram.svg',
        'whatsapp.com'    => 'whatsapp.svg',
        'wa.me'           => 'whatsapp.svg',
    ],
];
