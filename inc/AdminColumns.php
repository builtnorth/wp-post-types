<?php

/**
 * Class: AdminColumns
 *
 * Handles the setup of custom admin columns for post types.
 *
 * @package BuiltStarter
 * @since BuiltStarter 2.0.0
 */

namespace BuiltNorth\PostTypesConstructor;

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Class AdminColumns
 * Manages the configuration of custom admin columns for post types.
 */
class AdminColumns
{
	/** @var string The post type prefix. */
	protected $prefix;

	/** @var string The post type name. */
	protected $post_type_name;

	/** @var array The custom columns configuration. */
	protected $columns;

	/**
	 * Constructor.
	 *
	 * @param string $prefix The prefix for the post type.
	 * @param string $post_type_name   The name of the post type.
	 * @param array  $columns          The custom columns configuration.
	 */
	public function __construct(
		string $prefix,
		string $post_type_name,
		array $columns = []
	) {
		$this->prefix = sanitize_key($prefix);
		$this->post_type_name = sanitize_key($post_type_name);
		$this->columns = $this->prepare_columns($columns);
		$this->init();
	}

	/**
	 * Prepare columns by adding the featured image column.
	 *
	 * @param array $columns The user-defined columns.
	 * @return array The prepared columns including the featured image.
	 */
	protected function prepare_columns(array $columns): array
	{
		return array_merge([
			'featured_image' => [
				'label' => __('Image', 'built-starter'),
				'width' => '60px',
			]
		], $columns);
	}

	/**
	 * Initialize the class.
	 * Sets up WordPress hooks and actions.
	 */
	protected function init(): void
	{
		$full_post_type = $this->prefix . $this->post_type_name;
		add_filter("manage_{$full_post_type}_posts_columns", [$this, 'add_custom_columns']);
		add_action("manage_{$full_post_type}_posts_custom_column", [$this, 'display_custom_columns'], 10, 2);
		add_action('admin_head', [$this, 'inline_column_styles']);
	}

	/**
	 * Add new columns.
	 *
	 * @param array $columns The existing columns.
	 * @return array The modified columns.
	 */
	public function add_custom_columns($columns)
	{
		$new_columns = array();

		foreach ($columns as $key => $value) {
			if ($key === 'title') {
				// Add the featured image column just before the title
				$new_columns['featured_image'] = $this->columns['featured_image']['label'];
				$new_columns[$key] = $value;
				foreach ($this->columns as $custom_key => $custom_column) {
					if ($custom_key !== 'featured_image') {
						$new_columns[$custom_key] = $custom_column['label'];
					}
				}
			} elseif ($key !== 'date') {
				$new_columns[$key] = $value;
			}
		}
		$new_columns['date'] = $columns['date'];
		return $new_columns;
	}

	/**
	 * Display the custom columns.
	 *
	 * @param string $column_name The name of the column.
	 * @param int    $post_id     The ID of the current post.
	 */
	public function display_custom_columns($column_name, $post_id)
	{
		if ($column_name === 'featured_image') {
			echo get_the_post_thumbnail($post_id, array(50, 50));
		} elseif (isset($this->columns[$column_name])) {
			$column = $this->columns[$column_name];
			if (isset($column['callback']) && is_callable($column['callback'])) {
				call_user_func($column['callback'], $post_id);
			} elseif (isset($column['meta_key'])) {
				$value = get_post_meta($post_id, $column['meta_key'], true);
				echo esc_html($value);
			}
		}
	}

	/**
	 * Admin styles.
	 */
	public function inline_column_styles(): void
	{
		echo '<style>';
		foreach ($this->columns as $key => $column) {
			if (isset($column['width'])) {
				echo ".column-{$key} { width: {$column['width']}; }";
			}
		}
		echo '</style>';
	}
}
