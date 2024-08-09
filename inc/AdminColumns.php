<?php

namespace BuiltNorth\PostTypesConstructor;

class AdminColumns
{
	protected $post_type_name;
	protected $columns;
	protected $show_featured_image;

	public function __construct(
		string $post_type_name,
		array $columns = [],
		bool $show_featured_image = false
	) {
		$this->post_type_name = $post_type_name;
		$this->show_featured_image = $show_featured_image;
		$this->columns = $columns;

		/**
		 * Remove all default columns and actions for the post type.
		 * 
		 * @todo Figure out why this is necessary. If missing two images show.
		 */
		remove_all_filters("manage_{$this->post_type_name}_posts_columns");
		remove_all_actions("manage_{$this->post_type_name}_posts_custom_column");

		$this->init();
	}

	protected function init(): void
	{
		add_filter("manage_{$this->post_type_name}_posts_columns", [$this, 'add_custom_columns']);
		add_action("manage_{$this->post_type_name}_posts_custom_column", [$this, 'display_custom_columns'], 10, 2);
		add_action('admin_head', [$this, 'inline_column_styles']);
	}

	public function add_custom_columns($columns)
	{
		$new_columns = [];

		// Add checkbox (for bulk actions)
		if (isset($columns['cb'])) {
			$new_columns['cb'] = $columns['cb'];
		}

		// Add featured image column if enabled
		if ($this->show_featured_image) {
			$new_columns['featured_image'] = __('Image', 'built-starter');
		}

		// Add title column
		if (isset($columns['title'])) {
			$new_columns['title'] = $columns['title'];
		}

		// Add custom columns
		foreach ($this->columns as $key => $column) {
			$new_columns[$key] = $column['label'];
		}

		// Add remaining default columns
		foreach ($columns as $key => $value) {
			if (!isset($new_columns[$key])) {
				$new_columns[$key] = $value;
			}
		}

		return $new_columns;
	}

	public function display_custom_columns($column_name, $post_id)
	{
		if ($column_name === 'featured_image' && $this->show_featured_image) {
			$thumbnail_id = get_post_thumbnail_id($post_id);
			if ($thumbnail_id) {
				$image_attributes = wp_get_attachment_image_src($thumbnail_id, [50, 50]);
				if ($image_attributes) {
					echo '<!-- Start Custom Featured Image -->';
					echo '<img src="' . esc_url($image_attributes[0]) . '" width="50" height="50" alt="" />';
					echo '<!-- End Custom Featured Image -->';
				} else {
					echo '—';
				}
			} else {
				echo '—';
			}
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

	public function inline_column_styles(): void
	{
		echo '<style>';
		if ($this->show_featured_image) {
			echo ".column-featured_image { width: 60px; }";
		}
		foreach ($this->columns as $key => $column) {
			if (isset($column['width'])) {
				echo ".column-{$key} { width: {$column['width']}; }";
			}
		}
		echo '</style>';
	}
}
