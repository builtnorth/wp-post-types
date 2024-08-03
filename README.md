# WP Post Types

Composer package for WordPress with a utility class that simplifies the process of creating and managing custom post types with extended functionality. It provides an easy-to-use interface for setting up custom post types, taxonomies, meta fields, and admin columns.

## Features

-   **Custom Post Type Creation**: Easily create custom post types with a wide range of configurable options.
-   **Custom Taxonomy Support**: Add custom taxonomies to your post types.
-   **Custom Meta Fields**: Define and manage custom meta fields for your post types.
-   **Admin Columns**: Customize the columns displayed in the WordPress admin area for your post types.
-   **Featured Image Column**: Optionally display a featured image column in the admin list view.
-   **Custom Title Placeholder**: Set a custom placeholder text for the title field.
-   **Pagination Control**: Set custom pagination for archive pages of your post type.
-   **Legacy Meta Box Removal**: Option to remove the default custom fields meta box.

## Requirements

-   PHP >= 8.1
-   WordPress >= 6.4

## Installation

This library is meant to be dropped into a theme or plugin via composer: `composer require builtnorth/wp-post-types`

## Usage

Here's a basic example of how to use PostTypeExtended:

```
use BuiltNorth\PostTypesConstructor\PostTypeExtended;

new PostTypeExtended([
    'post_type' => [
        'prefix' => 'my_',
        'name' => 'example_one',
        'show_featured_image' => true,
        'args' => [
            'menu_icon' => 'dashicons-admin-post',
            'supports' => ['title', 'editor', 'thumbnail'],
        ]
    ],
    'taxonomies' => [
        [
			'prefix' => 'my_',
            'name' => 'custom_category',
            'args' => [
                'hierarchical' => true,
            ]
        ]
    ],
    'post_meta' => [
        [
            'name' => 'custom_field',
            'type' => 'string',
            'description' => 'A custom meta field',
        ],
    ],
    'admin_columns' => [
        [
            'name' => 'custom_field',
            'label' => 'Custom Field',
        ],
    ],
    'title_text' => 'Enter Custom Examlple Title Here',
    'pagination' => 10,
    'remove_meta_box' => true,
]);
```

## Configuration Options

-   post_type: Define the custom post type settings.
-   taxonomies: Array of custom taxonomies to be associated with the post type.
-   post_meta: Array of custom meta fields for the post type.
-   admin_columns: Customize the admin columns for the post type.
-   title_text: Set a custom placeholder for the title field.
-   pagination: Set the number of items per page in archive views.
-   remove_meta_box: Remove the default custom fields meta box if set to true.

## Disclaimer

This software is provided "as is", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. In no event shall the authors or copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other dealings in the software.

Use of this library is at your own risk. The authors and contributors of this project are not responsible for any damage to your website or any loss of data that may result from the use of this library.

While we strive to keep this library up-to-date and secure, we make no guarantees about its performance, reliability, or suitability for any particular purpose. Users are advised to thoroughly test the library in a safe environment before deploying it to a live site.

By using this library, you acknowledge that you have read this disclaimer and agree to its terms.
