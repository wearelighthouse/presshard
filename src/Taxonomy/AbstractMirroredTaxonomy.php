<?php

namespace PressHard\Taxonomy;

abstract class AbstractMirroredTaxonomy extends AbstractTaxonomy
{
    /**
     * @return \PressHard\PostType\AbstractPostType
     */
    abstract public function getPostType();

    /**
     * @return string
     */
    public function getPlural()
    {
        return $this->getPostType()->getPlural();
    }

    /**
     * @return string
     */
    public function getSingular()
    {
        return $this->getPostType()->getSingular();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getPostType()->getMirroredTaxonomyType();
    }

    /**
     * @return string
     */
    public function getMirroredTaxonomyType()
    {
        return sprintf(
            '%s_mirrored',
            $this->getType()
        );
    }

    /**
     * @return void
     */
    public function addMirroredTaxonomyActions()
    {
        add_action('before_delete_post', [$this, 'deletePost']);
        add_action('save_post', [$this, 'mirrorPost']);
    }

    /**
     * @return void
     */
    public function deletePost($postId)
    {
        $post = get_post($postId);
        $term = get_term_by(
            'slug',
            str_replace('__trashed', '', $post->post_name),
            $this->getType(),
            ARRAY_A
        );
        wp_delete_term($term['term_id'], $this->getType());
    }

    /**
     * @return void
     */
    public function mirrorPost($postId)
    {
        $postType = get_post_type($postId);

        if ($postType !== $this->getType()) {
            return;
        }

        if ($parentId = wp_is_post_revision($postId)) {
            $postId = $parentId;
        }

        $post = get_post($postId);

        if ($post->post_status === 'auto-draft') {
            return;
        }

        $term = $this->getTerm($post);
        if ($term) {
            wp_set_post_terms($postId, [$term->term_id], $this->getType());
        }
    }

    /**
     * @return int
     */
    private function getParentTermId($post)
    {
        if ($post->post_parent === 0) {
            return 0;
        }

        $parent = get_post($post->post_parent);
        $parentTerm = get_term_by(
            'slug',
            $parent->post_name,
            $this->getType(),
            ARRAY_A
        );

        // Create new parent term
        if (!$parentTerm) {
            $parentTerm = wp_insert_term(
                $parent->post_title,
                $this->getType(),
                ['parent' => $this->getParentTermId($parent)]
            );
        }

        return $parentTerm['term_id'];
    }

    /**
     * @return array|bool
     */
    private function getTerm($post)
    {
        $term = get_term_by(
            'slug',
            $post->post_status === 'trash'
                ? str_replace('__trashed', '', $post->post_name)
                : $post->post_name,
            $this->getType(),
            ARRAY_A
        );

        if ($post->post_status !== 'publish') {
            if ($term) {
                wp_delete_term($term['term_id'], $this->getType());
            }

            return false;
        }

        // Update term
        if ($term) {
            return wp_update_term(
                $term['term_id'],
                $this->getType(),
                [
                    'name' => $post->post_title,
                    'slug' => $post->post_name,
                    'parent' => $this->getParentTermId($post)
                ]
            );
        }

        // Create new term
        return wp_insert_term(
            $post->post_title,
            $this->getType(),
            ['parent' => $this->getParentTermId($post)]
        );
    }
}
