# WordPress Post Type Manager

Composer package for WordPress with a utility class that simplifies the process of creating and managing custom post types with extended functionality. It provides an easy-to-use interface for setting up custom post types, taxonomies, meta fields, and admin columns.

## Features

-   **Custom Post Type Creation**: Easily create custom post types with a wide range of configurable options.
-   **Custom Taxonomy Support**: Add custom taxonomies to your post types.
-   **Custom Meta Fields**: Define and manage custom meta fields for your post types.
-   **Admin Columns**: Customize the columns displayed in the WordPress admin area for your post types.
-   **Featured Image Column**: Optionally display a featured image column in the admin list view.
-   **Custom Title Placeholder**: Set a custom placeholder text for the title field.
-   **Pagination Control**: Set custom pagination for archive pages of your post type.
-   **Legacy Meta Box Removal**: Option to remove the default custom fields meta box. This is useful for the specific Gutenberg use case where `custom-field` support is needed for post meta, but you also want to hide the custom fields ftom the post editor.

## Requirements

-   PHP >= 8.1
-   WordPress >= 6.4

## Installation

This library is meant to be dropped into a theme or plugin via composer: `composer require builtnorth/wp-post-types`

## Basic Setup

To use PostTypeManager, you need to instantiate the class and call its `init()` method. The way you do this can vary depending on whether you're using it in a theme or a plugin. It is worth noting that if there is a case where the PostTypeManager finds a config file in a plugin and the theme, the theme will override all settings in the plugins config file.

### Theme Usage

When used in a theme, PostTypeManager will automatically look for a `post-type.config.json` file in your theme directory. Here's how to set it up:

```php
use BuiltNorth\PostTypesConstructor\PostTypeManager;

add_action('init', function() {
    $post_type_manager = new PostTypeManager();
    $post_type_manager->init();
}, 0);
```

This code should be placed in your theme's `functions.php` file or a custom plugin file that's loaded by your theme.

### Plugin Usage

When using PostTypeManager in a plugin, you'll typically want to specify the path to your configuration file explicitly. Here's how to do that:

```php
use BuiltNorth\PostTypesConstructor\PostTypeManager;

add_action('init', function() {
    $config_file = plugin_dir_path(__FILE__) . 'post-type.config.json';
    $post_type_manager = new PostTypeManager($config_file);
    $post_type_manager->init();
}, 0);
```

This code should be placed in your plugin's main PHP file or in a separate file that's included by your plugin.

### Custom Configuration Path

You can specify a custom path for your configuration file, regardless of whether you're using a theme or a plugin:

```php
$config_file = '/path/to/your/custom-config.json';
$post_type_manager = new PostTypeManager($config_file);
$post_type_manager->init();
```

### Using PHP Array Configuration

If you prefer to use a PHP array for configuration instead of a JSON file, you can do so like this:

```php
$config = [
    'post_types' => [
        // Your post type configurations
    ],
    // Other configurations
];

$post_type_manager = new PostTypeManager($config);
$post_type_manager->init();
```

## Best Practices

1. Always hook into the `init` action when registering post types and taxonomies.
2. Use a priority of 0 or a low number to ensure your registrations happen early.
3. In a plugin, always use an absolute path when specifying the configuration file location.
4. Consider using a constant for the configuration file path to make it easy to change across your plugin.

By following these initialization methods, you can effectively use PostTypeManager in both theme and plugin contexts, providing flexibility in how and where you manage your custom post type configurations.

## Disclaimer

This software is provided "as is", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. In no event shall the authors or copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other dealings in the software.

Use of this library is at your own risk. The authors and contributors of this project are not responsible for any damage to your website or any loss of data that may result from the use of this library.

While we strive to keep this library up-to-date and secure, we make no guarantees about its performance, reliability, or suitability for any particular purpose. Users are advised to thoroughly test the library in a safe environment before deploying it to a live site.

By using this library, you acknowledge that you have read this disclaimer and agree to its terms.
