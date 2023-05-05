<?php

defined('ABSPATH') || exit; // Exit if accessed directly

if (!function_exists('au_cf7_get_allowed_input_properties')) {
    /**
     * Get Allowed HTML for Form Field Properties
     *
     * @since 1.0.1
     *
     * @param array $extra Optional. A sequential array of properties to additionally include.
     *
     * @return array An associative array of properties.
     */
    function au_cf7_get_allowed_input_properties($extra = array())
    {
        $allowed_properties = array(
            'type' => array(),
            'id' => array(),
            'name' => array(),
            'placeholder' => array(),
            'value' => array(),
            'required' => array(),
            'class' => array(),
            'min' => array(),
            'max' => array(),
            'step' => array(),
            'disabled' => array(),
            'readonly' => array(),
            'checked' => array(),
            'size' => array(),
            'maxlength' => array(),
            'multiple' => array(),
            'pattern' => array(),
            'title' => array(),
            'autofocus' => array(),
            'autocomplete' => array()
        );
        if (is_array($extra) && count($extra)) {
            foreach ($extra as $property) {
                $allowed_properties[sanitize_text_field($property)] = array();
            }
        }
        return $allowed_properties;
    }
}

if (!function_exists('au_cf7_get_allowed_option_properties')) {
    /**
     * Get Allowed HTML for Select Option Field Properties
     *
     * @since 1.0.1
     *
     * @return array An associative array of properties.
     */
    function au_cf7_get_allowed_option_properties()
    {
        return array(
            'optgroup' => array(
                'label' => array(),
                'disabled' => array(),
                'hidden' => array()
            ),
            'option' => array(
                'value' => array(),
                'selected' => array(),
                'disabled' => array(),
                'hidden' => array()
            )
        );
    }
}


if (!function_exists('au_cf7_format_atts')) {
    /**
     * Returns a formatted string of HTML attributes
     *
     * @since 1.0.0
     *
     * @param array $atts Associative array of attribute name and value pairs
     *
     * @return string Formatted HTML attributes with keys and values both sanitized
     */
    function au_cf7_format_atts($atts)
    {
        if (is_array($atts) && count($atts)) {
            $sanitized_atts = array();
            static $boolean_attributes = array(
                'checked', 'disabled', 'multiple', 'readonly', 'required', 'selected',
            );
            foreach ($atts as $key => $value) {
                if ($key) {
                    $key = sanitize_key(strval($key));
                    if (in_array(strtolower($key), $boolean_attributes) || is_bool($value)) {
                        $key = strtolower($key);
                        if (!is_bool($value)) {
                            //$value = filter_var($value, FILTER_VALIDATE_BOOL);
                            $value = $value ? true : false; // If not set to a boolean, check for a truthy value
                        }
                        if ($value === true) {
                            $sanitized_atts[$key] = $key;
                        }
                    } elseif ($value && (is_string($value) || is_numeric($value))) {
                        $sanitized_atts[$key] = sanitize_text_field(strval($value));
                    }
                }
            }
            if (count($sanitized_atts)) {
                $output = array();
                foreach ($sanitized_atts as $key => $value) {
                    $output[] = sprintf('%s="%s"', esc_attr($key), esc_attr($value));
                }
                return implode(' ', $output);
            }
        }
        return '';
    }
}

if (!function_exists('au_cf7_array_has_key')) {
    /**
     * Array Key Exists and Has Value
     *
     * @since 3.1.0
     *
     * @param string|int $key The key to search for in the array.
     * @param array $array The array to search.
     * @param mixed $default The default value to return if not found or is empty. Default is an empty string.
     *
     * @return mixed The value of the key found in the array if it exists or the value of `$default` if not found or is empty.
     */
    function au_cf7_array_has_key($key, $array = array(), $default = '')
    {
        //Check if this key exists in the array
        $valid_key = (is_string($key) && !empty($key)) || is_numeric($key);
        $valid_array = is_array($array) && count($array);
        if ($valid_key && $valid_array && array_key_exists($key, $array)) {
            //Always return if it's a boolean or number, otherwise only return it if it has any value
            if ($array[$key] || is_bool($array[$key]) || is_numeric($array[$key])) {
                return $array[$key];
            }
        }
        return $default;
    }
}

if (!function_exists('array_key_first')) {
    /**
     * Gets the first key of an array
     *
     * Gets the first key of the given array without affecting the internal array pointer.
     *
     * @param array $array
     * @return int|string|null
     */
    function array_key_first($array = array())
    {
        foreach ($array as $key => $value) {
            return $key;
        }
        return null;
    }
}

/**
 * Create Input Field HTML
 *
 * @since 1.0.0
 *
 * @param array $atts An associative array of select input attributes. Requires `name` key to
 * have a value. Can also accept key/value pairs for `id` (recommended), `placeholder` (used
 * as the first option in the select field with an empty value), `value` (if wanting to set
 * the default selected value), and `required` (set to anything "truthy" to make it required).
 * @param bool $echo If true, will echo the HTML output before returning. Default is true.
 *
 * @return string HTML output of input field or empty string on failure.
 */
function au_cf7_cf_input_html($atts, $echo = true)
{
    // Default field attributes
    $atts = array_merge(array(
        'type' => 'text',
        'id' => '',
        'name' => '',
        'placeholder' => '',
        'value' => '',
        'required' => '',
        'checked' => ''
    ), array_change_key_case((array)$atts, CASE_LOWER));
    if (!empty($atts['name'])) {
        if ($atts['required']) {
            $atts['required'] = 'required'; // If truthy, always set to this value
        } else {
            unset($atts['required']); // If falsey, remove from attributes
        }
        if ($atts['type'] == 'checkbox' || $atts['type'] == 'radio') {
            if ($atts['value']) {
                $atts['checked'] = 'checked'; // If truthy, always set to this value
            }
        } else {
            unset($atts['checked']);
        }
        $html = sprintf('<input %s />', au_cf7_format_atts($atts));
        if ($echo) {
            echo (wp_kses($html, array('input' => au_cf7_get_allowed_input_properties())));
        }
        return $html;
    }
    return '';
}

/**
 * Create Checkbox Field HTML
 *
 * @since 1.0.0
 *
 * @param array $atts An associative array of select input attributes. Requires `name` key to
 * have a value. Can also accept key/value pairs for `id` (recommended), `placeholder` (used
 * as the first option in the select field with an empty value), `value` (if wanting to set
 * the default selected value), and `required` (set to anything "truthy" to make it required).
 * @param string $label The text to display next to the checkbox or radio button.
 * @param bool $label_ui Optional. If true, will place input inside a `<label>` element. Default is true.
 * @param bool $reverse Optional. If true, will reverse the order to display the text label first then the button. Default is false.
 * @param bool $echo Optional. If true, will echo the HTML output before returning. Default is true.
 *
 * @return string HTML output of the checkbox or radio button or empty string on failure.
 */
function au_cf7_cf_checkbox_html($atts, $label, $label_ui = true, $reverse = false, $echo = true)
{
    // Default field attributes
    $atts = array_merge(array(
        'type' => 'checkbox',
        'id' => '',
        'name' => '',
        'placeholder' => '',
        'value' => '',
        'required' => '',
        'checked' => ''
    ), array_change_key_case((array)$atts, CASE_LOWER));
    $html = au_cf7_cf_input_html($atts, false);
    if ($html) {
        $label = sprintf('<span class="wpcf7-list-item-label">%s</span>', esc_html($label));
        if ($reverse) {
            $html = $label . $html;
        } else {
            $html .= $label;
        }
        if ($label_ui) {
            $html = '<label>' . $html . '</label>';
        }
        if ($echo) {
            echo (wp_kses($html, array(
                'input' => au_cf7_get_allowed_input_properties(),
                'label' => array(),
                'span' => array('class' => array())
            )));
        }
        return $html;
    }
    return '';
}

/**
 * Create Textarea Input Field HTML
 *
 * @since 1.0.0
 *
 * @param array $atts An associative array of select input attributes. Requires `name` key to
 * have a value. Can also accept key/value pairs for `id` (recommended), `placeholder` (used
 * as the first option in the select field with an empty value), `value` (if wanting to set
 * the default selected value), and `required` (set to anything "truthy" to make it required).
 * @param bool $echo Optional. If true, will echo the HTML output before returning. Default is true.
 *
 * @return string HTML output of select field or empty string on failure.
 */
function au_cf7_cf_textarea_html($atts, $echo = true)
{
    // Default field attributes
    $atts = array_merge(array(
        'type' => 'textarea', // Ignored but key is needed so it's successfully unset
        'id' => '',
        'name' => '',
        'placeholder' => '',
        'value' => '',
        'required' => ''
    ), array_change_key_case((array)$atts, CASE_LOWER));
    if (!empty($atts['name'])) {
        $value = sanitize_text_field($atts['value']);
        unset($atts['type'], $atts['value']);
        if ($atts['required']) {
            $atts['required'] = 'required'; // If truthy, always set to this value
        } else {
            unset($atts['required']); // If falsey, remove from attributes
        }
        $html = sprintf(
            '<textarea %s>%s</textarea>',
            au_cf7_format_atts($atts),
            esc_html($value)
        );
        if ($echo) {
            echo (wp_kses($html, array('textarea' => au_cf7_get_allowed_input_properties())));
        }
        return $html;
    }
    return '';
}

/**
 * Create Select Input Field HTML
 *
 * @since 1.0.0
 *
 * @param array $atts An associative array of select input attributes. Requires `name` key to
 * have a value. Can also accept key/value pairs for `id` (recommended), `placeholder` (used
 * as the first option in the select field with an empty value), `value` (if wanting to set
 * the default selected value), and `required` (set to anything "truthy" to make it required).
 * @param array|string $options Accepts an associative array of key/value pairs to use as the
 * select option's value/label pairs. It also accepts an associative array of associative
 * arrays with the keys being used as option group labels and the array values used as that
 * group's options. It also accepts a string value of HTML already formatted as options or
 * option groups. It also accepts a string value of a self-closing shortcode that is
 * evaluated and its output is either options or option groups.
 * @param bool $echo If true, will echo the HTML output before returning. Default is true.
 *
 * @return string HTML output of select field or empty string on failure.
 */
function au_cf7_cf_select_html($atts, $options, $echo = true)
{
    // Default field attributes
    $atts = array_merge(array(
        'type' => 'select', // Ignored but key is needed so it's successfully unset
        'id' => '',
        'name' => '',
        'placeholder' => '',
        'value' => '',
        'required' => ''
    ), array_change_key_case((array)$atts, CASE_LOWER));
    if (!empty($atts['name'])) {
        $value = $atts['value'];
        $placeholder = $atts['placeholder']; // Get placeholder value
        unset($atts['type'], $atts['value'], $atts['placeholder']); // Remove from attributes
        if ($atts['required']) {
            $atts['required'] = 'required'; // If truthy, always set to this value
        } else {
            unset($atts['required']); // If falsey, remove from attributes
        }
        $html = sprintf('<select %s>', au_cf7_format_atts($atts)); // Open select field
        if ($placeholder) {
            $html .= sprintf(
                '<option value=""%s>%s</option>',
                empty($value) ? ' selected' : '',
                esc_html($placeholder)
            );
        }
        $allowed_html = au_cf7_get_allowed_option_properties();
        if (is_array($options) && count($options)) {
            //Check if using option groups
            if (is_array(array_values($options)[0])) {
                foreach ($options as $group_name => $opt_group) {
                    $html .= sprintf('<optgroup label="%s">', esc_attr($group_name)); // Open option group
                    foreach ($opt_group as $option_value => $option_label) {
                        $html .= sprintf(
                            '<option value="%1$s"%3$s>%2$s</option>',
                            esc_attr($option_value),
                            esc_html($option_label),
                            $value == $option_value ? ' selected' : ''
                        );
                    }
                    $html .= '</optgroup>'; // Close option group
                }
            } else {
                foreach ($options as $option_value => $option_label) {
                    $html .= sprintf(
                        '<option value="%1$s"%3$s>%2$s</option>',
                        esc_attr($option_value),
                        esc_html($option_label),
                        $value == $option_value ? ' selected' : ''
                    );
                }
            }
        } elseif (is_string($options) && !empty($options)) {
            // If options were passed as a string, go ahead and use them
            if (strpos($options, '<option') === 0 || stripos($options, '<optgroup') === 0) {
                $html .= wp_kses($options,  $allowed_html);
            } else {
                // If a shortcode was passed as the options, evaluate it and use the result
                $shortcode_output = au_cf7_cf_get_dynamic($options);
                if (is_string($shortcode_output) && !empty($shortcode_output) && (strpos($shortcode_output, '<option') === 0) || strpos($shortcode_output, '<optgroup') === 0) {
                    $html .= wp_kses($shortcode_output, $allowed_html);
                }
            }
        }
        $html .= '</select>'; // Close select field
        if ($echo) {
            echo (wp_kses($html, array_merge($allowed_html, array('select' => au_cf7_get_allowed_input_properties()))));
        }
        return $html;
    }
    return '';
}

/**
 * Get Dynamic Value
 *
 * @since 1.0.0
 *
 * @param string $value The form tag value.
 * @param WPCF7_FormTag|false $tag Optional. Use to look up default value.
 *
 * @return string The dynamic output or the original value, not escaped or sanitized.
 */
function au_cf7_cf_get_dynamic($value, $tag = false)
{
    if ($tag !== false) {
        $default = $tag->get_option('defaultvalue', '', true);
        if (!$default) {
            $default = $tag->get_default_option(strval(reset($tag->values)));
        }
        $value = wpcf7_get_hangover($tag->name, $default);
    }
    if (is_string($value) && !empty($value)) {
        // If a shortcode was passed as the options, evaluate it and use the result
        $shortcode_tag = '[' . $value . ']';
        $shortcode_output = do_shortcode($shortcode_tag); //Shortcode value
        if (is_string($shortcode_output) && $shortcode_output != $shortcode_tag) {
            return $shortcode_output;
        }
    }
    return $value;
    $placeholder = '';
    $validation_error = '';
    $atts = array();

    // Get your locations
    $location_ids = get_posts(array(
        'fields' => 'ids', // returns an array of post IDs
        'post_type' => 'locations', // specify custom post type
        'post_status' => 'publish', // only get publicly published locations
        'posts_per_page' => -1, // get all locations
        'order' => 'ASC', // order alphabetically from A to Z
        'orderby' => 'title' // order by title
    ));

    // Get option HTML
    $options = array();
    if ($placeholder) {
        $options[] = sprintf(
            '<option value=""%2$s>%1$s</option>',
            esc_html($placeholder), // label
            $tag->is_required() ? ' selected disabled hidden' : '' // Disallow placeholder as an option if field is required
        );
    }
    if (count($location_ids)) {
        foreach ($location_ids as $location_id) {
            $options[] = sprintf(
                '<option value="%1$s"%3$s>%2$s</option>',
                esc_attr($location_id), // value attribute
                esc_html(get_the_title($location_id)), // label
                $value == $location_id ? ' selected' : '' // selected attribute
            );
        }
    }

    // Return the select field HTML
    return sprintf(
        '<span class="wpcf7-form-control-wrap %s"><select %s>%s</select>%s</span>',
        sanitize_html_class($tag->name),
        wpcf7_format_atts($atts), //This function already escapes attribute values
        wp_kses(implode('', $options), au_cf7_get_allowed_option_properties()),
        $validation_error
    );
}
