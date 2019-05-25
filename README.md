# Dynamic Stylesheet

Simple drop in class for generating dynamic stylesheets in WordPress.

## About

This class will try to write the custom CSS to a static file in the `wp-content/cache/` directory. If there is no write access, it will load the dynamic stylesheet instead.

## Usage

Copy the `loader.php` file somewhere in your theme or plugin and then add the following code:

```php
// Include loader class (not required if using composer).
require_once __DIR__ 'path/to/loader.php';

// My custom CSS to generate.
$custom_color = 'red'
$custom_css   = 'body { background-color: ' . $custom_color . ' !important; }';

// Initialize dynamic stylesheet.
$dynamic_css = new SeoThemes\DynamicStylesheet\Loader( $custom_css );

// Run hooks.
$dynamic_css->run();
```
