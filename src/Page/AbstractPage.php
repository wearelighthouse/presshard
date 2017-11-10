<?php

namespace PressHard\Page;

use PressHard\Core\WPEntity;

use Exception;

abstract class AbstractPage extends WPEntity
{
    /**
     * @var int
     */
    public $postId;

    /**
     * @return string
     */
    abstract public function getSlug();

    /**
     * @return string
     */
    abstract public function getTitle();

    /**
     * @return array
     */
    public function getChildPages()
    {
        return [];
    }

    /**
     * @return \PressHard\Page\AbstractPage
     */
    public function getParentPage()
    {
        return null;
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
     * @return string
     */
    public function getPrefix()
    {
        return sprintf(
            '_%s_%s_',
            str_replace('-', '_', $this->getSlug()),
            $this->getType()
        );
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'page';
    }

    /**
     * @return void
     */
    public function register()
    {
        $page = get_page_by_path($this->getPath());

        $this->postId = $page
            ? $page->ID
            : wp_insert_post([
                'post_title' => $this->getTitle(),
                'post_name' => $this->getSlug(),
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_parent' => $this->getParentPage()
                    ? $this->getParentPage()->postId
                    : 0
            ]);

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
}
