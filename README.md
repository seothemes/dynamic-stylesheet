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

Pass a string of custom CSS to the Loader class parameters and then load the hooks using the `run` method:

```php
// My custom CSS to generate.
$custom_color = get_option( 'custom-color', '#eee' );
$custom_css   = 'body { background-color: ' . $custom_color . ' !important; }';

// Initialize Loader class (pass custom CSS as parameter here).
$dynamic_css = new SeoThemes\DynamicStylesheet\Loader( $custom_css );

// Load hooks.
$dynamic_css->run();
```

You can also change the stylesheet handle and option name when initializing the Loader class using the $handle parameter:

```php
$dynamic_css = new SeoThemes\DynamicStylesheet\Loader( $custom_css, 'my-prefix' );
```



