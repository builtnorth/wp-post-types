<?php

namespace BuiltNorth\PostTypesConstructor;

use BuiltNorth\PostTypesConstructor\PostType;
use BuiltNorth\PostTypesConstructor\Taxonomy;
use BuiltNorth\PostTypesConstructor\PostMeta;
use BuiltNorth\PostTypesConstructor\AdminColumns;

class PostTypeExtended
{
	protected $config;

	public function __construct(array $args = [])
	{
		$this->config = $args;
		add_action('init', [$this, 'init'], 2);
	}

	public function init()
	{
		$this->setup_post_type();
		$this->register_taxonomy();
		$this->register_post_meta();
		$this->setup_admin_columns();
		$this->setup_additional_features();
	}

	protected function setup_post_type()
	{
		$post_type = $this->config['post_type'] ?? [];
		$name = $post_type['name'] ?? '';

		if (empty($name)) {
			error_log('PostTypeExtended: Post type name is required.');
			return;
		}

		if (post_type_exists($name)) {
			$this->modify_existing_post_type();
		} else {
			$this->register_new_post_type($post_type);
		}
	}

	protected function register_new_post_type($post_type)
	{
		$formatted_name = str_replace(['-', '_'], ' ', $post_type['name']);

		$default_args = [
			'prefix' => '',
			'slug' => $post_type['name'],
			'archive' => $post_type['name'] . 's',
			'singular' => ucwords($formatted_name),
			'plural' => ucwords($formatted_name) . 's',
			'args' => []
		];

		$post_type_args = array_merge($default_args, $post_type);

		new PostType(
			prefix: $post_type_args['prefix'],
			name: $post_type_args['name'],
			slug: $post_type_args['slug'],
			archive: $post_type_args['archive'],
			singular: $post_type_args['singular'],
			plural: $post_type_args['plural'],
			args: $post_type_args['args']
		);
	}

	protected function modify_existing_post_type()
	{
		$post_type_name = $this->config['post_type']['name'];
		$new_args = $this->config['post_type']['args'] ?? [];

		if (!empty($new_args)) {
			add_action('init', function () use ($post_type_name, $new_args) {
				global $wp_post_types;
				if (isset($wp_post_types[$post_type_name])) {
					$args = &$wp_post_types[$post_type_name];
					foreach ($new_args as $key => $value) {
						$args->$key = $value;
					}
				}
			}, 99);  // High priority to run after the post type is registered
		}
	}

	protected function register_taxonomy()
	{
		$taxonomies = $this->config['taxonomies'] ?? [];
		$post_type_name = $this->config['post_type']['name'] ?? '';

		foreach ($taxonomies as $taxonomy) {
			$name = $taxonomy['name'] ?? '';
			if (empty($name)) continue;

			$formatted_name = str_replace(['-', '_'], ' ', $name);

			$default_args = [
				'prefix' => $this->config['post_type']['prefix'] ?? '',
				'name' => $name,
				'slug' => $name,
				'singular' => ucwords($formatted_name),
				'plural' => ucwords($formatted_name) . 's',
				'post_type_name' => $post_type_name,
				'args' => []
			];

			$taxonomy_args = array_merge($default_args, $taxonomy);

			new Taxonomy(
				prefix: $taxonomy_args['prefix'],
				name: $taxonomy_args['name'],
				slug: $taxonomy_args['slug'],
				singular: $taxonomy_args['singular'],
				plural: $taxonomy_args['plural'],
				post_type_name: $taxonomy_args['post_type_name'],
				args: $taxonomy_args['args']
			);
		}
	}

	protected function register_post_meta()
	{
		$post_meta = $this->config['post_meta'] ?? [];
		if (empty($post_meta)) return;

		$meta_array = [];
		foreach ($post_meta as $meta) {
			if (empty($meta['name'])) continue;
			$meta_array[$meta['name']] = [
				'type' => $meta['type'] ?? 'string',
				'description' => $meta['description'] ?? '',
				'default' => $meta['default'] ?? '',
			];
		}

		new PostMeta(
			prefix: $this->config['post_type']['prefix'] ?? '',
			post_type_name: $this->config['post_type']['name'] ?? '',
			meta: $meta_array
		);
	}

	protected function setup_admin_columns()
	{
		$admin_columns = $this->config['admin_columns'] ?? [];
		$show_featured_image = $this->config['post_type']['show_featured_image'] ?? false;

		$columns_array = [];

		// Only add featured image column if explicitly set to true
		if ($show_featured_image === true) {
			$columns_array['featured_image'] = [
				'label' => 'Image',
				'callback' => [$this, 'display_featured_image'],
				'width' => '60px',
			];
		}

		foreach ($admin_columns as $column) {
			if (empty($column['name'])) continue;
			$columns_array[$column['name']] = [
				'label' => $column['label'] ?? ucfirst(str_replace('_', ' ', $column['name'])),
				'meta_key' => ($this->config['post_type']['prefix'] ?? '') . ($this->config['post_type']['name'] ?? '') . '_' . $column['name'],
				'width' => $column['width'] ?? null,
			];
		}

		// Only create AdminColumns instance if there are columns to add
		if (!empty($columns_array)) {
			new AdminColumns(
				prefix: $this->config['post_type']['prefix'] ?? '',
				post_type_name: $this->config['post_type']['name'] ?? '',
				columns: $columns_array,
				show_featured_image: $this->config['post_type']['show_featured_image'] ?? false
			);
		}
	}

	public function display_featured_image($post_id)
	{
		echo get_the_post_thumbnail($post_id, [50, 50]);
	}

	protected function setup_additional_features()
	{
		if (isset($this->config['title_text'])) {
			add_filter('enter_title_here', [$this, 'change_title_text'], 10, 2);
		}

		if (isset($this->config['pagination'])) {
			add_action('pre_get_posts', [$this, 'pagination_fix']);
		}

		if ($this->config['remove_meta_box'] ?? false) {
			add_action('admin_menu', [$this, 'remove_legacy_meta_box']);
		}
	}

	public function change_title_text($title, $post)
	{
		if ($post->post_type === ($this->config['post_type']['prefix'] ?? '') . ($this->config['post_type']['name'] ?? '')) {
			return $this->config['title_text'];
		}
		return $title;
	}

	public function pagination_fix($query)
	{
		if (!is_admin() && $query->is_main_query() && is_post_type_archive(($this->config['post_type']['prefix'] ?? '') . ($this->config['post_type']['name'] ?? ''))) {
			$query->set('posts_per_page', $this->config['pagination']);
		}
	}

	public function remove_legacy_meta_box()
	{
		remove_meta_box('postcustom', ($this->config['post_type']['prefix'] ?? '') . ($this->config['post_type']['name'] ?? ''), 'normal');
	}
}
