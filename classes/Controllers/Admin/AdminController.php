<?php

namespace classes\Controllers\Admin;

use classes\Controllers\AppController;

class AdminController extends AppController
{
    protected $is_logged_in = false;

    public function setUp()
    {
        parent::setUp();

        if ($this->session->is_logged_in) {
            $this->is_logged_in = true;
        }
    }
}
