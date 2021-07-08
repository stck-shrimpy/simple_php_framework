<?php

namespace classes\Controllers\User;

use classes\Controllers\User\UserController;
use classes\Storages\UsersStorage;

class UserAuthController extends UserController
{
    public function showLoginForm()
    {
        if ($this->is_logged_in) {
            $this->redirect(self::HOME_URI);
        }

        $this->render('user/auth/loginForm.php', get_defined_vars());
    }

    public function login()
    {
        if ($this->is_logged_in) {
            $this->redirect(self::HOME_URI);
        }

        $email    = $this->getParam('email');
        $password = $this->getParam('password');

        $errors     = [];
        $users      = new UsersStorage();
        $found_user = $users->fetch(['*'], [['column' => 'email', 'condition' => '=', 'value' => $email]]);
        if (empty($found_user)) {
            $errors[] = 'メールアドレスもしくはパスワードが間違っています';
        } elseif ($found_user['email_verified_at'] === null || !$users->passwordVerify($password, $found_user['password'])) {
            $errors[] = 'メールアドレスもしくはパスワードが間違っています';
        }

        if (empty($errors)) {
            $this->session->user = [
                'id'   => $found_user['id'],
                'name' => $found_user['name'],
            ];

            $this->redirect('index.php');
        }

        $this->render('user/auth/loginForm.php', get_defined_vars());
    }

    public function logout()
    {
        $this->session->unset('user');

        $this->redirect(self::HOME_URI);
    }
}
