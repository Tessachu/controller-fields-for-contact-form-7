=== PRODUCT_NAME ===
Contributors: tessawatkinsllc
Donate link: https://just1voice.com/donate/
Tags: Contact Form 7, conditional, controllable, dynamic form, form field
Tested up to: 6.5
Stable tag: VERSION_PLACEHOLDER

Hide or display content in your forms created in Contact Form 7 based on user selections!

== Description ==

Contact Form 7 is an excellent WordPress plugin and one of the top choices of free WordPress plugins for contact forms. Controller Fields makes it even more awesome by adding dynamic content capabilities. This plugin provides new form tags for select fields, checkboxes, and radio buttons so you can use them to control the display of other fields and content. Some examples might include:

* Hiding or revealing additional form fields based the current value in a dropdown
* Hiding or revealing content when users click a checkbox
* Hiding or revealing information based on which radio button is currently selected
* Controllers and controlled fields can be nested

The possibilities are endless!

= View Demo =

[Preview this plugin](https://wordpress.org/plugins/TEXTDOMAIN_PLACEHOLDER/?preview=1) in the WordPress Playground, a sandbox environment where you can explore the features of this plugin, both in the WordPress backend as an admin user and on the frontend.

= WHAT DOES IT DO? =

This plugin creates three (3) new form tags for controlling the appearance of other form tags (aka "controller") and a special form tag generator button to output the HTML needed to wrap the controllable form tags inside.

= HOW TO USE IT =

After installing and activating the plugin, you will have four (4) new buttons at the top when creating or editing a Contact Form 7 form: select controller, checkbox controller, radio controller, and controlled form tag wrapper. Most of the options in their tag generators will be familiar to Contact Form 7 users but there have been some upgrades.

**Id Attribute**

The frontend script relies on unique IDs for the controller form tags, so be sure to always set one!

**Options**

There are three (3) ways you can define your options. The first method is simply typing your options with each one on a new line, e.g.:

<pre>Apples
Bananas
Dragonfruit</pre>

The second method is similar, but it allows you more control over the value and label by using ` | ` to separate them, e.g.:

<pre>fruit_1 | Apples
fruit_2 | Bananas
fruit_3 | Dragonfruit</pre>

The third method is dynamic in that you can use a shortcode to populate your options with two important provisions:

1. The shortcode should NOT include the normal square brackets (`[` and `]`). So, instead of `[my_shortcode key='value']` you would use `my_shortcode key='value'`.
1. Any parameters in the shortcode must use single quotes. That is: `my_shortcode key='value'` and not `my_shortcode key="value"`

Shortcodes used here should return a string value with the option or option group HTML.

**Default value**

This field can take static text or a shortcode. If using a shortcode, the same syntax applies from the options field. However, this field also has a few more needs:

1. The text/shortcode must first have apostrophes converted to it's HTML entity code, `&#39;`
1. After that, it must be URL encoded so that spaces become `%20` and other non-alphanumeric characters are converted.

**Placeholder**

Only available for the select controller form tag, this field can take static text or a shortcode.  If using a shortcode, the same syntax applies from the options field. However, this field also has a few more needs:

1. The text/shortcode must first have apostrophes converted to it's HTML entity code, `&#39;`
1. After that, it must be URL encoded so that spaces become `%20` and other non-alphanumeric characters are converted.

**Read Only Attribute**

Simply check this box if you do not want to let users edit this field. It will add the `readonly` attribute to your form field.

**Disabled Attribute**

Simply check this box if you do not want to submit this field in the form. It will add the `disabled` attribute to your form field.

== Installation ==

= Minimum Requirements =

To ensure your WordPress installation meets these requirements, you can login to your WordPress website and navigate to *Tools > Site Health* and click on the *Info* tab. Expand the *WordPress*, *Active Plugins*, and *Server* accordions and compare that information with the details below.

* WordPress version 5.5 or greater
* PHP version 7.4 or greater
* [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) version 5.7 or greater

There are three (3) ways to install my plugin: automatically, upload, or manually.

= Install Method 1: Automatic Installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser.

1. Log in to your WordPress dashboard.
1. Navigate to **Plugins > Add New**.
1. Where it says “Keyword” in a dropdown, change it to “Author”
1. In the search form, type “TessaWatkinsLLC” (results may begin populating as you type but my plugins will only show when the full name is there)
1. Once you’ve found my plugin in the search results that appear, click the **Install Now** button and wait for the installation process to complete.
1. Once the installation process is completed, click the **Activate** button to activate my plugin.

= Install Method 2: Upload via WordPress Admin =

This method involves is a little more involved. You don’t need to leave your web browser, but you’ll need to download and then upload the files yourself.

1. [Download my plugin](https://wordpress.org/plugins/TEXTDOMAIN_PLACEHOLDER/) from WordPress.org; it will be in the form of a zip file.
1. Log in to your WordPress dashboard.
1. Navigate to **Plugins > Add New**.
1. Click the **Upload Plugin** button at the top of the screen.
1. Select the zip file from your local file system that was downloaded in step 1.
1. Click the **Install Now** button and wait for the installation process to complete.
1. Once the installation process is completed, click the **Activate** button to activate my plugin.

= Install Method 3: Manual Installation =

This method is the most involved as it requires you to be familiar with the process of transferring files using an SFTP client.

1. [Download my plugin](https://wordpress.org/plugins/TEXTDOMAIN_PLACEHOLDER/) from WordPress.org; it will be in the form of a zip file.
1. Unzip the contents; you should have a single folder named `TEXTDOMAIN_PLACEHOLDER`.
1. Connect to your WordPress server with your favorite SFTP client.
1. Copy the folder from step 2 to the `/wp-content/plugins/` folder in your WordPress directory. Once the folder and all of its files are there, installation is complete.
1. Now log in to your WordPress dashboard.
1. Navigate to **Plugins > Installed Plugins**. You should now see my plugin in your list.
1. Click the **Activate** button under my plugin to activate it.

== Screenshots ==

1. A screenshot of the four (4) new buttons added in the Contact Form 7 form edit screen.
2. Form-tag generator for the select controller
3. Form-tag generator for the checkbox controller
4. Form-tag generator for the radio controller
5. Form-tag generator for the controlled form tag wrapper
6. Example of a select controller field configured to control a text field in the form edit screen
7. Animated GIF example of the user selecting the option in the select controller field to hide and show the text field

== Frequently Asked Questions ==

Please check out the [FAQ on our website](https://aurisecreative.com/docs/TEXTDOMAIN_PLACEHOLDER/frequently-asked-questions/?utm_source=wordpress.org&utm_medium=link&utm_campaign=TEXTDOMAIN_PLACEHOLDER&utm_content=readme).

== Upgrade Notice ==

= VERSION_PLACEHOLDER =
Major updates to script function to allow for more flexibiity, including operations, multiple controllers, numeric value checks, and a bug fix! Tag generator updates coming soon! See [the changelog](https://plugins.trac.wordpress.org/browser/TEXTDOMAIN_PLACEHOLDER/trunk/changelog.txt) for more details.

== Changelog ==

= 1.1.1 =

**Submission Date: April 16, 2024**

* Fix: Addressed a bug introduced in version 1.1.0 that prevented frontend assets from loading.


= 1.1.0 =

**Submission Date: April 15, 2024**

* Feature: Number fields can now be controllers. They display controlled fields based on number ranges set in the controlled element's values.
* Feature: Controlled fields can now have multiple controllers. The `data-controller` value should be a pipe-delimited list of controller IDs. If it has multiple controllers, then there should be a `data-{controller ID}-values` attribute that sets the values for that specific controller. Controlled fields with multiple controllers are only hidden if all controllers evaluate to no matches. Otherwise, it will remain visible. Multiple controllers are treated as an OR comparison, meaning at least one controller must be active for the controlled field to display. Nest them to treat them as an AND operation.
* Feature: Values can include operations such as `>`, `>=`, `<`, `<=`, `!=`, `*`, `BLANK`, `EMPTY_OR_ZERO`. The operation `=` is assumed when no comparison is found.
* Fix: added `!important` to `au-cf7-controller` CSS to ensure it's hidden in case of conflicting styles.

= 1.0.2 =

**Submission Date: May 5, 2023**

* Fix: modifications from plugin review

= 1.0.1 =

**Submission Date: May 1, 2023**

* Fix: modifications from plugin review

= 1.0.0 =

**Submission Date: March 30, 2023**

* Major: first submission!

= Full Changelog =

Please see our [additional changelog.txt file](https://plugins.trac.wordpress.org/browser/TEXTDOMAIN_PLACEHOLDER/trunk/changelog.txt)