<?php

defined('ABSPATH') || exit; // Exit if accessed directly

/**
 * Admin Scripts and Styles
 *
 * Enqueue scripts and styles to be used on the admin pages
 *
 * @since 3.1.0
 *
 * @param string $hook Hook suffix for the current admin page
 */
function au_cf7_cf_enqueue_admin_assets($hook)
{
    //Only load on CF7 Form pages
    if ($hook == 'toplevel_page_wpcf7') {
        $prefix = 'au-cf7-controllable-fields';
        $url = plugin_dir_url(CF7_CONTROLLERFIELDS_FILE);
        $path = plugin_dir_path(CF7_CONTROLLERFIELDS_FILE);

        wp_enqueue_style(
            $prefix . 'admin', //Handle
            $url . 'assets/styles/tag-generator.css', //Source
            array('contact-form-7-admin'), //Dependencies
            defined('WP_DEBUG') && constant('WP_DEBUG') ? @filemtime($path . 'assets/styles/tag-generator.css') : CF7_CONTROLLERFIELDS_VERSION //Version
        );

        //Plugin Scripts
        wp_enqueue_script(
            $prefix . 'taggenerator', //Handle
            $url . 'assets/scripts/tag-generator.js', //Source
            array('jquery', 'wpcf7-admin-taggenerator'), //Dependencies
            defined('WP_DEBUG') && constant('WP_DEBUG') ? @filemtime($path . 'assets/scripts/tag-generator.js') : CF7_CONTROLLERFIELDS_VERSION, //Version
            true //In footer
        );
    }
}
add_action('admin_enqueue_scripts', 'au_cf7_cf_enqueue_admin_assets');

/**
 * Create Tag Generators
 *
 * @return void
 */
function au_cf7_cf_add_tag_generators()
{
    if (!class_exists('WPCF7_TagGenerator')) {
        return;
    }
    $tag_generator = WPCF7_TagGenerator::get_instance();

    $controller_callback = 'au_cf7_cf_tag_generator_controller';
    $controller_options = array('default', 'defaultvalue', 'placeholder', 'readonly', 'disabled', 'required', 'label_first', 'use_label_element');

    // Controller Fields
    $tag_generator->add(
        'select_controller', // id
        __('select controller', 'controller-fields-for-contact-form-7'), // title
        $controller_callback, //callback
        $controller_options // options
    );

    $tag_generator->add(
        'checkbox_controller', // id
        __('checkbox controller', 'controller-fields-for-contact-form-7'), // title
        $controller_callback, //callback
        $controller_options // options
    );

    $tag_generator->add(
        'radio_controller', // id
        __('radio controller', 'controller-fields-for-contact-form-7'), // title
        $controller_callback, //callback
        $controller_options // options
    );

    // Controlled Wrapper
    $tag_generator->add(
        'controlled_wrapper', //id
        __('controlled form tag wrapper', 'controller-fields-for-contact-form-7'), //title
        'au_cf7_cf_tag_generator_controlled' //callback
    );
}
add_action('wpcf7_admin_init', 'au_cf7_cf_add_tag_generators', 100);

/**
 * Echo HTML for Controller Tag Generator
 *
 * @param WPCF7_ContactForm $contact_form
 * @param array $options
 *
 * @return void
 */
function au_cf7_cf_tag_generator_controller($contact_form, $options = '')
{
    $options = wp_parse_args($options);
    $type = $options['id'];
    $utm_source = urlencode(home_url());

    au_cf7_cf_tag_generator_open(__('Generate a "controller" form tag that can hide/show other elements in the form.', 'controller-fields-for-contact-form-7'));

    //Input field - Required checkbox
    au_cf7_cf_tag_generator_input(
        $options['content'], // prefix
        'required', // field name
        __('Field type', 'controller-fields-for-contact-form-7'), // label
        array(), // attributes
        'checkbox', // type
        __('Required field', 'controller-fields-for-contact-form-7') // description
    );

    //Input field - Field Name
    au_cf7_cf_tag_generator_input(
        $options['content'], // prefix
        'name', // field name
        __('Name', 'controller-fields-for-contact-form-7'), // label
        array('class' => 'oneline') // attributes, additional classes
    );

    //Input field - ID attribute
    au_cf7_cf_tag_generator_input(
        $options['content'], // prefix
        'id', // field name
        __('Id attribute', 'controller-fields-for-contact-form-7'), // label
        array('class' => 'idvalue oneline option'), // attributes, additional classes
        'text', // field type
        __('This is the controller\'s name, use this value when creating controlled fields.', 'controller-fields-for-contact-form-7'), // description
    );

    //Input field - Options / Dynamic value
    au_cf7_cf_tag_generator_input(
        $options['content'], // prefix
        'values', // field name
        __('Options', 'controller-fields-for-contact-form-7'), // label
        // attributes
        array(
            'class' => 'values oneline minheight', // additional classes
            'placeholder' => "Option 1&#10;option_2 | Option 2" // placeholder attribute
        ),
        'textarea', // field type
        __('Can be static text or a shortcode. If static text, put one option per line. If using a shortcode, it should output the option or option group HTML.', 'controller-fields-for-contact-form-7'), // description
        false, // select options
        // Link args
        array(
            'url' => 'https://aurisecreative.com/docs/contact-form-7-dynamic-text-extension/shortcodes/', // link to documentation
            'label' =>  __('View DTX shortcode syntax documentation', 'controller-fields-for-contact-form-7'), // link label
            'utm_source' => $utm_source // UTM source
        )
    );

    //Input field - Default
    au_cf7_cf_tag_generator_input(
        $options['content'], // prefix
        'defaultvalue', // field name
        __('Default value', 'controller-fields-for-contact-form-7'), // label
        // attributes
        array(
            'class' => 'oneline au-cf-option', // additional classes
            'placeholder' => "CF7_get_post_var key='post_title'" // placeholder attribute
        ),
        'text', // field type
        __('Can be static text or a shortcode.', 'controller-fields-for-contact-form-7'), // description
        false, // select options
        // Link args
        array(
            'url' => 'https://aurisecreative.com/docs/contact-form-7-dynamic-text-extension/shortcodes/dtx-attribute-placeholder/', // link to documentation
            'label' =>  __('View DTX placeholder documentation', 'controller-fields-for-contact-form-7'), // link label
            'utm_source' => $utm_source // UTM source
        )
    );

    if ($type == 'select_controller') {
        //Input field - Dynamic placeholder
        au_cf7_cf_tag_generator_input(
            $options['content'], // prefix
            'placeholder', // field name
            __('Placeholder', 'controller-fields-for-contact-form-7'), // label
            // attributes
            array(
                'class' => 'oneline au-cf-option', // additional classes
                'placeholder' => "CF7_get_post_var key='post_title'" // placeholder attribute
            ),
            'text', // field type
            __('Can be static text or a shortcode.', 'controller-fields-for-contact-form-7'), // description
            false, // select options
            // Link args
            array(
                'url' => 'https://aurisecreative.com/docs/contact-form-7-dynamic-text-extension/shortcodes/dtx-attribute-placeholder/', // link to documentation
                'label' =>  __('View DTX placeholder documentation', 'controller-fields-for-contact-form-7'), // link label
                'utm_source' => $utm_source // UTM source
            )
        );
    } else {
        //Input field - Label First
        au_cf7_cf_tag_generator_input(
            $options['content'], // prefix
            'label_first', // field name
            __('Text first', 'controller-fields-for-contact-form-7'), // label
            array('class' => 'option'), // attributes, additional classes
            'checkbox', // field type
            __('Display the label text first followed by the checkbox', 'controller-fields-for-contact-form-7') // description
        );

        //Input field - Label First
        au_cf7_cf_tag_generator_input(
            $options['content'], // prefix
            'use_label_element', // field name
            __('Label UI', 'controller-fields-for-contact-form-7'), // label
            // attributes
            array(
                'class' => 'option', // additional classes
                'value' => 'on' // Default this box to be checked
            ),
            'checkbox', // field type
            __('Wrap each item with label element to make clicking easier', 'controller-fields-for-contact-form-7') // description
        );

        if ($type == 'checkbox_controller') {
            //Input field - exclusive
            au_cf7_cf_tag_generator_input(
                $options['content'], // prefix
                'exclusive', // field name
                __('Exclusive', 'controller-fields-for-contact-form-7'), // label
                array('class' => 'option'), // attributes, additional classes
                'checkbox', // field type
                __('Mimic radio button functionality by clearing other checkboxes when one is selected.', 'controller-fields-for-contact-form-7') // description
            );
        }
    }

    //Input field - Readonly attribute
    au_cf7_cf_tag_generator_input(
        $options['content'], // prefix
        'readonly', // field name
        __('Read only attribute', 'controller-fields-for-contact-form-7'), // row label
        array('class' => 'option'), // attributes, additional classes
        'checkbox', // field type
        __('Do not let users edit this field', 'controller-fields-for-contact-form-7') // checkbox label / field description
    );

    //Input field - Readonly attribute
    au_cf7_cf_tag_generator_input(
        $options['content'], // prefix
        'disabled', // field name
        __('Disabled attribute', 'controller-fields-for-contact-form-7'), // row label
        array('class' => 'option'), // attributes, additional classes
        'checkbox', // field type
        __('Do not submit this field in the form', 'controller-fields-for-contact-form-7') // checkbox label / field description
    );

    //Input field - Class attribute
    au_cf7_cf_tag_generator_input(
        $options['content'], // prefix
        'class', // field name
        __('Class attribute', 'controller-fields-for-contact-form-7'), // row label
        array('class' => 'classvalue oneline option') // attributes, additional classes

    );

    au_cf7_cf_tag_generator_close($type);
}

/**
 * Echo HTML for Controller Tag Generator
 *
 * @param WPCF7_ContactForm $contact_form
 * @param array $options
 *
 * @return void
 */
function au_cf7_cf_tag_generator_controlled($contact_form, $options = '')
{
    $options = wp_parse_args($options);
    $type = $options['id'];
    $utm_source = urlencode(home_url());

    au_cf7_cf_tag_generator_open(__('Generate a "controlled" element wrapper that will be displayed or hidden based on a controller field. Insert your form tags inside the wrapper.', 'controller-fields-for-contact-form-7'));

    //Input field - Field Name
    au_cf7_cf_tag_generator_input(
        $options['content'], // prefix
        'name', // field name
        __('Controller', 'controller-fields-for-contact-form-7'), // label
        // attributes
        array(
            'class' => 'oneline', // additional classes
            'placeholder' => 'subject'
        ),
        'text',
        __('The id attribute of the controller field that controls this element.', 'controller-fields-for-contact-form-7'), // description
    );

    //Input field - Dynamic value
    au_cf7_cf_tag_generator_input(
        $options['content'], // prefix
        'values', // field name
        __('Values', 'controller-fields-for-contact-form-7'), // label
        // attributes
        array(
            'class' => 'oneline minheight', // additional classes
            'placeholder' => "cars,18,bar" // placeholder attribute
        ),
        'textarea', // field type
        __('If the controller is a radio or select field, put the list of values (one per line) that should cause this content to display', 'controller-fields-for-contact-form-7'), // description
        false, // select options
        // Link args
        array(
            'url' => 'https://aurisecreative.com/docs/contact-form-7-dynamic-text-extension/shortcodes/', // link to documentation
            'label' =>  __('View controlled field documentation', 'controller-fields-for-contact-form-7'), // link label
            'utm_source' => $utm_source // UTM source
        )
    );

    au_cf7_cf_tag_generator_close($type);
}

/**
 * Open Form-Tag Generator
 *
 * @since 1.0.0
 *
 * @param string $description Optional.
 *
 * @return void
 */
function au_cf7_cf_tag_generator_open($description = '')
{
    //Open Form-Tag Generator
    printf(
        '<div class="control-box"><fieldset><legend>%s</legend><table class="form-table"><tbody>',
        esc_html($description)
    );
}

/**
 * Close Form-Tag Generator
 *
 * @since 1.0.0
 *
 * @param string $field_type The form tag's field type
 *
 * @return void
 */
function au_cf7_cf_tag_generator_close($field_type)
{
    if ($field_type == 'controlled_wrapper') {
        printf(
            '</tbody></table></fieldset></div><div class="insert-box"><textarea name="%s" class="tag code" readonly="readonly" onfocus="this.select()"></textarea><div class="submitbox"><input type="button" class="button button-primary insert-controller-wrapper" value="%s" /></div><br class="clear" /></div>',
            esc_attr($field_type),
            esc_html__('Insert Wrapper', 'controller-fields-for-contact-form-7')
        );
    } else {
        printf(
            '</tbody></table></fieldset></div><div class="insert-box"><input type="text" name="%s" class="tag code" readonly="readonly" onfocus="this.select()" /><div class="submitbox"><input type="button" class="button button-primary insert-tag" value="%s" /></div><br class="clear" /></div>',
            esc_attr($field_type),
            esc_html__('Insert Tag', 'controller-fields-for-contact-form-7')
        );
    }
}

/**
 * Generator Input Field
 *
 * @since 1.0.0
 *
 * @param string $prefix form tag prefix
 * @param string $name form tag name
 * @param string $label form tag label
 * @param array $atts form field attributes
 * @param string $type Optional. Form type. Can be `text`, `checkbox`, `select` or empty string.
 * @param string $description Optional. Description to display under the form field.
 * @param string $link_url Optional. URL to documentation.
 * @param string $link_label Optional. Link label for documentation link.
 * @param string $utm_source Optional. UTM source attribute for documentation link.
 *
 * @return void
 */
function au_cf7_cf_tag_generator_input($prefix, $name, $label, $atts = array(), $type = 'text', $description = '', $select_options = array(), $link_args = array())
{
    if (!empty($prefix) && !empty($name) && !empty($label)) {
        $input_html = '';
        // Default field attributes
        $atts = array_merge(array(
            'type' => $type ? $type : 'text', // Default field type
            'id' => $prefix . '-' . $name, // field id
            'name' => $name, // Set name, if not already
            'placeholder' => '',
            'value' => '',
            'required' => '',
            'class' => ''
        ), array_change_key_case((array)$atts, CASE_LOWER));
        $description = is_string($description) && !empty($description) ? $description : '';
        switch ($type) {
            case 'checkbox':
                $input_html .= '<label>';
                $input_html .= au_cf7_cf_input_html($atts, false);
                if ($description) {
                    $input_html .= esc_html($description);
                }
                $input_html .= '</label>';
                $description = '';
                break;
            case 'select':
                $input_html .= au_cf7_cf_select_html($atts, $select_options, false);
                break;
            case 'textarea':
                $input_html .= au_cf7_cf_textarea_html($atts, false);
                break;
            default: // text
                $type = 'text';
                if (strpos($atts['class'], 'au-cf-option') !== false) {
                    $input_html .= sprintf('%s%s',  au_cf7_cf_input_html(array_merge($atts, array(
                        'type' => 'hidden', // Override to be hidden
                        'name' => $name, // Override to have the real name
                        'id' => $atts['name'], // Override to have a different ID so UI label doesn't match it
                        'class' => str_replace('au-cf-option', 'option', $atts['class']) // Set this as the real "option" class
                    )), false), au_cf7_cf_input_html(array_merge($atts, array(
                        'name' => 'au-cf-' . $name // Override to have a false name
                    )), false));
                } else {
                    $input_html .= au_cf7_cf_input_html($atts, false);
                }
                break;
        }
        if ($input_html) {
            if (is_array($link_args) && count($link_args) && !empty($url = sanitize_url(au_cf7_array_has_key('url', $link_args)))) {
                $description .= sprintf(
                    '%s<a href="%s?utm_source=%s&utm_medium=link&utm_campaign=controller-fields-for-contact-form-7&utm_content=form-tag-generator-%s" target="_blank" rel="noopener">%s</a>.',
                    $description ? '&nbsp;' : '',
                    esc_url($url),
                    au_cf7_array_has_key('utm_source', $link_args) ? esc_attr($link_args['utm_source']) : '',
                    esc_attr($atts['type']),
                    au_cf7_array_has_key('label', $link_args) ? esc_html($link_args['label']) : __('View documentation', 'controller-fields-for-contact-form-7'),
                );
            }
            printf(
                '<tr id="%s"><th scope="row"><label for="%s">%s</label></th><td>',
                esc_attr($prefix . '-row-' . $name),
                esc_attr($atts['id']),
                esc_html($label)
            );
            $allowed_properties = au_cf7_get_allowed_input_properties();
            echo (wp_kses($input_html, array_merge(array(
                'label' => array('for' => array()),
                'input' => $allowed_properties,
                'select' => $allowed_properties,
                'textarea' => $allowed_properties
            ), au_cf7_get_allowed_option_properties())));
            if ($description) {
                printf('<br /><small>%s</small>', wp_kses($description, array(
                    'strong' => array(),
                    'em' => array(),
                    'a' => array('href' => array(), 'target' => array(), 'rel' => array(), 'title' => array())
                )));
            }
            echo ('</td></tr>');
        }
    }
}
