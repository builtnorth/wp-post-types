<?php

namespace BuiltNorth\PostTypesConstructor;

class Taxonomy
{
	protected $name;
	protected $slug;
	protected $singular;
	protected $plural;
	protected $post_types;
	protected $args;

	public function __construct(
		string $name,
		string $slug,
		string $singular,
		string $plural,
		array $post_types,
		array $args = []
	) {
		$this->name = sanitize_key($name);
		$this->slug = sanitize_title_with_dashes($slug);
		$this->singular = $singular;
		$this->plural = $plural;
		$this->post_types = $post_types;
		$this->args = $args;

		add_action('init', array($this, 'register_taxonomy'));
	}

	public function register_taxonomy()
	{
		$labels = array(
			'name'                       => _x($this->plural, 'built'),
			'singular_name'              => _x($this->singular, 'built'),
			'search_items'               => __("Search {$this->singular}", 'built'),
			'popular_items'              => __("Popular {$this->plural}", 'built'),
			'all_items'                  => __("All {$this->plural}", 'built'),
			'parent_item'                => __("Parent {$this->singular}", 'built'),
			'parent_item_colon'          => __("Parent {$this->singular}:", 'built'),
			'edit_item'                  => __("Edit {$this->singular}", 'built'),
			'update_item'                => __("Update {$this->singular}", 'built'),
			'add_new_item'               => __("Add New {$this->singular}", 'built'),
			'new_item_name'              => __("New {$this->singular}", 'built'),
			'separate_items_with_commas' => __("Separate " . strtolower($this->plural) . " with commas", 'built'),
			'add_or_remove_items'        => __("Add or remove " . strtolower($this->plural), 'built'),
			'choose_from_most_used'      => __("Choose from the most used " . strtolower($this->plural), 'built'),
			'menu_name'                  => __($this->plural, 'built'),
		);

		$default_args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => $this->slug,
				'with_front' => false,
			),
		);

		$args = array_merge($default_args, $this->args);

		register_taxonomy($this->name, $this->post_types, $args);
	}
}
