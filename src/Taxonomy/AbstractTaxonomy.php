<?php

namespace PressHard\Taxonomy;

use PressHard\Core\WPEntity;

abstract class AbstractTaxonomy extends WPEntity
{
    /**
     * @var bool
     */
    protected $isEditable = true;

    /**
     * @var bool
     */
    protected $isHierarchical = true;

    /**
     * @var array
     */
    protected $terms = [];

    /**
     * @return string
     */
    abstract public function getPlural();

    /**
     * @return array
     */
    abstract public function getPostTypes();

    /**
     * @return string
     */
    abstract public function getSingular();

    /**
     * @return string|bool
     */
    public function getSlug()
    {
        return false;
    }

    /**
     * @return void
     */
    public function register()
    {
        if (!taxonomy_exists($this->getType())) {
            $pluralLower = strtolower($this->getPlural());

            $args = [
                'labels' => [
                    'name' => $this->getPlural(),
                    'singular_name' => $this->getSingular(),
                    'all_items' => 'All ' . $this->getPlural(),
                    'edit_item' => 'Edit ' . $this->getSingular(),
                    'view_item' => 'View ' . $this->getSingular(),
                    'update_item' => 'Update ' . $this->getSingular(),
                    'add_new_item' => 'Add New ' . $this->getSingular(),
                    'new_item_name' => 'New ' . $this->getSingular() . ' Name',
                    'parent_item' => 'Parent ' . $this->getSingular(),
                    'parent_item_colon' => 'Parent ' . $this->getSingular() . ':',
                    'search_items' => 'Search ' . $this->getPlural(),
                    'popular_items' => 'Popular ' . $this->getPlural(),
                    'separate_items_with_commas' => 'Separate ' . $pluralLower . ' with commas',
                    'add_or_remove_items' => 'Add or remove ' . $pluralLower,
                    'choose_from_most_used' => 'choose_from_most_used ' . $pluralLower,
                    'not_found' => 'No ' . $pluralLower . ' found'
                ],
                'hierarchical' => $this->isHierarchical,
                'show_admin_column' => true,
                'rewrite' => false
            ];

            if ($this->getSlug()) {
                $args['rewrite'] = [
                    'slug' => $this->getSlug(),
                    'with_front' => false
                ];
            }

            if (!$this->isEditable) {
                $args += [
                    'capabilities' => [
                        'assign_terms' => 'manage_options',
                        'edit_terms'   => 'god',
                        'manage_terms' => 'god',
                    ],
                    'show_in_nav_menu' => false
                ];
            }

            register_taxonomy(
                $this->getType(),
                $this->getPostTypes(),
                $args
            );
        }
    }

    /**
     * @return void
     */
    public function insertTerms()
    {
        foreach ($this->terms as $term) {
            $termEntity = get_term_by(
                'name',
                $term['name'],
                $this->getType(),
                ARRAY_A
            );

            if (!$termEntity) {
                wp_insert_term(
                    $term['name'],
                    $this->getType(),
                    ['slug' => $term['slug']]
                );
            }
        }
    }

    /**
     * @return string
     */
    public function getTermSlugRegex()
    {
        $termSlugs = array_map(function ($term) {
            return $term['slug'];
        }, $this->terms);

        return sprintf(
            '(%s)',
            implode('|', $termSlugs)
        );
    }
}
