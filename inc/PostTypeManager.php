<?php

namespace BuiltNorth\PostTypesConstructor;

use BuiltNorth\PostTypesConstructor\PostType;
use BuiltNorth\PostTypesConstructor\Taxonomy;
use BuiltNorth\PostTypesConstructor\PostMeta;
use BuiltNorth\PostTypesConstructor\AdminColumns;

class PostTypeManager
{
	protected $config = [];
	protected $postTypeMap = [];

	public function __construct($config = null)
	{
		$this->loadConfig($config);
	}

	protected function loadConfig($config)
	{
		if (is_string($config) && file_exists($config)) {
			// Load JSON configuration
			$this->loadJsonConfig($config);
		} elseif (is_array($config)) {
			// Use PHP array configuration
			$this->config = $config;
		} else {
			// Try to find default JSON configuration
			$default_config = $this->findDefaultConfig();
			if ($default_config) {
				$this->loadJsonConfig($default_config);
			}
		}
	}

	protected function loadJsonConfig($file_path)
	{
		$json_config = file_get_contents($file_path);
		$this->config = json_decode($json_config, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \Exception('Invalid JSON configuration: ' . json_last_error_msg());
		}
	}

	protected function findDefaultConfig()
	{
		$possible_locations = [
			get_stylesheet_directory() . '/post-type.config.json',
			get_template_directory() . '/post-type.config.json',
			__DIR__ . '/post-type.config.json',
		];

		foreach ($possible_locations as $location) {
			if (file_exists($location)) {
				return $location;
			}
		}

		return null;
	}

	public function mergeConfig(array $override_config)
	{
		$this->config = array_replace_recursive($this->config, $override_config);
	}

	public function init()
	{
		if (empty($this->config)) {
			//throw new \Exception('No configuration loaded. Cannot initialize.');
		}

		$this->setup_post_types();
		$this->register_taxonomies();
		$this->register_post_meta();
		$this->setup_admin_columns();
		$this->setup_additional_features();
	}


	protected function setup_post_types()
	{
		$post_types = $this->config['post_types'] ?? [];

		foreach ($post_types as $internal_key => $post_type) {
			$name = $post_type['name'] ?? '';
			if (empty($name)) {
				error_log("PostTypeManager: Post type name is required for key '{$internal_key}'.");
				continue;
			}

			if (post_type_exists($name)) {
				$this->modify_existing_post_type($name, $post_type);
			} else {
				$this->register_post_type($internal_key, $post_type);
			}
		}
	}

	protected function register_post_type($internal_key, $post_type)
	{
		$name = $post_type['name'];
		$this->postTypeMap[$internal_key] = $name;

		$formatted_name = str_replace(['-', '_'], ' ', $internal_key);
		$default_args = [
			'slug' => $internal_key,
			'archive' => $internal_key . 's',
			'singular' => $post_type['singular'] ?? ucwords($formatted_name),
			'plural' => $post_type['plural'] ?? ucwords($formatted_name) . 's',
			'args' => []
		];

		$post_type_args = array_merge($default_args, $post_type);

		new PostType(
			name: $name,
			slug: $post_type_args['slug'],
			archive: $post_type_args['archive'],
			singular: $post_type_args['singular'],
			plural: $post_type_args['plural'],
			args: $post_type_args['args']
		);
	}

	protected function modify_existing_post_type($post_type_name, $post_type_config)
	{
		if (!empty($post_type_config)) {
			add_action('init', function () use ($post_type_name, $post_type_config) {
				global $wp_post_types;

				if (isset($wp_post_types[$post_type_name])) {
					$post_type = &$wp_post_types[$post_type_name];

					if (isset($post_type_config['singular'])) {
						$singular = $post_type_config['singular'];
						$post_type->labels->singular_name = $singular;
						$post_type->labels->add_new = "Add New $singular";
						$post_type->labels->add_new_item = "Add New $singular";
						$post_type->labels->edit_item = "Edit $singular";
						$post_type->labels->new_item = "New $singular";
						$post_type->labels->view_item = "View $singular";
						$post_type->labels->view_items = "View $singular";
						$post_type->labels->name_admin_bar = $singular;
					}

					if (isset($post_type_config['plural'])) {
						$plural = $post_type_config['plural'];
						$post_type->labels->name = $plural;
						$post_type->labels->menu_name = $plural;
						$post_type->labels->all_items = "All $plural";
						$post_type->labels->search_items = "Search $plural";
						$post_type->labels->not_found = "No $plural found";
						$post_type->labels->not_found_in_trash = "No $plural found in Trash";
						$post_type->labels->archives = "$plural Archives";
						$post_type->labels->attributes = "$plural Attributes";
					}

					if (isset($post_type_config['args'])) {
						foreach ($post_type_config['args'] as $key => $value) {
							$post_type->$key = $value;
						}
					}
				}
			}, 999);
		}
	}

	protected function register_taxonomies()
	{
		$taxonomies = $this->config['taxonomies'] ?? [];
		foreach ($taxonomies as $internal_key => $taxonomy) {
			$this->register_taxonomy($internal_key, $taxonomy);
		}
	}

	protected function register_taxonomy($internal_key, $taxonomy)
	{
		$name = $taxonomy['name'] ?? '';
		if (empty($name)) {
			error_log("PostTypeManager: Taxonomy name is required for key '{$internal_key}'.");
			return;
		}

		$formatted_name = str_replace(['-', '_'], ' ', $internal_key);
		$default_args = [
			'slug' => $internal_key,
			'singular' => ucwords($formatted_name),
			'plural' => ucwords($formatted_name) . 's',
			'post_types' => [],
			'args' => []
		];

		$taxonomy_args = array_merge($default_args, $taxonomy);

		// Map internal post type keys to their registered names
		$mapped_post_types = array_map(
			fn($pt) => $this->postTypeMap[$pt] ?? $pt,
			$taxonomy_args['post_types']
		);

		new Taxonomy(
			name: $name,
			slug: $taxonomy_args['slug'],
			singular: $taxonomy_args['singular'],
			plural: $taxonomy_args['plural'],
			post_types: $mapped_post_types,
			args: $taxonomy_args['args']
		);
	}

	protected function register_post_meta()
	{
		$post_meta = $this->config['post_meta'] ?? [];
		foreach ($post_meta as $internal_key => $meta_config) {
			$post_type_name = $this->postTypeMap[$internal_key] ?? $internal_key;
			$meta_array = [];

			if (isset($meta_config['meta']) && is_array($meta_config['meta'])) {
				$meta_array[$meta_config['meta']['name']] = $this->prepare_meta_config($meta_config['meta']);
			} elseif (is_array($meta_config)) {
				foreach ($meta_config as $meta_key => $meta_field) {
					if (is_array($meta_field)) {
						$meta_array[$meta_key] = $this->prepare_meta_config($meta_field);
					}
				}
			}

			if (!empty($meta_array)) {
				new PostMeta($post_type_name, $meta_array);
			}
		}
	}

	protected function prepare_meta_config($meta_field)
	{
		return [
			'type' => $meta_field['type'] ?? 'string',
			'description' => $meta_field['description'] ?? '',
			'default' => $meta_field['default'] ?? null,
			'sanitize_callback' => $meta_field['sanitize_callback'] ?? null,
		];
	}

	protected function setup_admin_columns()
	{
		$admin_columns = $this->config['admin_columns'] ?? [];
		foreach ($admin_columns as $internal_key => $columns) {
			$post_type_name = $this->postTypeMap[$internal_key] ?? $internal_key;
			$columns_array = [];
			$show_featured_image = $columns['show_featured_image'] ?? false;

			foreach ($columns['columns'] ?? [] as $column) {
				if (empty($column['name'])) continue;
				$columns_array[$column['name']] = [
					'label' => $column['label'] ?? ucfirst(str_replace('_', ' ', $column['name'])),
					'meta_key' => $internal_key . '_' . $column['name'],
					'width' => $column['width'] ?? null,
				];
			}

			new AdminColumns(
				post_type_name: $post_type_name,
				columns: $columns_array,
				show_featured_image: $show_featured_image
			);
		}
	}

	protected function setup_additional_features()
	{
		$extras = $this->config['extras'] ?? [];
		foreach ($extras as $internal_key => $feature) {
			$post_type_name = $this->postTypeMap[$internal_key] ?? $internal_key;
			if (isset($feature['title_text'])) {
				add_filter("enter_title_here", function ($title, $post) use ($post_type_name, $feature) {
					if ($post->post_type === $post_type_name) {
						return $feature['title_text'];
					}
					return $title;
				}, 10, 2);
			}

			if (isset($feature['pagination'])) {
				add_action('pre_get_posts', function ($query) use ($post_type_name, $feature) {
					if (!is_admin() && $query->is_main_query() && is_post_type_archive($post_type_name)) {
						$query->set('posts_per_page', $feature['pagination']);
					}
				});
			}

			if ($feature['remove_meta_box'] ?? false) {
				add_action('admin_menu', function () use ($post_type_name) {
					remove_meta_box('postcustom', $post_type_name, 'normal');
				});
			}
		}
	}

	public static function setConfig(array $config)
	{
		$instance = self::getInstance();
		$instance->config = $config;
	}

	public function getRegisteredName($internal_key)
	{
		return $this->postTypeMap[$internal_key] ?? $internal_key;
	}
}
