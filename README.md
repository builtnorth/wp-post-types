# WP Post Types

Composer package containing constructor classes to easily register post types and related functions in a theme or plugin.

## Requirements

-   PHP >= 8.1
-   WordPress >= 6.4

## Installation

This library is meant to be dropped into a theme or plugin via composer: `composer require builtnorth/wp-post-types`

## Features

This library contains a number of features that streamline the process of creating custom post types. It is reccomended to register a class for each of your post types to streamline registration of all features, but that is not neccessary.

Note that features should be registered on the `init` hook at a priority less than `10`.

-   Post Type Registration
-   Taxonmy Registration
-   Post Meta Registration
-   Admin Column Setup

### Post Type Registration

To register a post type, simply call the follwoing in your theme or plugin. `args` are all optional, and any valid arguments can be used.

```
<?php
	if (class_exists('BuiltNorth\PostTypesConstructor\PostType')) {
		new \BuiltNorth\PostTypesConstructor\PostType(
			prefix: 'your_prefix_',
			name: 'example',
			slug: 'example',
			archive: 'examples',
			singular: 'Example',
			plural: 'Examples',
			args: [
				'menu_icon' => 'dashicons-index-card',
				'supports' => [
					'editor', 'title', 'thumbnail', 'page-attributes', 'custom-fields'
				],
				'hierarchical' => false,
			]
		);
	}
?>
```

### Taxonomy Registration

```
<?php
	if (class_exists('BuiltNorth\PostTypesConstructor\Taxonomy')) {
		new \BuiltNorth\PostTypesConstructor\Taxonomy(
			prefix: 'your_prefix_',
			name: 'type',
			slug: 'types',
			singular: 'Type',
			plural: 'Types',
			post_type_name: 'example',
			args: [
				'hierarchical' => true,
			]
		);
	}
<?php>
```

### Post Meta Registration

```
if (class_exists('BuiltNorth\PostTypesConstructor\PostMeta')) {
		new \BuiltNorth\PostTypesConstructor\PostMeta(
			prefix: 'your_prefix_',
			post_type_name: 'example',
			meta: [
				'sample_text' => [
					'type' => 'string'
				],
				'sample_boolean' => [
					'type' => 'boolean',
					'default' => false
				],
				'sample_integer' => [
					'type' => 'integer',
					'sanitize_callback' => 'absint'
				]
			]
		);
	}
```

### Admin Columns Setup

```
<?php
	if (class_exists('BuiltNorth\PostTypesConstructor\AdminColumns')) {
		new \BuiltNorth\PostTypesConstructor\AdminColumns(
			prefix: 'your_prefix_',
			post_type_name: 'example',
			columns: [
				'sample_text' => [
					'label' => __('Sample Text', 'compass-companion'),
					'meta_key' => 'your_prefix_example_sample_text',
					'width' => '15%',
				],
			]
		);
	}
?>
```

## Disclaimer

This software is provided "as is", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. In no event shall the authors or copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other dealings in the software.

Use of this library is at your own risk. The authors and contributors of this project are not responsible for any damage to your website or any loss of data that may result from the use of this library.

While we strive to keep this library up-to-date and secure, we make no guarantees about its performance, reliability, or suitability for any particular purpose. Users are advised to thoroughly test the library in a safe environment before deploying it to a live site.

By using this library, you acknowledge that you have read this disclaimer and agree to its terms.
