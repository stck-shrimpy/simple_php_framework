<?php

namespace classes\Controllers\Admin;

use classes\Controllers\Admin\AdminController;
use classes\Storages\AdminsStorage;

class AdminAuthController extends AdminController
{
    public function showLoginForm()
    {
        if ($this->is_logged_in) {
            $this->redirect('admin/bulletin/search.php');
        }

        $this->render('admin/auth/loginForm.php', get_defined_vars());
    }

    public function login()
    {
        if ($this->is_logged_in) {
            $this->redirect('admin/bulletin/index.php');
        }

        $login_id = $this->getParam('login_id');
        $password = $this->getParam('password');

        $errors = [];
        $admins = new AdminsStorage();
        $admin  = $admins->fetch(['*'], [['column' => 'login_id', 'condition' => '=', 'value' => $login_id]]);
        if (empty($admin)) {
            $errors[] = 'ログイン情報が正しくありません';
        } elseif (!$admins->passwordVerify($password, $admin['password'])) {
            $errors[] = 'ログイン情報が正しくありません';
        }

        if (empty($errors)) {
            $this->session->is_logged_in = true;

            $this->redirect('admin/bulletin/index.php');
        }

        $this->render('admin/auth/loginForm.php', get_defined_vars());
    }

    public function logout()
    {
        $this->session->unset('is_logged_in');

        $this->redirect(self::HOME_URI);
    }
}
