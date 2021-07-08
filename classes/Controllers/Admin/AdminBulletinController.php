<?php

namespace classes\Controllers\Admin;

use lib\Paginator;
use lib\Uploader\ImageUploader;
use classes\Controllers\Admin\AdminController;
use classes\Storages\BulletinStorage;

class AdminBulletinController extends AdminController
{
    const ITEM_COUNT_PER_PAGE = 20;
    const LINK_COUNT          = 10;

    public function setUp()
    {
        parent::setUp();

        if (!$this->is_logged_in) {
            $this->redirect(self::HOME_URI);
        }
    }

    public function index()
    {
        $conditions = [];

        $title = $this->getParam('title');
        if ($title !== null) {
            $conditions[] = ['column' => 'title', 'condition' => 'LIKE', 'value' => "%{$title}%"];
        }

        $content = $this->getParam('content');
        if ($content !== null) {
            $conditions[] = ['column' => 'title', 'condition' => 'LIKE', 'value' => "%{$content}%"];
        }

        $image_option = $this->getParam('image_option');

        if ($image_option === 'with') {
            $conditions[] = ['column' => 'image', 'condition' => 'IS NOT NULL'];
        } elseif ($image_option === 'without') {
            $conditions[] = ['column' => 'image', 'condition' => 'IS NULL'];
        }

        $status_option = $this->getParam('status_option');
        if ($status_option === 'available') {
            $conditions[] = ['column' => 'deleted_at', 'condition' => 'IS NULL'];
        } elseif ($status_option === 'deleted') {
            $conditions[] = ['column' => 'deleted_at', 'condition' => 'IS NOT NULL'];
        }

        $bulletin  = new BulletinStorage();
        $paginator = $this->createPaginator($bulletin->count($conditions));
        $posts     = $bulletin->select(
            ['*'],
            $conditions,
            [
                ['column' => 'created_at', 'sort' => 'desc'],
            ],
            $paginator->getItemCountPerPage(),
            $paginator->getOffset(),
        );

        $this->session->redirect_params = $this->getQueryParams();

        $this->render('admin/bulletin/index.php', get_defined_vars());
    }

    public function recover()
    {
        $post_id  = $this->getParam('post_id');

        $bulletin = new BulletinStorage();
        $bulletin->update(['deleted_at' => null], [['column' => 'id', 'condition' => '=', 'value' => $post_id]]);

        $this->redirect('admin/bulletin/index.php', $this->getRedirectParams());
    }

    public function delete()
    {
        $post_ids = $this->getParam('post_ids');

        $bulletin = new BulletinStorage();
        foreach ($post_ids as $post_id) {
            $post = $bulletin->selectById($post_id);
            if ($post['image'] !== null) {
                $this->createImageUploader()->delete($post['image'], true);
            }

            $bulletin->softDeleteById($post_id);
        }

        $this->redirect('admin/bulletin/index.php', $this->getRedirectParams());
    }

    public function deleteImage()
    {
        $post_id = $this->getParam('post_id');

        $bulletin = new BulletinStorage();
        $post = $bulletin->selectById($post_id);
        if ($post['image'] !== null) {
            $this->createImageUploader()->delete($post['image'], true);
            $bulletin->update(['image' => null], [['column' => 'id', 'condition' => '=', 'value' => $post_id]]);
        }

        $this->redirect('admin/bulletin/index.php', $this->getRedirectParams());
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

    protected function getRedirectParams()
    {
        $redirect_params = $this->session->redirect_params;
        $this->session->unset('redirect_params');

        return $redirect_params;
    }
}
