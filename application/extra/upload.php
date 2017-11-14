<?php

return [
    //上传大小限制，单位字节
    'upload_size_limit' => [
        'face'     => 524288,
        'image'    => 1048576,
        'attach'   => 524288000,
        'document' => 5242880,
        'video'    => 209715200,
        'audio'    => 5242880,
    ],
    //上传允许的文件格式
    'upload_type_limit' => [
        'face'     => 'jpg,png,jpeg',
        'image'    => 'jpg,png,gif,jpeg',
        'attach'   => 'zip,rar,tar.gz,apk',
        'document' => 'xls,xlsx,doc,docx,ppt,pptx',
        'video'    => 'mp4',
        'audio'    => 'mp3',
    ],

    //上传的位置,也可以是/home/file定义
    'upload_path'       => ROOT_PATH . 'public/uploads',

    /**
     * 七牛云,目前七牛云，也可以自己搭建文件服务器
     */
    'AccessKey'         => 'QY9AgTB1DBPrgugun0DLZ0hM7VO0eJT5_lupBYi8',
    'SecretKey'         => '9JcQU8z3fCH_kJdumtqELnZKXyod_Ea7I2vM82oS',

    'UploadUrl' => 'http://up-z1.qiniu.com',

    'bucket' => [
        'file'  => [
            'host'    => 'http://oxwhk1gk1.bkt.clouddn.com',
            'private' => false
        ],
        'audio' => [
            'host'    => 'http://oxwhws9hg.bkt.clouddn.com',
            'private' => true
        ],
    ]

];