<?php
require_once('/wordpress/wp-load.php');

wp_insert_post(array(
    'ID' => 2,
    'post_author' => 1,
    'post_type' => 'page',
    'post_status' => 'publish',
    'post_name' => 'example-page-with-dynamic-form',
    'post_title' => 'Example Page with Dynamic Form',
    'post_content' => '<!-- wp:contact-form-7/contact-form-selector {"id":4,"hash":"d3m0For","title":"Demo Form"} -->
<div class="wp-block-contact-form-7-contact-form-selector">[contact-form-7 id="d3m0For" title="Demo Form"]</div>
<!-- /wp:contact-form-7/contact-form-selector -->',
    'comment_status' => 'closed',
    'ping_status' => 'closed'
));

wp_insert_post(array(
    'ID' => 4,
    'post_type' => 'wpcf7_contact_form',
    'post_status' => 'publish',
    'post_title' => 'Demo Form',
    'post_author' => 1,
    'comment_status' => 'closed',
    'ping_status' => 'closed',
    'meta_input' => array(
        '_form' => '<div><h2>Drop-down menu as Controller</h2>
    <p>
        <label>
            <span class="label">Select a fruit</span>
            [select_controller fruits id:my-fruit "Apples" "Bananas" "Dragon fruit"]
        </label>
    </p>
    <div>
        <p><strong>Content below me will change based on your fruit of choice!</strong></p>
        <div data-controller="my-fruit" data-values="Apples" class="au-cf7-hidden-by-controller">
            <p>How you like them apples?</p>
        </div>
        <div data-controller="my-fruit" data-values="Bananas" class="au-cf7-hidden-by-controller">
            <p>Bananas are my favorite fruit.</p>
        </div>
        <div data-controller="my-fruit" data-values="Dragon fruit" class="au-cf7-hidden-by-controller">
            <p>I give this a 0/10 because it is not a dragon.</p>
        </div>
    </div>
</div>
<hr />
<div>
    <h2>Checkboxes as Controller</h2>
    <p>
        <label>
            <span class="label">Favorite Drink<br /><small>Check all that may apply</small></span>
            [checkbox_controller drinks use_label_element "hot | Hot Drinks" "iced | Iced Drinks" "frozen | Frozen Drinks"]
        </label>
    </p>
    <div>
        <p><strong>Content below me will change based on your chosen drink(s)!</strong></p>
        <div data-controller="drinks" data-values="hot|frozen" class="au-cf7-hidden-by-controller">
            <p>I like both hot and frozen, I am fire and ice, baby!</p>
        </div>
        <div data-controller="drinks" data-values="iced|frozen" class="au-cf7-hidden-by-controller">
            <p>Only Iced and frozen for me!</p>
        </div>
        <div data-controller="drinks" data-values="FALSEY" class="au-cf7-hidden-by-controller">
            <p>I hate everything equally.</p>
        </div>
    </div>
</div>
<hr />
<div>
    <h2>Radio buttons as Controller</h2>
    <p>
        <label>
            <span class="label">RPG Role:</span>&nbsp;
            [radio_controller rpg use_label_element "tank | Tank" "healer | Healer" "damage-dealer | Damage Dealer"]
        </label>
    </p>
    <div>
        <p><strong>Content below me will change based on your role!</strong></p>
        <div data-controller="rpg" data-values="tank" class="au-cf7-hidden-by-controller">
            <p>Give me everything you got!</p>
        </div>
        <div data-controller="rpg" data-values="healer" class="au-cf7-hidden-by-controller">
            <p>I got \'chu fam</p>
        </div>
        <div data-controller="rpg" data-values="damage-dealer" class="au-cf7-hidden-by-controller">
            <p><em>YEET!</em></p>
        </div>
    </div>
</div>
<div>
    <h2>Number field as Controller</h2>
    <p>
        <label>
            <span class="label">What is your annual gross income?</span>&nbsp;
            [number us_tax_bracket id:tax-bracket class:au-controller min:0 step:1000 "50000"]
        </label>
    </p>
    <div>
        <p><strong>Content below me will change based on your numeric answer above.</strong></p>
        <div data-controller="tax-bracket" data-values="<=30000" class="au-cf7-hidden-by-controller">
            <p>Based on your income, you fall in the <strong>lower class</strong> in the USA. This is the lowest class and you have four classes above you.</p>
        </div>
        <div data-controller="tax-bracket" data-values="<=58020" class="au-cf7-hidden-by-controller">
            <p>Based on your income, you fall in the <strong>lower-middle class</strong> in the USA. You have one class below you and three classes above you.</p>
        </div>
        <div data-controller="tax-bracket" data-values="<=94000" class="au-cf7-hidden-by-controller">
            <p>Based on your income, you fall in the <strong>middle class</strong> in the USA. You have two classes below you and two classes above you.</p>
        </div>
        <div data-controller="tax-bracket" data-values="<=153000" class="au-cf7-hidden-by-controller">
            <p>Based on your income, you fall in the <strong>upper-middle class</strong> in the USA. You have three classes below you and one class above you.</p>
        </div>
        <div data-controller="tax-bracket" data-values=">153000" class="au-cf7-hidden-by-controller">
            <p>Based on your income, you fall in the <strong>upper class</strong> in the USA. This is the highest class and you have four classes below you.</p>
        </div>
    </div>
</div>',
        '_mail' => array(),
        '_mail2' => array(),
        '_messages' => array(),
        '_additional_settings' => 'demo_mode: on' . PHP_EOL . 'skip_mail: on',
        '_locale' => 'en_US',
        '_hash' => 'd3m0Form'
    )
));
