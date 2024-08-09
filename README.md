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

Initiate the class somehwere in your plugin or theme with one of the following depenfing on the method you choose. There are two ways to register post types and related. Feel free to use whichever you prefer:

```php
use BuiltNorth\PostTypesConstructor\PostTypeExtended;

if (class_exists(PostTypeExtended::class)) {

	// Use for JSON Config
	add_action('after_setup_theme', function () {
		PostTypeExtended::init();
	});

	// Use for PHP config
	add_action('after_setup_theme', function() {
	    $config = [
	        // Your custom configuration here
	    ];
	    PostTypeExtended::setConfig($config);
	}, 9);
}
```

1. The standard way is to add a post-type.config.json file to the root of your plugin or theme.
2. The alternate way is to initiate configure via php.

Minimal post type registration via JSON:

```json
{
    "post_types": {
        "sample": {
            "name": "prefix_sample",
		]
	]
}
```

Minimal post type registration via PHP:

```php
$config = [
	[
		'post_types' => [
			'sample' => [
				'name' => 'prefix_sample',
			]
		]
	]
]
```

Below we will take a look at more advanced examples. In the examples, both methods are registering the exact same items. The examples also try and demonstrate the full capabilitie of the library.

Here is what's happening in the examples:

1. First, register a post type called `sample`.
    - `sample` is used only as a key though, as for best practices the actual post type name is `prefix_sample`. The prefix is not required, but reccomended.
    - We are setting the menu icon and supports.
2. Next, modify the existing/standard `post` post type.
    - Update the singular name and plural names.
    - Change the menu icon.
3. Next, register the taxonomy `example_category` (key)
    - Add the actual prefixed name of `prefix_example_category`.
    - Change the plural name.
    - Assign the taxonomy to `sample` and `post` post types.
4. Then, register post meta for `sample`.
5. Set up admin columns for the post meta that was registered.
    - Also set the featured image for `posts` to display in the admin columns.
6. Finally, add some extra settings to `sample` and `post`.

### Usage Method 1 (JSON Registration)

Make sure you have a post-type.config.json file at the root of your plugin or theme. Within the file use the following format for registration.

```json
{
    "post_types": {
        "sample": {
            "name": "prefix_sample",
            "args": {
                "menu_icon": "dashicons-paperclip",
                "supports": ["title", "editor", "thumbnail", "custom-fields"]
            }
        },
        "post": {
            "name": "post",
            "singular": "Article",
            "plural": "News",
            "args": {
                "menu_icon": "dashicons-megaphone"
            }
        }
    },
    "taxonomies": {
        "example_category": {
            "name": "prefix_example_category",
            "plural": "Example Categories",
            "post_types": ["sample", "post"],
            "args": {
                "hierarchical": false
            }
        }
    },
    "post_meta": {
        "sample": {
            "meta": {
                "name": "custom_field",
                "type": "string",
                "description": "A custom meta field"
            }
        }
    },
    "admin_columns": {
        "sample": {
            "show_featured_image": true,
            "columns": [
                {
                    "name": "custom_field",
                    "label": "Custom Field"
                }
            ]
        },
        "post": {
            "show_featured_image": true
        }
    },
    "extras": {
        "sample": {
            "title_text": "Enter Sample Post Title Here",
            "pagination": 9
        },
        "post": {
            "title_text": "Enter News Article Title Here",
            "pagination": 12
        }
    }
}
```

### Usage Method 2 (PHP Registration)

Somewhere in a file such as functions.php, add the follwing. Once added, you should have post types with settings.

```php
	$config = [
		[
			'post_types' => [
				'sample' => [
					'name' => 'prefix_sample',
					'args' => [
						'menu_icon' => 'dashicons-paperclip',
						'supports' => ['title', 'editor', 'thumbnail', 'custom-fields']
					]
				],
				'post' => [
					'name' => 'post',
					'singular' => 'Article',
					'plural' => 'News',
					'args' => [
						'menu_icon' => 'dashicons-megaphone'
					]
				]
			],
			'taxonomies' => [
				'example_category' => [
					'name' => 'prefix_example_category',
					'plural' => 'Example Categories',
					'post_types' => ['sample', 'post'],
					'args' => [
						'hierarchical' => false
					]
				]
			],
			'post_meta' => [
				'sample' => [
					'meta' => [
						'name' => 'custom_field',
						'type' => 'string',
						'description' => 'A custom meta field'
					]
				]
			],
			'admin_columns' => [
				'sample' => [
					'show_featured_image' => true,
					'columns' => [
						[
							'name' => 'custom_field',
							'label' => 'Custom Field'
						]
					]
				],
				'post' => [
					'show_featured_image' => true
				]
			],
			'extras' => [
				'sample' => [
					'title_text' => 'Enter Sample Post Title Here',
					'pagination' => 9
				],
				'post' => [
					'title_text' => 'Enter News Article Title Here',
					'pagination' => 12
				]
			]
		]
];
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
        -   `show_in_rest`: (boolean)Expose to the Rest API or not.
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
