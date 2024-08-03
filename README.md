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

Be sure to call the following at the top of any file that you use the PostTypeExtended class:

```
use BuiltNorth\PostTypesConstructor\PostTypeExtended;
```

Now, let's create a sample post type called 'example_one'. Note the lowercase name and use of underscores. It will be converted to `Example One` where expected. There is also options to define all default args as well, so you could set the plural setting to `Our Examples`, `Examples`, etc.

```
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

Additionally, existing post types can be extended with options as well. For example if you want to add a custom taxonomy to posts and show the featured image in the admin columns:

```
new PostTypeExtended([
	'post_type' => [
		'name' => 'post',
		'show_featured_image' => true,
	],
	'taxonomies' => [
		[
			'name' => 'custom_taxonomy',
		]
	],
]);
```

## Configuration Options

### Post Type Configuration

-   `post_type`:
    -   `name`: (string) The name of the post type.
    -   `prefix`: (string) A prefix for the post type name.
    -   `slug`: (string) The slug for the post type URLs.
    -   `archive`: (string) The archive slug for the post type.
    -   `singular`: (string) The singular label for the post type.
    -   `plural`: (string) The plural label for the post type.
    -   `show_featured_image`: (boolean) Whether to show the featured image in admin columns.
    -   `args`: (array) Additional arguments for register_post_type().
        -   `public`: (boolean) Whether the post type is publicly queryable.
        -   `publicly_queryable`: (boolean) Whether queries can be performed on the front end.
        -   `show_ui`: (boolean) Whether to generate a default UI for managing this post type.
        -   `show_in_menu`: (boolean) Where to show the post type in the admin menu.
        -   `menu_position`: (integer) The position in the menu order the post type should appear.
        -   `menu_icon`: (string) The URL to the icon to be used for this menu.
        -   `capability_type`: (string) The string to use to build the read, edit, and delete capabilities.
        -   `hierarchical`: (boolean) Whether the post type is hierarchical.
        -   `supports`: (array) Core features the post type supports.
        -   `has_archive`: (boolean) Whether the post type has an archive page.
        -   `rewrite`: (array) Triggers the handling of rewrites for this post type.
        -   `query_var`: (boolean|string) Sets the query_var key for this post type.

### Taxonomies

-   `taxonomies`: (array) An array of taxonomy configurations.
    -   `name`: (string) The name of the taxonomy.
    -   `slug`: (string) The slug of the taxonomy.
    -   `singular`: (string) The singular label for the taxonomy.
    -   `plural`: (string) The plural label for the taxonomy.
    -   `args`: (array) Additional arguments for register_taxonomy().
        -   `hierarchical`: (boolean) Whether the taxonomy is hierarchical.
        -   `public`: (boolean) Whether the taxonomy is publicly queryable.
        -   `show_ui`: (boolean) Whether to generate a default UI for managing this taxonomy.
        -   `show_admin_column`: (boolean) Whether to allow automatic creation of taxonomy columns on associated post-types table.
        -   `query_var`: (boolean|string) Sets the query_var key for this taxonomy.
        -   `rewrite`: (array) Triggers the handling of rewrites for this taxonomy.

### Post Meta

-   `post_meta`: (array) An array of custom fields configurations.
    -   `name`: (string) The name of the custom field.
    -   `type`: (string) The data type of the field (e.g., 'string', 'integer', 'boolean').
    -   `description`: (string) A description of the field.
    -   `default`: (mixed) The default value for the field.

### Admin Columns

-   `admin_columns`: (array) An array of admin column configurations.
    -   `name`: (string) The name of the column.
    -   `label`: (string) The label for the column header.
    -   `meta_key`: (string) The meta key to display (for custom fields).
    -   `callback`: (callable) A custom callback function to display column content.
    -   `width`: (string) The width of the column (e.g., '100px').

### Additional Settings

-   `title_text`: (string) Custom text for the title input placeholder.
-   `pagination`: (integer) Number of items to show per page in the admin list.
-   `remove_meta_box`: (boolean) Whether to remove the default custom fields meta box.

## Roadmap

-   In admin columns, add option to make taxonomies and meta fields sortable.
-   In admin columns, add option for inline editing.

## Disclaimer

This software is provided "as is", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. In no event shall the authors or copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other dealings in the software.

Use of this library is at your own risk. The authors and contributors of this project are not responsible for any damage to your website or any loss of data that may result from the use of this library.

While we strive to keep this library up-to-date and secure, we make no guarantees about its performance, reliability, or suitability for any particular purpose. Users are advised to thoroughly test the library in a safe environment before deploying it to a live site.

By using this library, you acknowledge that you have read this disclaimer and agree to its terms.

```

```
