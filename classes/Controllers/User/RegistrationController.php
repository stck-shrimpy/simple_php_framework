<?php

namespace classes\Controllers\User;

use classes\Controllers\User\UserController;
use classes\Storages\UsersStorage;

class RegistrationController extends UserController
{
    const EMAIL_SUBJECT = 'cho_bbs';
    const EMAIL_HEADER  = 'From: admin@cho_bbs.com';

    public function setUp()
    {
        parent::setUp();

        if ($this->is_logged_in) {
            $this->redirect(self::HOME_URI);
        }
    }

    public function showRegistrationForm()
    {
        $is_registered       = false;
        $is_register_confirm = false;

        $this->render('user/registration/registrationForm.php', get_defined_vars());
    }

    public function register()
    {
        $user_name = $this->getParam('user_name');
        $email     = $this->getParam('email');
        $password  = $this->getParam('password');

        $data = [
            'name'     => $user_name,
            'email'    => $email,
            'password' => $password,
        ];

        $is_registered       = false;
        $is_register_confirm = false;
        $users               = new UsersStorage();
        $errors              = $users->validate($data);

        if (empty($errors)) {
            $is_register_confirm = true;
            if ($this->getParam('do_register') === 'true') {
                $email_token = get_uniq_string(32);
                $verify_link = get_url('user/registration/emailTokenVerify.php', ['email_token' => $email_token]);
                $message     = "こんばんは! {$data['name']} \r\n リンクをクリックしてメールアドレスを認証してください。 \r\n" . $verify_link;

                $data['email_token'] = $email_token;
                $found_user          = $users->fetch(['*'], [['column' => 'email', 'condition' => '=', 'value' => $email]]);
                $should_send_email   = true;
                if (empty($found_user)) {
                    $users->insert($data);
                } elseif ($found_user['email_verified_at'] === null) {
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $users->update($data, [['column' => 'email', 'condition' => '=', 'value' => $email]]);
                } else {
                    $should_send_email = false;
                }

                if ($should_send_email) {
                    if (!mb_send_mail($data['email'], self::EMAIL_SUBJECT, $message, self::EMAIL_HEADER)) {
                        $this->log('mb_send_mail () メールが正しく送信されませんでした。', E_USER_NOTICE);
                    }
                }

                $is_registered = true;
            }
        }

        $this->render('user/registration/registrationForm.php', get_defined_vars());
    }

    public function emailTokenVerify()
    {
        $email_token = $this->getParam('email_token');
        if ($email_token === null) {
            $this->err400();
        }

        $errors            = [];
        $is_email_verified = false;
        $users             = new UsersStorage();
        $found_user        = $users->fetch(['*'], [['column' => 'email_token', 'condition' => '=', 'value' => $email_token]]);
        if (empty($found_user)) {
            $errors[] = 'リンクが正しくありません。再度会員登録を行ってください';
        } else {
            if ($found_user['email_verified_at'] !== null) {
                $errors[] = '既にメールアドレスが認証されています';
            } elseif ($users->isEmailTokenExpired($found_user)) {
                $errors[] = 'リンクの有効期限が切れています。再度会員登録を行ってください';
            } else {
                $is_email_verified = true;
            }
        }

        if ($is_email_verified) {
            $users->update(['email_verified_at' => date('Y-m-d H:i:s')], [['column' => 'id', 'condition' => '=', 'value' => $found_user['id']]]);
            $this->session->user = [
                'id'   => $found_user['id'],
                'name' => $found_user['name'],
            ];
        }

        $this->render('user/registration/emailToken.php', get_defined_vars());
    }
}
