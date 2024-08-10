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

To use PostTypeManager, you need to instantiate the class and call its `init()` method. The way you do this can vary depending on whether you're using it in a theme or a plugin.

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

## Advanced Usage with PostTypeManager

If you're using the PostTypeManager class to manage your PostTypeManager instance, you can set it up like this:

```php
use BuiltNorth\Polaris\Features\PostTypeManager;

add_action('init', function() {
    $post_type_manager = new PostTypeManager();
    $post_type_manager->register_post_types();
}, 0);
```

This approach allows for additional configuration and overrides managed by the PostTypeManager class.

## Best Practices

1. Always hook into the `init` action when registering post types and taxonomies.
2. Use a priority of 0 or a low number to ensure your registrations happen early.
3. In a plugin, always use an absolute path when specifying the configuration file location.
4. Consider using a constant for the configuration file path to make it easy to change across your plugin.

By following these initialization methods, you can effectively use PostTypeManager in both theme and plugin contexts, providing flexibility in how and where you manage your custom post type configurations.
