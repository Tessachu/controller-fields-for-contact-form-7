/*!
    Name: controllable-fields.js
    Author: AuRise Creative | https://aurisecreative.com
    Last Modified: March 28, 2023 @ 19:04

    To use this feature...

    1. Add a "au-cf7-controller" class to the radio, checkbox, or select HTML elements that will be controlling others
        - Checkbox: displays the controlled fields when checked and hides when unchecked.
        - Radio:    displays the controlled fields when checked and hides the rest.
        - Select:   displays the controlled fields when they match the value that is selected and hides the rest.
    2. Controlled fields should have a data-controller attribute on its wrapping element set to the ID attribute of its controller within the same form
    3. Controlled fields should have a "au-cf7-hidden-by-controller" class added to its wrapping element to hide it by default. This feature simply toggles that class on/off, so you'll need CSS to actually hide it based on that class.
    4. If it is controlled by a radio button or select element, the wrapping element of the controlled field should also have a data-values attribute set to a pipe separated list of the values used to display it.
    5. If the controlled field should be required when displayed, instead of adding the required attribute to the input/select field, add the "required-when-visible" to the class attribute.
    6. It is possible to nest controllers.
*/
var $ = jQuery.noConflict(),
    auControllableFields = {
        version: '2023.03.28.20.34',
        init: function() {
            //Plugin initialization
            console.info('Initialising controllable-fields.js. Last modified ' + auControllableFields.version);

            // Controllable fields
            if ($('.wpcf7 form input[type=checkbox].au-cf7-controller, .wpcf7 form input[type=radio].au-cf7-controller, .wpcf7 form select.au-cf7-controller').length) {
                //Add controllable field listeners
                $('.wpcf7 form input[type=checkbox].au-cf7-controller, .wpcf7 form input[type=radio].au-cf7-controller').on('click', auControllableFields.toggleHandler);
                $('.wpcf7 form select.au-cf7-controller').on('change', auControllableFields.toggleHandler);

                // Initialize default states
                $c1 = 1;
                $('.wpcf7 form input[type=checkbox].au-cf7-controller, .wpcf7 form input[type=radio].au-cf7-controller, .wpcf7 form select.au-cf7-controller').each(function() {
                    var $controller = $(this),
                        id = $controller.attr('id'),
                        id2 = $controller.attr('data-id'),
                        cid = id2 && id != id2 ? id2 : id,
                        $controlled = $('[data-controller="' + cid + '"]');
                    if ($controlled.length) {
                        var controlled_value = $controller.is('input[type=checkbox]') ? auControllableFields.getCheckbox($controller) : $controller.val(),
                            ariaControls = [];
                        auControllableFields.toggleControlledFields(id, controlled_value);
                        // Update aria attributes
                        $controlled.each(function() {
                            $c2 = 1;
                            var $thisControlled = $(this),
                                thisControlledId = $thisControlled.attr('id'),
                                thisLabel = $thisControlled.attr('aria-labelledby');
                            if (!thisControlledId) {
                                // Generate a random ID
                                thisControlledId = 'temp-id-' + cid + '-' + $c1 + '-' + $c2;
                                $thisControlled.attr('id', thisControlledId);
                            }
                            ariaControls.push(thisControlledId);
                            if (!thisLabel) {
                                thisLabel = id;
                            } else {
                                thisLabel += ' ' + id;
                            }
                            $thisControlled.attr({ 'role': 'group', 'aria-labelledby': thisLabel });
                            $c2++;
                        });
                        ariaControls = ariaControls.join(' ');
                        $controller.attr({ 'aria-controls': ariaControls, 'aria-owns': ariaControls });
                    } else {
                        console.warn('No controlled elements found for controller: ' + cid);
                    }
                    $c1++;
                });
            }
        },
        getCheckbox: function(input) {
            //Returns a true/false boolean value based on whether the checkbox is checked
            var $input = $(input);
            return ($input.is(':checked') || $input.prop('checked'));
        },
        toggleCheckbox: function(input, passedValue) {
            //Changes a checkbox input to be checked or unchecked based on boolean parameter (or toggles if not included)
            //Only changes it visually - it does not change any data in any objects
            var $input = $(input);
            var value = passedValue;
            if (typeof(value) != 'boolean' || value === undefined) {
                value = !auControllableFields.getCheckbox($input);
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
            var $controller = typeof(e) == 'string' ? $('#' + e) : $(this),
                id = $controller.attr('id');
            auControllableFields.toggleControlledFields(id, null);
        },
        toggleControlledFields: function(id, forcedToggle) {
            var $controller = $('#' + id);
            if ($controller.length < 1) { console.warn('Controller #' + id + '" does not exist!'); return; }
            //console.info('Toggle Fields: ' + id);
            var id2 = $controller.attr('data-id'),
                id = id2 && id != id2 ? id2 : id,
                $controlled = $('[data-controller="' + id + '"]');
            if ($controlled.length < 1) { console.warn('No controlled elements found for controller: ' + id); return; }
            if ($controller.is('select')) {
                var controlled_value = forcedToggle === null || forcedToggle === undefined ? $controller.val() : forcedToggle,
                    count_displayed = 0;
                //Because it is a select field, the value must match that of the input to display it
                $controlled.each(function() {
                    var $thisControlled = $(this);
                    var myValues = $thisControlled.attr('data-values');
                    if (myValues.indexOf('|') >= 0) {
                        myValues = myValues.split('|');
                    } else {
                        myValues = [myValues];
                    }
                    var matches = 0;
                    $.each(myValues, function(i, value) {
                        if (value == controlled_value) { matches++; }
                    });
                    if (matches > 0) {
                        count_displayed++;
                        //This controlled element's value matches what was selected in the dropdown, display it
                        $thisControlled.removeClass('au-cf7-hidden-by-controller');
                        //If there are any required fields, add the required flag to them
                        var $required_fields = $thisControlled.find('.required-when-visible');
                        if ($required_fields.length > 0) {
                            $required_fields.each(function() {
                                $(this).attr({ 'required': 'required', 'aria-required': 'true' });
                            });
                        }
                    } else {
                        //This controlled element's value does not match what was selected in the dropdown, hide it
                        //Checkbox or radio button is false, so hide its options
                        $thisControlled.addClass('au-cf7-hidden-by-controller');
                        //If there are any required fields, remove the required flag from them
                        var $required_fields = $thisControlled.find('[required]');
                        if ($required_fields.length > 0) {
                            $required_fields.each(function() {
                                var $thisRequiredField = $(this);
                                if (!$thisRequiredField.hasClass('required-when-visible')) {
                                    $thisRequiredField.addClass('required-when-visible');
                                }
                                $thisRequiredField.attr('aria-required', 'false').removeAttr('required');
                            });
                        }
                        //Search through the fields that are being hidden, and if they are controllers themselves,
                        //toggle them off and hide their controlled fields
                        if ($thisControlled.length) {
                            $thisControlled.each(function(i, value) {
                                var $c = $(this).find('.au-cf7-controller');
                                if ($c.length) {
                                    //console.info('One of the fields you are hiding is a controller, so hide its fields!');
                                    auControllableFields.toggleCheckbox($c, false);
                                    auControllableFields.toggleControlledFields($c.attr('id'), false);
                                }
                            });
                        }
                    }
                });
                // Update ARIA flag
                if (count_displayed == $controlled.length) {
                    $controller.attr('aria-expanded', 'true');
                } else {
                    $controller.attr('aria-expanded', 'false');
                }
            } else {
                var toggle = forcedToggle === null || forcedToggle === undefined ? auControllableFields.getCheckbox($controller) : forcedToggle;
                if (toggle) {
                    //Checkbox or radio button is true, so reveal its options
                    $controlled.removeClass('au-cf7-hidden-by-controller');
                    //If there are any required fields, add the required flag to them
                    var $required_fields = $controlled.find('.required-when-visible');
                    if ($required_fields.length > 0) {
                        $required_fields.each(function() {
                            $(this).attr({ 'required': 'required', 'aria-required': 'true' });
                        });
                    }
                    if ($controller.is('[type=radio]')) {
                        //Because we are a radio button, we have to hide all other options except for this
                        var $radioGroup = $('[name="' + $controller.attr('name') + '"]:not(#' + id + ')');
                        //Search through the fields that are being hidden, and if they are controllers themselves,
                        //toggle them off and hide their controlled fields
                        if ($radioGroup.length) {
                            $radioGroup.each(function(i, value) {
                                auControllableFields.toggleControlledFields($(this).attr('id'), false);
                            });
                        }
                    }
                    $controller.attr('aria-expanded', 'true'); // Update ARIA flag
                } else {
                    //Checkbox or radio button is false, so hide its options
                    $controlled.addClass('au-cf7-hidden-by-controller');
                    //If there are any required fields, remove the required flag from them
                    var $required_fields = $controlled.find('[required]');
                    if ($required_fields.length > 0) {
                        $required_fields.each(function() {
                            var $thisRequiredField = $(this);
                            if (!$thisRequiredField.hasClass('required-when-visible')) {
                                $thisRequiredField.addClass('required-when-visible');
                            }
                            $thisRequiredField.attr('aria-required', 'false').removeAttr('required');
                        });
                    }
                    //Search through the fields that are being hidden, and if they are controllers themselves,
                    //toggle them off and hide their controlled fields
                    if ($controlled.length) {
                        $controlled.each(function(i, value) {
                            var $c = $(this).find('.au-cf7-controller');
                            if ($c.length) {
                                //console.info('One of the fields you are hiding is a controller, so hide its fields!');
                                auControllableFields.toggleCheckbox($c, false);
                                auControllableFields.toggleControlledFields($c.attr('id'), false);
                            }
                        });
                    }
                    $controller.attr('aria-expanded', 'false'); // Update ARIA flag
                }
            }
        }
    };
$(document).ready(auControllableFields.init);