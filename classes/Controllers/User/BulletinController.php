<?php

namespace classes\Controllers\User;

use lib\Paginator;
use lib\Uploader\ImageUploader;
use classes\Controllers\User\UserController;
use classes\Storages\BulletinStorage;

class BulletinController extends UserController
{
    const ITEM_COUNT_PER_PAGE = 10;
    const LINK_COUNT          = 5;

    public function index()
    {
        $name                = ($this->is_logged_in) ? $this->user['name'] : null;
        [$paginator, $posts] = $this->getBulletin();

        $this->render('user/bulletin/index.php', get_defined_vars());
    }

    public function create()
    {
        $name    = $this->getParam('name');
        $title   = $this->getParam('title');
        $content = $this->getParam('content');

        $data = [
            'name'    => $name,
            'title'   => $title,
            'content' => $content,
        ];

        if (!$this->is_logged_in) {
            $data['password'] = $this->getParam('password');
        }

        $bulletin       = new BulletinStorage();
        $errors         = $bulletin->validate($data);
        $image          = $this->getFile('image');
        $uploader_image = $this->createImageUploader();
        $has_image      = !empty($image);
        if ($has_image) {
            $errors = array_merge($errors, $uploader_image->validate($image, 'image'));
        }

        if (empty($errors)) {
            if ($has_image) {
                $data['image'] = $uploader_image->save($image['data'], $image['name']);
            } else {
                $data['image'] = null;
            }

            if ($this->is_logged_in) {
                $data['user_id'] = $this->user['id'];
            }

            $bulletin->insert($data);

            $this->redirect(self::HOME_URI);
        } else {
            [$paginator, $posts] = $this->getBulletin();

            $this->render('user/bulletin/index.php', get_defined_vars());
        }
    }

    public function edit()
    {
        $id       = $this->getParam('post_id');
        $password = $this->getParam('password');
        $page     = $this->getParam('page');
        if ($id === null) {
            $this->err400();
        }

        $bulletin = new BulletinStorage();
        $post     = $bulletin->selectById($id);
        if (empty($post)) {
            $this->err404();
        }

        if ($this->is_logged_in && $this->user['id'] !== $post['user_id']) {
            $this->err400();
        }

        $name          = $post['name'];
        $title         = $post['title'];
        $content       = $post['content'];
        $current_image = $post['image'];

        $errors            = [];
        $image             = $this->getFile('image');
        $do_delete_image   = ($this->getParam('do_delete_image') === 'true');
        $has_new_image     = !empty($image);
        $password_verified = false;
        if ($this->is_logged_in) {
            $password_verified = true;
        } else {
            if ($post['password'] === null) {
                $errors[] = 'パスワードが設定されていないため修正できません！';
            } else {
                $password_verified = $bulletin->passwordVerify($password, $post['password']);
                if (!$password_verified) {
                    $errors[] = 'パスワードが間違っています！再度入力してください';
                }
            }
        }

        if ($password_verified) {
            if ($this->getParam('do_edit') === 'true') {
                $name    = $this->getParam('name');
                $title   = $this->getParam('title');
                $content = $this->getParam('content');

                $data = [
                    'name'    => $name,
                    'title'   => $title,
                    'content' => $content,
                ];

                $bulletin       = new BulletinStorage();
                $errors         = $bulletin->validate($data);
                $uploader_image = $this->createImageUploader();
                if (!$do_delete_image && $has_new_image) {
                    $errors = array_merge($errors, $uploader_image->validate($image, 'image'));
                }

                if (empty($errors)) {
                    if ($do_delete_image) {
                        if (!empty($current_image)) {
                            $uploader_image->delete($current_image, true);
                        }

                        $data['image'] = null;
                    } elseif ($has_new_image) {
                        if (!empty($current_image)) {
                            $uploader_image->delete($current_image, true);
                        }

                        $data['image'] = $uploader_image->save($image['data'], $image['name']);
                    }

                    $bulletin->updateById($id, $data);

                    $this->redirect(self::HOME_URI, ['page' => $page]);
                }
            }
        }

        $this->render('user/bulletin/edit.php', get_defined_vars());
    }

    public function delete()
    {
        $id       = $this->getParam('post_id');
        $password = $this->getParam('password');
        $page     = $this->getParam('page');
        if ($id === null) {
            $this->err400();
        }

        $bulletin = new BulletinStorage();
        $post     = $bulletin->selectById($id);
        if (empty($post)) {
            $this->err404();
        }

        if ($this->is_logged_in && $this->user['id'] !== $post['user_id']) {
            $this->err400();
        }

        $errors            = [];
        $password_verified = false;
        if ($this->is_logged_in) {
            $password_verified = true;
        } else {
            if ($post['password'] === null) {
                $errors[] = 'パスワードが設定されていないため修正できません！';
            } else {
                $password_verified = $bulletin->passwordVerify($password, $post['password']);
                if (!$password_verified) {
                    $errors[] = 'パスワードが間違っています！再度入力してください';
                }
            }
        }

        if ($password_verified) {
            if ($this->getParam('do_delete') === 'true') {
                if ($post['image'] !== null) {
                    $this->createImageUploader()->delete($post['image'], true);
                }

                $bulletin->softDeleteById($id);

                $this->redirect(self::HOME_URI, ['page' => $page]);
            }
        }

        $this->render('user/bulletin/delete.php', get_defined_vars());
    }

    protected function getBulletin(): array
    {
        $bulletin  = new BulletinStorage();
        $paginator = $this->createPaginator($bulletin->count([['column' => 'deleted_at', 'condition' => 'IS NULL']]));
        $posts     = $bulletin->select(
            ['*'],
            [
                ['column' => 'deleted_at', 'condition' => "IS NULL"],
            ],
            [
                ['column' => 'created_at', 'sort' => 'desc'],
            ],
            $paginator->getItemCountPerPage(),
            $paginator->getOffset(),
        );

        return [$paginator, $posts];
    }

    protected function createPaginator($item_count)
    {
        $paginator = new Paginator($item_count);
        $paginator->setItemCountPerPage(self::ITEM_COUNT_PER_PAGE);
        $paginator->setLinkCount(self::LINK_COUNT);
        $paginator->setUri($this->getEnv('Request-Uri'));
        $paginator->setCurrentPage($this->getParam('page'));

        return $paginator;
    }

    protected function createImageUploader()
    {
        return new ImageUploader($this->image_dir);
    }
}
