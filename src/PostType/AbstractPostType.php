<?php

namespace PressHard\PostType;

use PressHard\Core\WPEntity;

abstract class AbstractPostType extends WPEntity
{
    /**
     * @var bool
     */
    protected $hasArchive = true;

    /**
     * @var bool
     */
    protected $isHierarchical = false;

    /**
     * @var array
     */
    protected $supports = [
        'title',
        'editor',
        'thumbnail'
    ];

    /**
     * @return string
     */
    abstract public function getPlural();

    /**
     * @return string
     */
    abstract public function getSingular();

    /**
     * @return string
     */
    abstract public function getSlug();

    /**
     * @return void
     */
    public function register()
    {
        if (!post_type_exists($this->getType())) {
            register_post_type($this->getType(), [
                'labels' => [
                    'name' => $this->getPlural(),
                    'singular_name' => $this->getSingular(),
                    'menu_name' => $this->getPlural(),
                    'add_new' => 'Add New',
                    'add_new_item' => 'Add New ' . $this->getSingular(),
                    'edit' => 'Edit',
                    'edit_item' => 'Edit ' . $this->getSingular(),
                    'new_item' => 'New ' . $this->getSingular(),
                    'view' => 'View ' . $this->getSingular(),
                    'view_item' => 'View ' . $this->getSingular(),
                    'search_items' => 'Search ' . $this->getPlural(),
                    'not_found' => 'No ' . $this->getPlural() . ' Found',
                    'not_found_in_trash' => 'No ' . $this->getPlural() . ' Found in Trash',
                    'parent' => 'Parent ' . $this->getSingular(),
                ],
                'public' => true,
                'hierarchical' => $this->isHierarchical,
                'supports' => $this->supports,
                'has_archive' => $this->hasArchive,
                'rewrite' => [
                    'slug' => $this->getSlug(),
                    'with_front' => false
                ]
            ]);
        }

        parent::register();
    }
}
