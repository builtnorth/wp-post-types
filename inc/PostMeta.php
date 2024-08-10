<?php

namespace BuiltNorth\PostTypesConstructor;

class PostMeta
{
	protected $post_type_name;
	protected $meta;

	/**
	 * Constructor.
	 *
	 * @param string $post_type_name The name of the post type.
	 * @param array  $meta           Custom meta fields for the post type.
	 */
	public function __construct(
		string $post_type_name,
		array $meta = []
	) {
		$this->post_type_name = sanitize_title_with_dashes($post_type_name);
		$this->meta = $meta;

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
		foreach ($this->meta as $field => $config) {
			$args = [
				'show_in_rest' => true,
				'single' => true,
				'type' => $config['type'],
				'description' => $config['description'],
			];

			if (isset($config['default'])) {
				$args['default'] = $config['default'];
			}

			if (isset($config['sanitize_callback'])) {
				$args['sanitize_callback'] = $config['sanitize_callback'];
			} else {
				$args['sanitize_callback'] = $this->get_default_sanitize_callback($args['type']);
			}

			register_post_meta($this->post_type_name, $field, $args);
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
