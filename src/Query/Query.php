<?php

namespace PressHard\Query;

class Query
{
    /**
     * @return array
     */
    public static function all($args = [], $isMainLoop = false)
    {
        $args = $args + ['posts_per_page' => -1];

        if ($isMainLoop) {
            return query_posts($args);
        }

        return get_posts($args);
    }

    /**
     * @return array
     */
    public static function list($args = [])
    {
        $posts = static::all($args + ['orderby' => 'title']);

        return static::generateList($posts);
    }

    /**
     * @return array
     */
    protected static function generateList(array $posts)
    {
        $list = [];

        foreach ($posts as $post) {
            $list[$post->ID] = $post->post_title;
        }

        return $list;
    }
}
