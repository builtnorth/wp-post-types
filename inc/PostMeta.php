<?php

/**
 * PostMeta Class
 *
 * Handles the configuration of custom post type meta fields.
 *
 * @package BuiltNorth\PostTypesConstructor
 * @since BuiltStarter 2.0.0
 */

namespace BuiltNorth\PostTypesConstructor;

class PostMeta
{
	protected $prefix;
	protected $post_type_name;
	protected $meta;

	/**
	 * Constructor.
	 *
	 * @param string $prefix The prefix for the post type.
	 * @param string $post_type_name   The post_type_name of the post type.
	 * @param array  $meta      Custom meta fields for the post type.
	 */
	public function __construct(
		string $prefix,
		string $post_type_name,
		array $meta = []
	) {
		$this->prefix = sanitize_title_with_dashes($prefix);
		$this->post_type_name   = sanitize_title_with_dashes($post_type_name);
		$this->meta      = $meta;

		$this->init();
	}

	protected function init()
	{
		add_action('init', array($this, 'register_post_meta'));
		add_action('rest_api_init', array($this, 'register_post_meta'));
	}

	/**
	 * Register post meta for the custom post type.
	 */
	public function register_post_meta()
	{
		$full_post_type_post_type_name = $this->prefix . $this->post_type_name;

		foreach ($this->meta as $field => $config) {
			$args = [
				'show_in_rest' => true,
				'single' => true,
				'type' => $config['type'] ?? 'string',
			];

			if (isset($config['sanitize_callback'])) {
				$args['sanitize_callback'] = $config['sanitize_callback'];
			} else {
				$args['sanitize_callback'] = $this->get_default_sanitize_callback($args['type']);
			}

			if (isset($config['default'])) {
				$args['default'] = $config['default'];
			}

			register_post_meta($full_post_type_post_type_name, $full_post_type_post_type_name . '_' . $field, $args);
		}
	}

	/**
	 * Get the default sanitize callback based on the field type.
	 *
	 * @param string $type The type of the field.
	 * @return string|callable The sanitize callback.
	 */
	protected function get_default_sanitize_callback($type)
	{
		switch ($type) {
			case 'boolean':
				return 'rest_sanitize_boolean';
			case 'integer':
				return 'absint';
			case 'number':
				return 'floatval';
			case 'array':
				return 'rest_sanitize_array';
			default:
				return 'sanitize_text_field';
		}
	}
}
