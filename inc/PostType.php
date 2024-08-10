<?php

namespace BuiltNorth\PostTypesConstructor;

class PostType
{
	public $name;
	protected $slug;
	protected $archive;
	protected $singular;
	protected $plural;
	protected $args;
	protected $labels;
	protected $capabilities;
	protected $mapMetaCap;

	public function __construct(string $key, array $config = [])
	{
		$this->name = $config['name'] ?? $this->formatPostTypeName($key);
		$this->slug = $config['slug'] ?? sanitize_title_with_dashes($key);
		$this->archive = $config['archive'] ?? $this->slug;
		$this->singular = $config['singular'] ?? ucfirst(str_replace(['_', '-'], ' ', $key));
		$this->plural = $config['plural'] ?? $this->singular . 's';
		$this->args = $config['args'] ?? [];
		$this->labels = $config['labels'] ?? [];
		$this->capabilities = $config['capabilities'] ?? [];
		$this->mapMetaCap = $config['map_meta_cap'] ?? true;
	}

	public static function formatPostTypeName($key)
	{
		return str_replace([' ', '-'], '_', strtolower($key));
	}

	public function register_post_type()
	{
		$labels = $this->prepare_labels();
		$args = $this->prepare_args($labels);

		register_post_type($this->name, $args);
	}

	protected function prepare_labels()
	{
		$default_labels = self::get_default_labels($this->singular, $this->plural);
		return array_merge($default_labels, $this->labels);
	}

	protected function prepare_args($labels)
	{
		$default_args = array(
			'labels'              => $labels,
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_rest'        => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => $this->archive,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => array(
				'slug'       => $this->slug,
				'with_front' => false
			),
			'supports'            => array(
				'editor',
				'excerpt',
				'page-attributes',
				'title',
				'thumbnail',
			),
			'capability_type'     => 'post',
			'map_meta_cap'        => $this->mapMetaCap,
		);

		if (!empty($this->capabilities)) {
			$default_args['capabilities'] = $this->capabilities;
		}

		return array_merge($default_args, $this->args);
	}

	public static function get_default_labels($singular, $plural)
	{
		return [
			'name'                  => _x($plural, 'Post type general name', 'built'),
			'singular_name'         => _x($singular, 'Post type singular name', 'built'),
			'menu_name'             => _x($plural, 'Admin Menu text', 'built'),
			'name_admin_bar'        => _x($singular, 'Add New on Toolbar', 'built'),
			'add_new'               => __('Add New', 'built'),
			'add_new_item'          => __("Add New {$singular}", 'built'),
			'new_item'              => __("New {$singular}", 'built'),
			'edit_item'             => __("Edit {$singular}", 'built'),
			'view_item'             => __("View {$singular}", 'built'),
			'all_items'             => __("All {$plural}", 'built'),
			'search_items'          => __("Search {$plural}", 'built'),
			'parent_item_colon'     => __("Parent {$plural}:", 'built'),
			'not_found'             => __("No {$plural} found.", 'built'),
			'not_found_in_trash'    => __("No {$plural} found in Trash.", 'built'),
			'featured_image'        => _x("{$singular} Cover Image", 'Overrides the "Featured Image" phrase for this post type.', 'built'),
			'set_featured_image'    => _x("Set cover image", 'Overrides the "Set featured image" phrase for this post type.', 'built'),
			'remove_featured_image' => _x("Remove cover image", 'Overrides the "Remove featured image" phrase for this post type.', 'built'),
			'use_featured_image'    => _x("Use as cover image", 'Overrides the "Use as featured image" phrase for this post type.', 'built'),
			'archives'              => _x("{$singular} archives", 'The post type archive label used in nav menus.', 'built'),
			'insert_into_item'      => _x("Insert into {$singular}", 'Overrides the "Insert into post"/"Insert into page" phrase (used when inserting media into a post).', 'built'),
			'uploaded_to_this_item' => _x("Uploaded to this {$singular}", 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase (used when viewing media attached to a post).', 'built'),
			'filter_items_list'     => _x("Filter {$plural} list", 'Screen reader text for the filter links heading on the post type listing screen.', 'built'),
			'items_list_navigation' => _x("{$plural} list navigation", 'Screen reader text for the pagination heading on the post type listing screen.', 'built'),
			'items_list'            => _x("{$plural} list", 'Screen reader text for the items list heading on the post type listing screen.', 'built'),
		];
	}

	public static function modify_existing($name, $config)
	{
		add_action('init', function () use ($name, $config) {
			global $wp_post_types;

			if (isset($wp_post_types[$name])) {
				$post_type = &$wp_post_types[$name];

				$singular = $config['singular'] ?? null;
				$plural = $config['plural'] ?? null;

				$default_labels = self::get_default_labels($singular, $plural);
				$custom_labels = $config['labels'] ?? [];

				$merged_labels = array_merge(
					(array) $post_type->labels,
					$default_labels,
					$custom_labels
				);

				$post_type->labels = (object) $merged_labels;
				$post_type->label = $merged_labels['name'];

				if (isset($config['args'])) {
					foreach ($config['args'] as $key => $value) {
						$post_type->$key = $value;
					}
				}

				if (isset($config['capabilities'])) {
					$post_type->cap = (object) array_merge((array) $post_type->cap, $config['capabilities']);
				}

				if (isset($config['map_meta_cap'])) {
					$post_type->map_meta_cap = $config['map_meta_cap'];
				}
			}
		}, 999);
	}
}
