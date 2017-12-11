<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://accessidaho.org
 * @since      1.0.0
 *
 * @package    Top_Downloads
 * @subpackage Top_Downloads/public
 */

class Top_Downloads_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The table name we are editing.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $table_name    The plugins table name we are editing.
	 */
	private $table_name;

	/**
	 * This is simply an alias of $wpdb.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $db    So we don't have to keep calling the global.
	 */
	private $db;

	/**
	 * The parameters returned by shortcode arguments.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $params    Stores this class wide for use in functions.
	 */
	private $params;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		global $wpdb;
		$this->db = $wpdb;
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->table_name = $this->db->prefix . 'top_downloads';
	}

	/**
	 * Gets the attachment id from the url.
	 *
	 * @since    1.0.0
	 */
	private function get_attachment_id($url) {
		$attachment = $this->db->get_col($this->db->prepare("SELECT ID FROM {$this->db->posts} WHERE guid=%s", $url));
		return $attachment[0];
	}

	/**
	 * Register the JavaScript for the public area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/top-downloads-public.js', array('jquery'), $this->version, true );

		wp_localize_script($this->plugin_name, 'top_downloads', array(
			'nonce' 	 => wp_create_nonce('download_upcount'),
			'admin_ajax' => admin_url( 'admin-ajax.php')
		));
	}

	public function ajax() {
		check_ajax_referer('download_upcount', 'nonce');
		$attachment = (int)$this->get_attachment_id(esc_url($_POST['attachment']));

		if (!$attachment) {
			wp_send_json_error();
		} else {
			$this->db->insert(
				$this->table_name,
				array('attachment' => $attachment,)
			);
		}
		wp_send_json_success();
	}

	private function map_attachments($attachment) {
		$downloads = 0;
		$period = strtoupper($this->params['period']);
		$length = intval($this->params['length']);
		$downloads = $this->db->get_var($this->db->prepare("SELECT COUNT(*) FROM $this->table_name WHERE attachment=%s AND time BETWEEN NOW() - INTERVAL $length $period AND NOW()", $attachment));
		$post = get_post($attachment);
		$badge = ($this->params['show_count']) ? "<span class='badge'>$downloads</span>" : '';

		return array(
			'attachment_id' => $attachment,
			'downloads'		=> $downloads,
			'html'			=> '<a class="list-group-item" href="' . wp_get_attachment_url( $attachment ) . '">' . $post->post_title . $badge . '</a>'
		);
	}

	private function is_period($period) {
		return in_array( strtolower($period), array('day', 'week', 'month', 'year'));
	}

	public function shortcode($atts) {
		/**
		 * This is a simple function to remove year old downloads counts. May make
		 * an option for this later in the dashboard.
		 */
		$this->db->query("DELETE FROM $this->table_name WHERE time < (NOW() - INTERVAL 1 YEAR)");
		$html = (string)'<ul class="list-group">';

		/**
		 * Fix the $atts variable to use defaults when none are provided.
		 */
		$this->params = (array)shortcode_atts(array(
			'limit' 		=> 5,
			'show_count' 	=> false,
			'period'		=> 'month',
			'length'		=> 1
		), $atts );

		$this->params['period'] = $this->is_period($this->params['period']) ? $this->params['period'] : 'month';

		/**
		 * Find all the attachments and their IDs.
		 */
		$attachments = new WP_Query(array(
			'post_type' 		=> 'attachment',
			'post_status' 		=> 'inherit',
			'fields'			=> 'ids',
			'posts_per_page' 	=> -1,
			// 'cat' => -185
		) );

		$attachments = array_map(array($this, 'map_attachments'), $attachments->posts);

		usort($attachments, function($a, $b) {
    		return $b['downloads'] - $a['downloads'];
		});

		$attachments = array_slice($attachments, 0, (int)$this->params['limit']);
		$html .= implode( wp_list_pluck($attachments, 'html'));
		return $html . '</ul>';
	}
}
