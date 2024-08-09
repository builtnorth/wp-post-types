<?php

namespace BuiltNorth\PostTypesConstructor;

class PostType
{
	protected $name;
	protected $slug;
	protected $archive;
	protected $singular;
	protected $plural;
	protected $args;

	public function __construct(
		string $name,
		string $slug,
		string $archive,
		string $singular,
		string $plural,
		array $args = []
	) {
		$this->name = sanitize_key($name);
		$this->slug = sanitize_title_with_dashes($slug);
		$this->archive = sanitize_title_with_dashes($archive);
		$this->singular = $singular;
		$this->plural = $plural;
		$this->args = $args;

		add_action('init', [$this, 'register_post_type']);
	}

	public function register_post_type()
	{
		$labels = array(
			'name'               => _x($this->plural, 'built'),
			'singular_name'      => _x($this->singular, 'built'),
			'add_new'            => __("Add New {$this->singular}", 'built'),
			'add_new_item'       => __("Add New {$this->singular}", 'built'),
			'edit_item'          => __("Edit {$this->singular}", 'built'),
			'new_item'           => __("New {$this->singular}", 'built'),
			'all_items'          => __("All {$this->plural}", 'built'),
			'view_item'          => __("View {$this->singular}", 'built'),
			'search_items'       => __("Search {$this->plural}", 'built'),
			'not_found'          => __("No {$this->plural} found", 'built'),
			'not_found_in_trash' => __("No {$this->plural} found in Trash", 'built'),
			'parent_item_colon'  => '',
			'menu_name'          => __($this->plural, 'built')
		);

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
			'menu_position'       => 21,
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
		);

		$args = array_merge($default_args, $this->args);

		register_post_type($this->name, $args);
	}
}
