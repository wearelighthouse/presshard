<?php

namespace BBPA\Page;

use BBPA\Core\WPEntity;

use Exception;

abstract class AbstractPage extends WPEntity
{
    /**
     * @return string
     */
    abstract public function getSlug();

    /**
     * @return string
     */
    abstract protected function getTitle();

    /**
     * @return string
     */
    public function getType()
    {
        return 'page';
    }

    /**
     * @return array
     */
    protected function getChildPages()
    {
        return [];
    }

    /**
     * @return \BBPA\Page\AbstractPage
     */
    protected function getParentPage()
    {
        return null;
    }

    /**
     * @return string
     */
    protected function getPrefix()
    {
        return sprintf(
            '_%s_%s_',
            str_replace('-', '_', $this->getSlug()),
            $this->getType()
        );
    }

    /**
     * @return stirng
     */
    public function getPath()
    {
        if (!$this->getParentPage()) {
            return $this->getSlug();
        }

        return sprintf(
            '%s/%s',
            $this->getParentPage()->getPath(),
            $this->getSlug()
        );
    }

    /**
     * @return void
     */
    public function create()
    {
        $this->createPost();

        foreach ($this->getChildPages() as $childPage) {
            if (!$childPage->getParentPage() ||
                get_class($childPage->getParentPage()) !== get_class($this)
            ) {
                throw new Exception(sprintf(
                    'getParentPage must return an instance of %s',
                    get_class($this)
                ));
            }

            $childPage->create();
        }
    }

    /**
     * @return void
     */
    public function register()
    {
        parent::register();

        foreach ($this->getChildPages() as $childPage) {
            if (!$childPage->getParentPage() ||
                get_class($childPage->getParentPage()) !== get_class($this)
            ) {
                throw new Exception(sprintf(
                    'getParentPage must return an instance of %s',
                    get_class($this)
                ));
            }

            $childPage->register();
        }
    }

    /**
     * @return CMB2 object
     */
    protected function createMetaBox($id, $title, $options = [])
    {
        return parent::createMetabox(
            str_replace('-', '_', $this->getSlug()) . '_' . $id,
            $title,
            ['show_on' => ['key' => 'id', 'value' => [$this->postId]]] + $options
        );
    }

    /**
     * @return int
     */
    public function createPost()
    {
        $postId = get_option($this->getPrefix() . 'id', false);

        if (!$postId) {
            $page = get_page_by_path($this->getPath());

            if ($page) {
                $postId = $page->ID;

                update_option($this->getPrefix() . 'id', $postId);
            }
        }

        if (!$postId) {
            $postId = wp_insert_post([
                'post_title' => $this->getTitle(),
                'post_name' => $this->getSlug(),
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_parent' => $this->getParentPage()
                    ? $this->getParentPage()->createPost()
                    : 0
            ]);
        }

        return $postId;
    }
}
