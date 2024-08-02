<?php

/**
 * ------------------------------------------------------------------
 * Init
 * ------------------------------------------------------------------
 *
 * Init class to initialize all other classes
 *
 * @package PostTypesConstructor
 * @since PostTypesConstructor 4.3.1
 *
 */

namespace BuiltNorth\PostTypesConstructor;

/**
 * If called directly, abort.
 */
if (!defined('WPINC')) {
	die;
}

class Init
{
	private $post_type;
	private $post_meta;
	private $taxonomy;
	private $admin_columns;

	public function __construct()
	{
		$this->post_type = new PostType();
		$this->post_meta = new PostMeta();
		$this->taxonomy = new Taxonomy();
		$this->admin_columns = new AdminColumns();
	}

	public function init()
	{
		$this->post_type->init();
		$this->post_meta->init();
		$this->taxonomy->init();
		$this->admin_columns > init();
	}

	public static function boot($hook = 'plugins_loaded')
	{
		add_action($hook, function () {
			$instance = new self();
			$instance->init();
		});
	}
}
