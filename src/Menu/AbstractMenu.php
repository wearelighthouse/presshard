<?php

namespace PressHard\Menu;

use PressHard\Page\AbstractPage;
use PressHard\Core\WPEntity;

abstract class AbstractMenu extends WPEntity
{
    /**
     * @return string
     */
    abstract public function getLocation();

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @return array
     */
    abstract public function getTree();

    /**
     * @return string
     */
    public function getType()
    {
        return 'nav_menu';
    }

    /**
     * @return void
     */
    public function register()
    {
        if (!is_nav_menu($this->getName())) {
            $menuId = wp_create_nav_menu($this->getName());
            $this->createItems($menuId, $this->getTree());

            register_nav_menu($this->getLocation(), $this->getName());

            $locations = get_nav_menu_locations();
            foreach (array_keys($locations) as $id) {
                if ($id === $this->getLocation()) {
                    $locations[$id] = $menuId;
                }
            }
            set_theme_mod('nav_menu_locations', $locations);
        }
    }

    /**
     * @return void
     */
    protected function pageMenuItem(AbstractPage $page)
    {
        return [
            'menu-item-object-id' => $page->postId,
            'menu-item-object' => $page->getType(),
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish'
        ];
    }

    /**
     * @return void
     */
    private function createItems($menuId, array $items, $parentId = 0)
    {
        foreach ($items as $item) {
            $item['menu-item-parent-id'] = $parentId;
            $itemId = wp_update_nav_menu_item($menuId, 0, $item);

            if (isset($item['children'])) {
                $this->createItems($menuId, $item['children'], $itemId);
            }
        }
    }
}
