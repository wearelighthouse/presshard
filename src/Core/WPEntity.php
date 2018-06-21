<?php

namespace PressHard\Core;

abstract class WPEntity extends Singleton
{
    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return void
     */
    public function create()
    {
    }

    /**
     * @return void
     */
    public function register()
    {
        add_action('cmb2_admin_init', [$this, 'registerMetaBoxes']);
    }

    /**
     * @return void
     */
    public function registerMetaBoxes()
    {
    }

    /**
     * @return CMB2 object
     */
    protected function createMetaBox($id, $title, $options = [])
    {
        $args = [
            'id' => $this->getPrefix() . $id,
            'title' => $title,
            'object_types' => [$this->getType()]
        ];

        return new_cmb2_box($args + $options);
    }

    /**
     * @return string
     */
    protected function addField($metaBox, $id, $name, $type, $options = [])
    {
        return $metaBox->add_field([
            'id' => $this->getPrefix() . $id,
            'name' => $name,
            'type' => $type
        ] + $options);
    }

    /**
     * @return void
     */
    protected function addGroupField($metaBox, $fieldId, $id, $name, $type, $options = [])
    {
        $metaBox->add_group_field(
            $fieldId,
            [
                'id' => $id,
                'name' => $name,
                'type' => $type
            ] + $options
        );
    }

    /**
     * @return mixed
     */
    public function getMetaValue($post, $id)
    {
        return $post->{$this->getMetaKey($id)};
    }

    /**
     * @return string
     */
    public function getMetaKey($id)
    {
        return $this->getPrefix() . $id;
    }

    /**
     * @return string
     */
    protected function getPrefix()
    {
        return sprintf(
            '_%s_',
            $this->getType()
        );
    }
}
