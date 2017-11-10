<?php

namespace PressHard\Query;

class Query
{
    /**
     * @return array
     */
    public static function all($args = [])
    {
        $args = [
            'posts_per_page' => -1
        ] + $args;

        return get_posts($args);
    }

    /**
     * @return array
     */
    public static function list($args = [])
    {
        $posts = Query::all(['orderby' => 'title']);

        return self::generateList($posts);
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
