# Dynamic Stylesheet

Simple drop in class for generating dynamic stylesheets in WordPress.

## About

This class will try to write the custom CSS to a static file in the `wp-content/cache/` directory. If there is no write access, it will load the dynamic stylesheet instead.

## Installation

__Composer (recommended)__

Run the following command from the root directory of your theme or plugin:

```shell
composer require seothemes/dynamic-stylesheet
```

__Manual__

Copy the `loader.php` file somewhere in your theme or plugin and then include it using `require_once`, e.g:

```php
// Include loader class (not required if using composer).
require_once __DIR__ . 'path/to/loader.php';
```

## Usage

Pass a string of custom CSS to the Loader class parameters and then load the hooks using the `run` method. It is recommended to generate your CSS inside a function that is also hooked to `wp_enqueue_scripts` to enable live preview in the Customizer. The stylesheet handle and option name can also be specified when initializing the Loader class with the second parameter:

```php
add_action( 'wp_enqueue_scripts', 'prefix_custom_css', 15 );
/**
 * Generate custom CSS and add inline styles to Customizer preview.
 *
 * @since 1.0.0
 *
 * @return string
 */
function prefix_custom_css() {
	$custom_color = get_option( 'custom-color', '#eee' );
  
	$custom_css   = 'body { background-color: ' . $custom_color . ' !important; }';

	if ( is_customize_preview() ) {
		wp_add_inline_style('dynamic', $custom_css);
	}

	return $custom_css;
}

// Initialize Loader class (pass custom CSS as parameter here).
$dynamic_css = new SeoThemes\DynamicStylesheet\Loader( prefix_custom_css(), 'my-prefix' );

// Load hooks.
$dynamic_css->run();
```

## Testing

__Customizer Settings__

To test that the Customizer settings are working correctly, add the following code snippet:

```php
add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->add_setting( 'custom-color', [
		'type' => 'option',
	] );

	$wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, 'custom-color', [
		'label'    => 'Custom Color',
		'section'  => 'colors',
		'settings' => 'custom-color',
	] ) );
} );
```

This will add a simple color control under the Colors section of the Customizer. If changes are not applied, try clearing the browser cache.




