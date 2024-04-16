<?php

/**
 * Plugin Name: Controller Fields for Contact Form 7
 * Description: This plugin extends Contact Form 7 by adding controller form fields and a framework to hide/display any form content based on user interaction. Requires Contact Form 7.
 * Version: VERSION_PLACEHOLDER
 * Author: AuRise Creative
 * Author URI: https://aurisecreative.com/
 * Plugin URI: https://aurisecreative.com/TEXTDOMAIN_PLACEHOLDER/
 * License: GPL v3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 5.5
 * Requires PHP: 7.4
 * Text Domain: TEXTDOMAIN_PLACEHOLDER
 * Requires Plugins: contact-form-7
 *
 * @package AuRise\Plugin\PluginNamespace
 * @copyright Copyright (c) 2024 Tessa Watkins, AuRise Creative <https://aurisecreative.com>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

defined('ABSPATH') || exit; // Exit if accessed directly

// Define current version
define('CF7_CONTROLLERFIELDS_VERSION', 'VERSION_PLACEHOLDER');

// Define root directory
defined('CF7_CONTROLLERFIELDS_DIR') || define('CF7_CONTROLLERFIELDS_DIR', __DIR__);

// Define root file
defined('CF7_CONTROLLERFIELDS_FILE') || define('CF7_CONTROLLERFIELDS_FILE', __FILE__);

/**
 * Initialise Plugin
 *
 * @since 1.0.0
 *
 * @return void
 */
function au_cf7_cf_init()
{
    add_action('wpcf7_init', 'au_cf7_cf_add_controller_tags'); // Add custom form tags to CF7
    add_filter('au_cf7_cf_controller_shortcode_handler', 'au_cf7_cf_validation_filter', 10, 2); // Validate custom form tags
}
add_action('plugins_loaded', 'au_cf7_cf_init', 20);


/**
 * Add Custom Shortcodes to Contact Form 7
 *
 * @since 1.0.0
 *
 * @return void
 */
function au_cf7_cf_add_controller_tags()
{
    //Add the dynamic text and hidden form fields
    wpcf7_add_form_tag(
        array(
            'select_controller', 'select_controller*',
            'radio_controller', 'radio_controller*',
            'checkbox_controller', 'checkbox_controller*'
        ),
        'au_cf7_cf_controller_shortcode_handler', //Callback
        array('name-attr' => true) //Features
    );
}

/**
 * Form Tag Handler
 *
 * @param WPCF7_FormTag $tag
 *
 * @return string HTML output of the shortcode
 */
function au_cf7_cf_controller_shortcode_handler($tag)
{
    $tag = new WPCF7_FormTag($tag);
    if (empty($tag->name)) {
        return '';
    }

    //Validate
    $validation_error = wpcf7_get_validation_error($tag->name);

    //Configure classes
    $class = wpcf7_form_controls_class($tag->type, 'au-cf7-controller');
    if ($validation_error) {
        $class .= ' wpcf7-not-valid';
    }

    // Configure attributes
    $atts = array(
        'name' => $tag->name,
        'id' => $tag->get_id_option(),
        'class' => $tag->get_class_option($class),
        'tabindex' => $tag->get_option('tabindex', 'int', true),
        'aria-invalid' => $validation_error ? 'true' : 'false',
        'aria-expanded' => 'false',
        'title' => __('Click to expand', 'TEXTDOMAIN_PLACEHOLDER'),
        'data-collapse' => __('Click to collapse', 'TEXTDOMAIN_PLACEHOLDER')
    );
    // Every controller needs a unique ID for JS to work
    if (empty($atts['id'])) {
        $atts['id'] = uniqid($atts['name'] . '-'); // Generate unique ID if it doesn't exist
    }
    if ($tag->has_option('readonly')) {
        $atts['readonly'] = 'readonly';
    }
    if ($tag->has_option('disabled')) {
        $atts['disabled'] = 'disabled';
    }
    if ($tag->is_required()) {
        $atts['aria-required'] = 'true';
        $atts['required'] = 'required';
    }

    // Get value
    $value = sanitize_text_field(au_cf7_cf_get_dynamic(null, $tag));

    // Identify placeholder
    if ($tag->has_option('placeholder') || $tag->has_option('watermark')) {
        //Reverse engineer what JS did (converted quotes to HTML entities --> URL encode) then sanitize
        $placeholder = au_cf7_cf_get_dynamic(sanitize_text_field(html_entity_decode(urldecode(implode('', (array)$tag->get_option('placeholder'))), ENT_QUOTES)));
        if ($placeholder) {
            //If a different placeholder text has been specified, set both attributes
            $atts['placeholder'] = $placeholder;
            $atts['value'] = $value;
        } else {
            //Default behavior of using the value as the placeholder
            $atts['placeholder'] = $value;
        }
    } else {
        // Otherwise just set the value as value
        $atts['value'] = $value;
    }

    // Get options
    $options = array();
    $pipes = $tag->pipes->to_array();
    if (count($pipes)) {
        foreach ($pipes as $pipe) {
            $key = sanitize_text_field(strval($pipe[0]));
            $value = sanitize_text_field(strval($pipe[1]));
            if ($key && $value) {
                $options[$key] = $value;
            }
        }
    }

    //Output the HTML
    switch ($tag->basetype) {
        case 'checkbox_controller': // checkboxes
        case 'radio_controller': // radio buttons
            if ($count = count($options)) {
                $reverse = in_array('label_first', $tag->options);
                $label_ui = in_array('use_label_element', $tag->options);
                $exclusive = in_array('exclusive', $tag->options);
                // Loop all the options
                $input_html = array();
                $i = 1;
                foreach ($options as $value => $label) {
                    $input_html[] = sprintf(
                        '<span class="wpcf7-list-item wpcf7-list-item-%s%s%s%s">%s</span>',
                        esc_attr($i),
                        $i === 1 ? ' first' : '',
                        $i === $count ? ' last' : '',
                        $exclusive ? ' wpcf7-exclusive-checkbox' : '',
                        au_cf7_cf_checkbox_html(
                            array_merge($atts, array(
                                'type' => str_replace('_controller', '', $tag->basetype), // checkbox or radio
                                'name' => $tag->basetype == 'radio_controller' || $exclusive || $count === 1 ? $atts['name'] : $atts['name'] . '[]', // if there are multiple checkboxes and they aren't exclusive, names are array
                                'id' => $atts['id'] . ($count > 1 ? '-' . $i : ''), // Every controller needs a unique ID
                                'data-id' => $atts['id'], // Include just in case
                                'value' => $atts['value'] && $value == $atts['value'] // Send true/false value
                            )),
                            $label,
                            $label_ui,
                            $reverse,
                            false
                        )
                    );
                    $i++;
                }
                $input_html = implode('', $input_html);
            }
            return sprintf(
                '<span class="wpcf7-form-control-wrap %s">%s%s</span>',
                sanitize_html_class($tag->name),
                $input_html,
                $validation_error
            );
        default: // select field
            return sprintf(
                '<span class="wpcf7-form-control-wrap %s">%s%s</span>',
                sanitize_html_class($tag->name),
                au_cf7_cf_select_html($atts, $options, false),
                $validation_error
            );
    }
}

/**
 *  Validate Required Controllers
 *
 * @param mixed $result
 * @param WPCF7_FormTag $tag
 *
 * @return mixed
 */
function au_cf7_cf_validation_filter($result, $tag)
{
    $tag = new WPCF7_FormTag($tag);

    //Sanitize value
    $value = empty($_POST[$tag->name]) ? '' : sanitize_text_field(trim(strval($_POST[$tag->name])));

    //Validate
    if (in_array($tag->basetype, array('select_controller', 'select_radio', 'select_checkbox'))) {
        if ($tag->is_required() && empty($value)) {
            $result->invalidate($tag, wpcf7_get_message('invalid_required'));
        }
    }
    return $result;
}

/**
 * Admin Scripts and Styles
 *
 * Enqueue scripts and styles to be used on the admin pages
 *
 * @since 3.1.0
 *
 * @param string $hook Hook suffix for the current admin page
 */
function au_cf7_cf_enqueue_assets()
{
    $cf7_handle = 'contact-form-7';
    $cf_handle = 'controller-fields-for-contact-form-7';
    // Only load our assets where CF7 assets are loaded
    if ((wp_script_is($cf7_handle, 'registered') || wp_script_is($cf7_handle, 'enqueued') || wp_script_is($cf7_handle, 'queue') || wp_script_is($cf7_handle, 'done') || wp_script_is($cf7_handle, 'to_do')) && !wp_script_is($cf_handle, 'queue')) {
        $minify = defined('SCRIPT_DEBUG') && constant('SCRIPT_DEBUG') ? '' : 'min';
        $url = plugin_dir_url(CF7_CONTROLLERFIELDS_FILE);
        $path = plugin_dir_path(CF7_CONTROLLERFIELDS_FILE);

        wp_enqueue_style(
            $cf_handle, //Handle
            $url . "assets/styles/controllable-fields{$minify}.css", //Source
            array(), //Dependencies
            $minify ? CF7_CONTROLLERFIELDS_VERSION : @filemtime($path . "assets/styles/controllable-fields{$minify}.css") //Version
        );

        //Plugin Scripts
        wp_enqueue_script(
            $cf_handle, //Handle
            $url . "assets/scripts/controllable-fields{$minify}.js", //Source
            array('jquery-core', $cf7_handle), //Dependencies
            $minify ? CF7_CONTROLLERFIELDS_VERSION : @filemtime($path . "assets/scripts/controllable-fields{$minify}.js"), //Version
            array('in_footer' => true, 'strategy' => 'defer') // Defer load in footer
        );
    }
}
add_action('wp_enqueue_scripts', 'au_cf7_cf_enqueue_assets', 20, 0);

/**
 * Include Utility Functions
 */
include_once(CF7_CONTROLLERFIELDS_DIR . '/includes/utilities.php');

if (is_admin()) {
    /**
     * Include the Admin Stuff
     */
    include_once(CF7_CONTROLLERFIELDS_DIR . '/includes/admin.php');
}
