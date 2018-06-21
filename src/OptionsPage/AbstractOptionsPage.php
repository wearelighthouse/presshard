<?php

namespace PressHard\OptionsPage;

use PressHard\Core\Singleton;

abstract class AbstractOptionsPage extends Singleton
{
    /**
     * @var CMB2 object
     */
    private $metaBox = null;

    /**
     * @return string
     */
    abstract protected function getKey();

    /**
     * @return string
     */
    abstract protected function getTitle();

    /**
     * @return void
     */
    abstract protected function registerFields();

    /**
     * @return string
     */
    public function getType()
    {
        return 'options-page';
    }

    /**
     * @return string
     */
    protected function getPrefix()
    {
        return sprintf(
            '_%s_%s_',
            $this->getKey(),
            $this->getType()
        );
    }

    /**
     * @return void
     */
    public function register()
    {
        add_action('cmb2_admin_init', function () {
            $this->metaBox = new_cmb2_box([
                'id' => $this->getPrefix(),
                'title' => $this->getTitle(),
                'object_types' => [$this->getType()],
                'option_key' => $this->getKey()
            ]);

            $this->registerFields();
        });
    }

    /**
     * @return mixed
     */
    public function getOption($id = '', $default = false)
    {
        return cmb2_get_option($this->getKey(), $this->getPrefix() . $id, $default);
    }

    /**
     * @return string
     */
    protected function addField($id, $name, $type, $options = [])
    {
        return $this->metaBox->add_field([
            'id' => $this->getPrefix() . $id,
            'name' => $name,
            'type' => $type
        ] + $options);
    }
}
