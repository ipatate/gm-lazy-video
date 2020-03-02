<?php

/**
 * Plugin Name: GM Lazy Video native
 * Description: Add lazy loading on native video from gutenberg
 * Version:     0.0.1
 * Author:      Faramaz Pat <info@goodmotion.fr>
 * License:     MIT License
 * Domain Path: /languages
 * Text Domain: gm-lazy-video
 **/

namespace Goodmotion\Lazy\Video;

require_once(dirname(__FILE__) . '/helpers/simplehtmldom/simple_html_dom.php');


defined('ABSPATH') || exit;

/**
 * test override embedded block
 */
function core_video_render($attributes, $content)
{
    $html = new \simple_html_dom();
    $html->load($content);
    // find video tag
    $container = $html->find('video');

    if (array_key_exists(0, $container)) {

        $src = $container[0]->getAttribute('src');
        $container[0]->setAttribute('data-src', $src);
        $container[0]->removeAttribute('src');
        // add class
        $class = $container[0]->getAttribute('class');
        $container[0]->setAttribute('class', $class . ' b-lazy');

        // search source tag if exist
        $source = $html->find('source');
        // pass src attribute to data-src
        foreach ($source as $key => $value) {
            $src = $value->getAttribute('src');
            $value->setAttribute('data-src', $src);
            $value->removeAttribute('src');
        }
    }

    return $html->save();
}

/**
 * override block core/video
 */
function replace_core_video_render()
{
    register_block_type('core/video', array(
        'render_callback' => __NAMESPACE__ . '\core_video_render',
    ));
}


/**
 * add script to front
 */
function frontend_scripts()
{
    if (has_block('core/video')) {
        wp_enqueue_script(
            'gm-lazy-video-blazy',
            plugins_url('js/blazy.min.js', __FILE__),
            filemtime(plugin_dir_path(__FILE__) . 'js/blazy.min.js')
        );
        wp_enqueue_script(
            'gm-lazy-video-front',
            plugins_url('js/index.js', __FILE__),
            filemtime(plugin_dir_path(__FILE__) . 'js/index.js')
        );
    }
}

add_action('wp_enqueue_scripts', __NAMESPACE__ . '\frontend_scripts');
add_action('init', __NAMESPACE__ . '\replace_core_video_render');
