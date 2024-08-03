<?php

namespace BuiltNorth\PostTypesConstructor;

use BuiltNorth\PostTypesConstructor\PostType;
use BuiltNorth\PostTypesConstructor\Taxonomy;
use BuiltNorth\PostTypesConstructor\PostMeta;
use BuiltNorth\PostTypesConstructor\AdminColumns;

class PostTypeExtended
{
	protected $post_type;
	protected $taxonomy;
	protected $post_meta;
	protected $admin_columns;
	protected $title_text;
	protected $pagination;
	protected $remove_meta_box;

	public function __construct(array $args = [])
	{
		$this->post_type = $args['post_type'] ?? [];
		$this->taxonomy = $args['taxonomy'] ?? [];
		$this->post_meta = $args['post_meta'] ?? [];
		$this->admin_columns = $args['admin_columns'] ?? [];
		$this->title_text = $args['title_text'] ?? null;
		$this->pagination = $args['pagination'] ?? null;
		$this->remove_meta_box = $args['remove_meta_box'] ?? false;

		add_action('init', [$this, 'init'], 2);
	}

	public function init()
	{
		$this->register_post_type();
		$this->register_taxonomy();
		$this->register_post_meta();
		$this->setup_admin_columns();

		if ($this->title_text) {
			add_filter('enter_title_here', [$this, 'change_title_text'], 10, 2);
		}
		if ($this->pagination) {
			add_action('pre_get_posts', [$this, 'pagination_fix']);
		}
		if ($this->remove_meta_box) {
			add_action('admin_menu', [$this, 'remove_legacy_meta_box']);
		}
	}

	public function register_post_type()
	{
		if (empty($this->post_type['name'])) {
			error_log('PostTypeExtended: Post type name is required.');
			return;
		}

		$name = $this->post_type['name'];
		$default_args = [
			'prefix' => '',
			'slug' => $name,
			'archive' => $name . 's',
			'singular' => ucfirst($name),
			'plural' => ucfirst($name) . 's',
			'args' => []
		];

		$post_type_args = array_merge($default_args, $this->post_type);

		new PostType(
			prefix: $post_type_args['prefix'],
			name: $name,
			slug: $post_type_args['slug'],
			archive: $post_type_args['archive'],
			singular: $post_type_args['singular'],
			plural: $post_type_args['plural'],
			args: $post_type_args['args']
		);
	}

	public function register_taxonomy()
	{
		if (empty($this->taxonomy)) return;

		$post_type_name = $this->post_type['name'] ?? '';
		$default_args = [
			'prefix' => $this->post_type['prefix'] ?? '',
			'name' => $post_type_name . '_category',
			'slug' => $post_type_name . '_category',
			'singular' => ucfirst($post_type_name) . ' Category',
			'plural' => ucfirst($post_type_name) . ' Categories',
			'post_type_name' => $post_type_name,
			'args' => []
		];

		$taxonomy_args = array_merge($default_args, $this->taxonomy);

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

	public function register_post_meta()
	{
		if (empty($this->post_meta)) return;

		$meta_array = [];
		foreach ($this->post_meta as $meta) {
			if (empty($meta['name'])) continue;
			$meta_array[$meta['name']] = [
				'type' => $meta['type'] ?? 'string',
				'description' => $meta['description'] ?? '',
				'default' => $meta['default'] ?? '',
			];
		}

		new PostMeta(
			prefix: $this->post_type['prefix'] ?? '',
			post_type_name: $this->post_type['name'] ?? '',
			meta: $meta_array
		);
	}

	public function setup_admin_columns()
	{
		if (empty($this->admin_columns)) return;

		$columns_array = [];
		foreach ($this->admin_columns as $column) {
			if (empty($column['name'])) continue;
			$columns_array[$column['name']] = [
				'label' => $column['label'] ?? ucfirst(str_replace('_', ' ', $column['name'])),
				'meta_key' => ($this->post_type['prefix'] ?? '') . ($this->post_type['name'] ?? '') . '_' . $column['name'],
			];
		}

		new AdminColumns(
			prefix: $this->post_type['prefix'] ?? '',
			post_type_name: $this->post_type['name'] ?? '',
			columns: $columns_array
		);
	}

	public function change_title_text($title, $post)
	{
		if ($post->post_type === ($this->post_type['prefix'] ?? '') . ($this->post_type['name'] ?? '')) {
			return $this->title_text;
		}
		return $title;
	}

	public function pagination_fix($query)
	{
		if (!is_admin() && $query->is_main_query() && is_post_type_archive(($this->post_type['prefix'] ?? '') . ($this->post_type['name'] ?? ''))) {
			$query->set('posts_per_page', $this->pagination);
		}
	}

	public function remove_legacy_meta_box()
	{
		remove_meta_box('postcustom', ($this->post_type['prefix'] ?? '') . ($this->post_type['name'] ?? ''), 'normal');
	}
}
