<?php

namespace classes\Controllers;

use lib\Controller\BaseController;
use lib\Session;
use lib\Uploader\FileUploader;

class AppController Extends BaseController
{
    const HOME_URI = 'index.php';

    protected $session   = null;
    protected $image_dir = FileUploader::UPLOAD_DIR_NAME . '/bulletin';

    public function setUp()
    {
        parent::setUp();

        $this->session = Session::create();
    }
}
