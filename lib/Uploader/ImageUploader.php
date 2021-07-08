<?php

namespace lib\Uploader;

class ImageUploader extends FileUploader
{
    protected $dir_path;
    protected $validation_params = [
        'name'  => '画像',
        'rules' => [
            'file_size'       => 1048576,
            'file_mimes'      => ['image/jpeg', 'image/png', 'image/gif'],
            'file_extensions' => ['jpeg', 'jpg', 'png', 'gif'],
        ],
    ];
}
