<?php

/**
 * ------------------------------------------------------------------
 * Class: Core / RegisterSettings
 * ------------------------------------------------------------------
 *
 * Registers any needed admin menus
 * 
 * @link https://github.com/dreamhigh0525/CPT-Class
 *
 * @package BuiltStarter
 * @since BuiltStarter 2.0.0
 */

namespace BuiltNorth\PostTypesConstructor;

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
	die;
}


/**
 * Class PostType
 * Handles the registration of custom post types.
 */
class PostType
{
	/**
	 * @var string The prefix for the post type.
	 */
	protected $prefix;

	/**
	 * @var string The name of the post type.
	 */
	protected $name;

	/**
	 * @var string The slug of the post type.
	 */
	protected $slug;

	/**
	 * @var string The archive slug of the post type.
	 */
	protected $archive;

	/**
	 * @var string The singular name of the post type.
	 */
	protected $singular;

	/**
	 * @var string The plural name of the post type.
	 */
	protected $plural;

	/**
	 * @var array Custom arguments for the post type.
	 */
	protected $args;

	/**
	 * Constructor.
	 *
	 * @param string $prefix   The prefix for the post type.
	 * @param string $name     The name of the post type.
	 * @param string $slug     The slug for the post type.
	 * @param string $archive  The archive slug for the post type.
	 * @param string $singular The singular name of the post type.
	 * @param string $plural   The plural name of the post type.
	 * @param array  $args     Custom arguments for the post type.
	 */
	public function __construct(
		string $prefix,
		string $name,
		string $slug,
		string $archive,
		string $singular,
		string $plural,
		array $args = []
	) {
		$this->prefix   = sanitize_title_with_dashes($prefix);
		$this->name     = sanitize_title_with_dashes($name);
		$this->slug     = sanitize_title_with_dashes($slug);
		$this->archive  = sanitize_title_with_dashes($archive);
		$this->singular = $singular;
		$this->plural   = $plural;
		$this->args     = $args;

		add_action('init', [$this, 'register_post_type']);
	}

	/**
	 * Register the custom post type.
	 */
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
				'editor', 'excerpt', 'page-attributes', 'title', 'thumbnail',
			),
		);

		$args = array_merge($default_args, $this->args);

		register_post_type("{$this->prefix}{$this->name}", $args);
	}

	/**
	 * Throw error on object clone.
	 *
	 * @return void
	 */
	public function __clone()
	{
		_doing_it_wrong(__FUNCTION__, esc_html__('Cloning is forbidden.', 'built'), '2.0.0');
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @return void
	 */
	public function __wakeup()
	{
		_doing_it_wrong(__FUNCTION__, esc_html__('Unserializing instances of this class is forbidden.', 'built'), '2.0.0');
	}
}
