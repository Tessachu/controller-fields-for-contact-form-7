/*!
    Name: controllable-fields.js
    Author: AuRise Creative | https://aurisecreative.com
    Last Modified: March 23, 2024 @ 11:58

    To use this feature...

    1. Add a "au-cf7-controller" class to the radio, checkbox, select, or number HTML elements that will be controlling others
        - Checkbox: displays the controlled fields when checked and hides when unchecked.
        - Radio:    displays the controlled fields when checked and hides the rest.
        - Select:   displays the controlled fields when they match the value that is selected and hides the rest.
        - Number:   displays the controlled fields based on number ranges set in the controlled element's values.
    2. Controlled fields should have a data-controller attribute on its wrapping element set to the ID attribute of its controller
        within the same form. It can have multiple controllers, with ID using pipes (|) to separate them.
    3. Controlled fields should have a "au-cf7-hidden-by-controller" class added to its wrapping element to hide it by default. This
        feature simply toggles that class on/off, so you'll need CSS to actually hide it based on that class.
    4. Controlled fields controlled by a radio button, select field, or number field, should have a `data-values` attribute set to a
        pipe separated list of the values used to display it on the wrapping element. Values can include operations such as >, >=,
        <, <=, !=, or *. The operation = is assumed when no comparison is found.
    5. If it has multiple controllers, then there should be a `data-{controller ID}-values` attribute that sets the values for that
        specific controller.
    5. If the controlled field should be required when displayed, instead of adding the required attribute to the input/select field,
        add the "required-when-visible" to the class attribute.
    6. It is possible to nest controllers.
    7. Controlled fields with multiple controllers are only hidden if all controllers evaluate to no matches. Otherwise, it will
        remain visible. Multiple controllers are treated as an OR comparison, meaning at least one controller must be active for
        the controlled field to display. Nest them to treat them as an AND operation.
*/
window['$'] = window['$'] || jQuery.noConflict();
const aurise_controllable_fields_cf7 = {
    version: '2024.03.25.12.36',
    init: function() {
        //Plugin initialization
        console.info('[controllable-fields.js] Initialising version', aurise_controllable_fields_cf7.version);

        // Controllable fields
        let click_controllers = '.wpcf7 form input[type=checkbox].au-cf7-controller, .wpcf7 form input[type=radio].au-cf7-controller',
            select_controllers = '.wpcf7 form select.au-cf7-controller',
            text_controllers = '.wpcf7 form input[type=number].au-cf7-controller',
            $controllers = $(click_controllers + ',' + select_controllers + ',' + text_controllers);
        if ($controllers.length) {

            //Add controllable field listeners
            $(click_controllers).on('click', aurise_controllable_fields_cf7.toggleHandler);
            $(select_controllers).on('change', aurise_controllable_fields_cf7.toggleHandler);
            $(text_controllers).on('change keyup blur', aurise_controllable_fields_cf7.toggleHandler);

            // Initialize default states
            let c1 = 1;
            $controllers.each(function() {
                let $controller = $(this);

                // Initialize top-level controllers only, inner controllers will be initialized as their ancestors become initialized <-- LIES
                //if (!$controller.closest('[data-controller]').length) {
                let id = $controller.attr('id'),
                    cid = aurise_controllable_fields_cf7.getControllerId(this),
                    $controlled = $('[data-controller*="' + cid + '"]');
                if ($controlled.length) {
                    let controlled_value = $controller.is('input[type=checkbox]') ? aurise_controllable_fields_cf7.getCheckbox($controller) : $controller.val(),
                        ariaControls = [];
                    aurise_controllable_fields_cf7.toggleControlledFields(cid, id, controlled_value);
                    // Update aria attributes
                    $controlled.each(function() {
                        let c2 = 1,
                            $thisControlled = $(this),
                            thisControlledId = $thisControlled.attr('id'),
                            thisLabel = $thisControlled.attr('aria-labelledby');
                        if (!thisControlledId) {
                            // Generate a random ID
                            thisControlledId = 'temp-id-' + cid + '-' + c1 + '-' + c2;
                            $thisControlled.attr('id', thisControlledId);
                        }
                        ariaControls.push(thisControlledId);
                        if (!thisLabel) {
                            thisLabel = id;
                        } else {
                            thisLabel += ' ' + id;
                        }
                        $thisControlled.attr({ 'role': 'group', 'aria-labelledby': thisLabel });
                        c2++;
                    });
                    ariaControls = ariaControls.join(' ');
                    $controller.attr({ 'aria-controls': ariaControls, 'aria-owns': ariaControls });
                } else {
                    console.warn('No controlled elements found for controller: ' + cid);
                    // If I am a checkbox or radio button, look for my name object instead
                }
                c1++;
                //}
            });
        }
    },
    getControllerId: function(input) {
        let $controller = $(input),
            id = $controller.attr('id'),
            id2 = $controller.attr('data-id');
        // If there is a data-id attribute, return that
        if (id2) {
            return id2;
        }
        // If I don't have an id attribute, return my name attribute
        if (!id || $controller.is('[type=radio]') || $controller.is('[type=checkbox]')) {
            return $controller.attr('name').replace('[]', '');
        }
        return id; // Return my id attribute
    },
    getControllerById: function(cid) {
        // The cid could be the id attribute or data-id attribute
        let $controller = $('[data-id="' + cid + '"].au-cf7-controller');
        if ($controller.length) {
            return $controller;
        }
        $controller = $('#' + cid + '.au-cf7-controller');
        if ($controller.length) {
            return $controller;
        }
        return false;
    },
    getCheckbox: function(input) {
        //Returns a true/false boolean value based on whether the checkbox is checked
        let $input = $(input);
        return ($input.is(':checked') || $input.prop('checked'));
    },
    toggleCheckbox: function(input, passedValue) {
        //Changes a checkbox input to be checked or unchecked based on boolean parameter (or toggles if not included)
        //Only changes it visually - it does not change any data in any objects
        let $input = $(input),
            value = passedValue;
        if (typeof(value) != 'boolean' || value === undefined) {
            value = !aurise_controllable_fields_cf7.getCheckbox($input);
        }
        if (value) {
            $input.attr('checked', 'checked');
            $input.prop('checked', true);
        } else {
            $input.removeAttr('checked');
            $input.prop('checked', false);
        }
    },
    toggleHandler: function(e) {
        let $controller = typeof(e) == 'string' ? $('#' + e) : $(e.target),
            id = $controller.attr('id'),
            cid = aurise_controllable_fields_cf7.getControllerId($controller);
        aurise_controllable_fields_cf7.toggleControlledFields(cid, id, null);
    },
    toggleControlledFields: function(cid, id, forcedToggle) {
        let $controller = $('#' + id);
        if ($controller.length < 1) { console.warn('Controller #' + id + '" does not exist!'); return; }
        let is_select = $controller.is('select'),
            is_checkbox = $controller.is('[type=checkbox]'),
            is_radio = $controller.is('[type=radio]'),
            //is_number = $controller.is('[type=number]'),
            $controlledObjs = $('[data-controller*="' + cid + '"]');
        if ($controlledObjs.length < 1) { console.warn('No controlled elements found for controller: ' + cid); return; }
        let controlled_value = forcedToggle === null || forcedToggle === undefined ? $controller.val() : forcedToggle,
            count_displayed = 0;
        // if (is_number) {
        //     controlled_value = parseFloat(controlled_value);
        // }
        // Loop through each controlled element; the value must match that of the input to display it
        $controlledObjs.each(function(co, controlled) {
            let $controlled = $(controlled),
                // If I have multiple controllers, get the values for this specific controller
                multiControllers = $controlled.attr('data-controller').indexOf('|') >= 0,
                acceptedValues = multiControllers ? $controlled.attr('data-' + cid + '-values') : $controlled.attr('data-values'),
                matches = 0,
                displayMe = false;
            // if (is_checkbox || is_radio) {
            //     debugger;
            //     if (aurise_controllable_fields_cf7.getCheckbox($controller)) {
            //         matches++;
            //     }
            // } else
            if (acceptedValues !== undefined) {
                if (acceptedValues.indexOf('|') >= 0) {
                    acceptedValues = acceptedValues.split('|');
                } else {
                    acceptedValues = [acceptedValues];
                }
                $.each(acceptedValues, function(i, acceptedValue) {
                    if (
                        (!is_checkbox && !is_radio) || // If neither a checkbox nor select field, continue to check for matches
                        ((is_checkbox || is_radio) && aurise_controllable_fields_cf7.getCheckbox($controller)) // For checkboxes and radios, it must also be checked
                    ) {
                        console.log('Comparing values for', controlled, 'where', controlled_value, '(controlled value) should match', acceptedValue, '(accepted value)');
                        /**
                         * acceptedValue = the controller's value that it should match to be true
                         * controlled_value = the controlled element's value that is being tested
                         */
                        matches += aurise_controllable_fields_cf7.compareValues(acceptedValue, controlled_value);
                    }
                });
            }
            if (matches > 0) {
                // This controlled element's value matches what was selected, display it
                count_displayed++;
                $controlled.removeClass('au-cf7-hidden-by-controller');
                displayMe = true;
                console.info('Displaying this controlled element', $controlled[0]);
            } else {
                // Before hiding, we need to check if I have multiple controllers and if I do, if any of them say I should still be shown
                if (multiControllers) {
                    matches = 0;
                    let controllers = $controlled.attr('data-controller').split('|');
                    $.each(controllers, function(c, other_cid) {
                        // Skip checking the controller this is for, just check the other ones
                        if (other_cid !== cid) {
                            let $other_controller = aurise_controllable_fields_cf7.getControllerById(other_cid),
                                other_controlled_value = $other_controller.val(),
                                is_other_checkbox = $other_controller.is('[type=checkbox]'),
                                is_other_radio = $other_controller.is('[type=radio]'),
                                otherAcceptedValues = $controlled.attr('data-' + other_cid + '-values');
                            if ($other_controller !== false) {
                                if (otherAcceptedValues === undefined) {
                                    if (($other_controller.is('[type=checkbox]') || $other_controller.is('[type=radio]')) && aurise_controllable_fields_cf7.getCheckbox($other_controller)) {
                                        matches++;
                                    }
                                } else {
                                    if (otherAcceptedValues.indexOf('|') >= 0) {
                                        otherAcceptedValues = otherAcceptedValues.split('|');
                                    } else {
                                        otherAcceptedValues = [otherAcceptedValues];
                                    }
                                    // Compare the controlled values agaist this other controller's values
                                    $.each(otherAcceptedValues, function(i, acceptedValue) {
                                        if (
                                            (!is_other_checkbox && !is_other_radio) || // If neither a checkbox nor select field, continue to check for matches
                                            ((is_other_checkbox || is_other_radio) && aurise_controllable_fields_cf7.getCheckbox($other_controller)) // For checkboxes and radios, it must also be checked
                                        ) {
                                            console.log('Comparing values for', controlled, 'where', other_controlled_value, '(controlled value) should match', acceptedValue, '(accepted value)');
                                            /**
                                             * acceptedValue = the controller's value that it should match to be true
                                             * controlled_value = the controlled element's value that is being tested
                                             */
                                            matches += aurise_controllable_fields_cf7.compareValues(acceptedValue, other_controlled_value);
                                        }
                                    });
                                }
                            }
                        }
                    });
                    if (matches > 0) {
                        // This controlled element's value matches what was selected, display it
                        count_displayed++;
                        $controlled.removeClass('au-cf7-hidden-by-controller');
                        displayMe = true;
                        console.info('Displaying this controlled element because at least one of its multiple controllers is true', $controlled[0]);
                    } else {
                        // This controlled element's value DOES NOT match what was selected, hide it
                        $controlled.addClass('au-cf7-hidden-by-controller');
                        console.info('Hiding this controlled element because all of its multiple controllers are false', $controlled[0]);
                    }
                } else {
                    // This controlled element's value DOES NOT match what was selected, hide it
                    $controlled.addClass('au-cf7-hidden-by-controller');
                    console.info('Hiding this controlled element', $controlled[0]);
                }
            }

            if (is_select) {
                // Handle nested required fields: TO-DO: filter out nested-requireds?
                let $required_fields = displayMe ? $controlled.find('.required-when-visible') : $controlled.find('[required]')
                if ($required_fields.length > 0) {
                    if (displayMe) {
                        // Add the required attributes because they are now visible
                        $required_fields.each(function(rf, required_field) {
                            $(required_field).attr({ 'required': 'required', 'aria-required': 'true' });
                        });
                    } else {
                        // Remove the required attributes because they are being hidden
                        $required_fields.each(function(rf, required_field) {
                            let $thisRequiredField = $(required_field);
                            if (!$thisRequiredField.hasClass('required-when-visible')) {
                                $thisRequiredField.addClass('required-when-visible');
                            }
                            $thisRequiredField.attr('aria-required', 'false').removeAttr('required');
                        });
                    }
                }

                // Hide nested controllers
                if (!displayMe) {
                    let $nested_controllers = $controlled.find('.au-cf7-controller');
                    if ($nested_controllers.length) {
                        //console.info('One of the fields you are hiding is a controller, so hide its fields!');
                        $nested_controllers.each(function(nc, nested_controller) {
                            let $nested_controller = $(nested_controller);
                            aurise_controllable_fields_cf7.toggleCheckbox($nested_controller, displayMe);
                            aurise_controllable_fields_cf7.toggleControlledFields(aurise_controllable_fields_cf7.getControllerId($nested_controller), $nested_controller.attr('id'), displayMe);
                        });
                    }
                }
            }
        });
        // Handle required fields and nested controllers differently than select boxes
        // if (is_checkbox || is_radio) {

        // }
        // Update ARIA flag on controller
        if (count_displayed && count_displayed == $controlledObjs.length) {
            $controller.attr('aria-expanded', 'true');
        } else {
            $controller.attr('aria-expanded', 'false');
        }
    },
    compareValues: function(value, controlled_value) {
        /**
         * value = the controller's value that it should match to be true
         * controlled_value = the controlled element's value that is being tested
         */
        let matches = 0;
        // Treat null and undefined values as empty strings
        if (controlled_value === null || controlled_value === undefined || typeof(controlled_value) != 'string') {
            controlled_value = '';
        }
        if (value === null || value === undefined || typeof(value) != 'string') {
            value = '';
        }
        if (value) {
            if (value == 'BLANK' || value == 'EMTPY') {
                if (controlled_value == '') {
                    matches++;
                }
            } else if (value == 'EMPTY_OR_ZERO' || value == 'FALSEY') {
                if (controlled_value == '' || controlled_value == 0) {
                    matches++;
                }
            } else if ((!isNaN(controlled_value)) && (value.startsWith('<') || value.startsWith('>'))) {
                // If comparing it to the range, it must be a number value
                controlled_value = parseFloat(controlled_value);
                if (value.startsWith('<=')) {
                    if (controlled_value <= parseFloat(value.replace('<=', ''))) {
                        //console.info('[NUMERIC MATCH]', controlled_value, '<=', value.replace('<=', ''));
                        matches++;
                    }
                } else if (value.startsWith('<')) {
                    if (controlled_value < parseFloat(value.replace('<', ''))) {
                        //console.info('[NUMERIC MATCH]', controlled_value, '<', value.replace('<', ''));
                        matches++;
                    }
                } else if (value.startsWith('>=')) {
                    if (controlled_value >= parseFloat(value.replace('>=', ''))) {
                        //console.info('[NUMERIC MATCH]', controlled_value, '>=', value.replace('>=', ''));
                        matches++;
                    }
                } else if (value.startsWith('>')) {
                    if (controlled_value > parseFloat(value.replace('>', ''))) {
                        //console.info('[NUMERIC MATCH]', controlled_value, '>', value.replace('>', ''));
                        matches++;
                    }
                }
            } else {
                if (value == '*' && controlled_value != '' && controlled_value != 0) {
                    // Match anything that isn't falsy
                    //console.info('[MATCH]', controlled_value, 'is not falsey');
                    matches++;
                } else if (value.startsWith('!=')) {
                    // Match everything else except this value
                    if (controlled_value != value.replace('!=', '')) {
                        //console.info('[MATCH]', controlled_value, 'is not equal to', value.replace('!=', ''));
                        matches++;
                    }
                } else if (value == controlled_value) {
                    // Match only this value
                    //console.info('[MATCH]', controlled_value, 'is equal to', value);
                    matches++;
                }
            }
        }
        return matches;
    }
};
$(document).ready(aurise_controllable_fields_cf7.init);