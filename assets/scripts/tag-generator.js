(function($) {
    'use strict';
    if (typeof wpcf7 === 'undefined' || wpcf7 === null) {
        return;
    }
    window.au_cf7_controllable_fields = window.au_cf7_controllable_fields || {};
    au_cf7_controllable_fields.taggen = {};

    au_cf7_controllable_fields.taggen.escapeRegExp = function(str) {
        return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
    };

    au_cf7_controllable_fields.taggen.replaceAll = function(input, f, r, no_escape) {
        if (input !== undefined && input !== null && typeof(input) == 'string' && input.trim() !== '' && input.indexOf(f) > -1) {
            var rexp = new RegExp(au_cf7_controllable_fields.taggen.escapeRegExp(f), 'g');
            if (no_escape) { rexp = new RegExp(f, 'g'); }
            return input.replace(rexp, r);
        }
        return input;
    };

    au_cf7_controllable_fields.taggen.updateOption = function(e) {
        var $this = $(e.currentTarget),
            value = encodeURIComponent(au_cf7_controllable_fields.taggen.replaceAll($this.val(), "'", '&#39;'));
        $this.siblings('input[type="hidden"].option').val(value);
    };

    au_cf7_controllable_fields.taggen.updateWrapper = function($form) {
        var id = $form.attr('data-id');
        var name = '';
        var name_fields = $form.find('input[name="name"]');
        if (name_fields.length) {
            name = name_fields.val();
            if ('' === name) {
                name = id + '-' + Math.floor(Math.random() * 1000);
                name_fields.val(name);
            }
        }
        $form.find('textarea.tag').each(function() {
            var tag_type = $(this).attr('name');
            if ($form.find(':input[name="tagtype"]').length) {
                tag_type = $form.find(':input[name="tagtype"]').val();
            }
            var components = au_cf7_controllable_fields.taggen.compose(tag_type, $form);
            $(this).val(components);
        });
    };

    au_cf7_controllable_fields.taggen.compose = function(tagType, $form) {
        var name = $form.find('input[name="name"]').val().trim(),
            values = $form.find('textarea[name="values"]').val().trim(),
            content = '<!-- Start controlled wrapper for controller #' + name + ' -->\r\n<div data-controller="' + name + '"';
        if (values) {
            // Convert to pipe delimited list in attribute
            content += ' data-values="' + (values.replace(new RegExp("[\r\n]", "gm"), "|")) + '"';
        }
        content += ' class="au-cf7-hidden-by-controller">\r\n\r\n<!-- Insert Form Tags Here -->\r\n\r\n</div><!-- End controlled wrapper for controller #' + name + ' -->';
        return content;
    };

    $(function() {
        $('form.tag-generator-panel input.au-cf-option').on('change', au_cf7_controllable_fields.taggen.updateOption);
        $('form.tag-generator-panel[data-id="controlled_wrapper"] .control-box :input').on('change', function() {
            var $form = $(this).closest('form.tag-generator-panel');
            wpcf7.taggen.normalize($(this));
            au_cf7_controllable_fields.taggen.updateWrapper($form);
        });
        $('form.tag-generator-panel[data-id="controlled_wrapper"]').each(function() {
            au_cf7_controllable_fields.taggen.updateWrapper($(this));
        });
        $('input.insert-controller-wrapper').click(function() {
            var $form = $(this).closest('form.tag-generator-panel');
            var tag = $form.find('textarea.tag').val();
            wpcf7.taggen.insert(tag);
            tb_remove(); // close thickbox
            return false;
        });
    });
})(jQuery);