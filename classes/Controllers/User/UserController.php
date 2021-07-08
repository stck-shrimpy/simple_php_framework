<?php

namespace classes\Controllers\User;

use classes\Controllers\AppController;

class UserController extends AppController
{
    protected $user         = [];
    protected $is_logged_in = false;

    public function setUp()
    {
        parent::setUp();

        if (!empty($this->session->user)) {
            $this->user = [
                'id'   => $this->session->user['id'],
                'name' => $this->session->user['name'],
            ];

            $this->is_logged_in = true;
        }
    }
}
