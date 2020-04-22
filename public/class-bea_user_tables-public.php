<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/meumairakram
 * @since      1.0.0
 *
 * @package    Bea_user_tables
 * @subpackage Bea_user_tables/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bea_user_tables
 * @subpackage Bea_user_tables/public
 * @author     Umair Akram <contactumairakram@gmail.com>
 */
class Bea_user_tables_Public {

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
	 */
	private $version;

	// Total users after querying.
	private $total_users;

	private $max_records;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bea_user_tables-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'dyna-table-css', plugin_dir_url( __FILE__ ) . 'css/tabulator-bootstrap-4-theme.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bea_user_tables-public.js', array( 'jquery' ), $this->version, false );
		
		wp_localize_script($this->plugin_name,'bea_ajax_obj',$this->local_vars_for_ajax());

		wp_enqueue_script( 'dyna-table-js', plugin_dir_url( __FILE__ ) . 'js/tabulator.min.js', array( 'jquery' ), $this->version, false );

	}

	public function get_table_format($filter = array('all','all'), $order = array('id','asc'), $page = 1) {
		
		// Generating Table HTML Markup
		$output = '<table id="bea_user_table">';
		$output .= '<thead><th data-dynatable-column="ID">';
		$output .= __('ID','bea-user-tables');
		$output .= '</th><th data-dynatable-column="display_name">';
		$output .= __("Display Name","bea-user-tables");
		$output .= '</th><th data-dynatable-column="user_login">';
		$output .= __("Username","bea-user-tables");
		$output .= '</th><th data-dynatable-column="role">';
		$output .= __('Role','bea-user-tables');
		$output .= '</th></thead>';
		$output .= '</table>';

		return $output;
	}

	// Funciton to generate Shortcode
	public function generate_users_table() {
		
		$this->max_records = 10;

		if(current_user_can('edit_users')) {
					
			$output = $this->get_table_format();


			// Print Filters Markup
			echo $this->generate_user_table_filters_html();
			
			// Print Table Markup
			echo $output;


		} else {
			// If user is not an Admin
			$output = __('<h4 class="bea-ut-error">Sorry You must be an Admin to access this Feature. </h4>','bea-user-tables');
			echo $output;
		}	

	}

	// Function to generate Filters Markup
	public function generate_user_table_filters_html() {
		$filters_string = __('Filter By','bea-user-tables');
		$html = <<<EOT
		<div id="bea_ut_filters" class="bea_user_filters">
			<div class="filter-wrap">
				<span>$filters_string: </span>
				<select name="filter_by" id="filter_by">
					<option value="none">No Filter</option>
				</select>
			</div>	
		</div>
EOT;

		return $html;
	}


	// Function to Serve all the Ajax calls made by the Plugin
	public function get_users_via_ajax() {

		// Verify None Key for Validation
		if(!wp_verify_nonce($_REQUEST['wp_nonce'])) {
			die('Unauthorized Access!');
		}

		$query_args = array('orderby' => 'id',
								'order' => 'ASC',
								'number' => 10,
								'paged' => $_REQUEST['page']
							);

		$sorters = (isset($_REQUEST['sorters'])) ? $_REQUEST['sorters'] : NULL;

		if($sorters != NULL && count($sorters)) {
			foreach($sorters as $sort) {
				$query_args['orderby'] = $sort['field'];
			 	$query_args['order'] = $sort['dir'];
			}
		}

		$filters = (isset($_REQUEST['filters'])) ? $_REQUEST['filters'] : NULL;

		if($filters != NULL && count($filters) > 0) {
			$query_args['role'] = $filters[0]['value'];
		}	

		$all_users = new WP_User_Query($query_args);

		$this->total_users = $all_users->total_users;
		$last_page = ceil($all_users->total_users / 10);

		$output_data = array('last_page' => $last_page,'data' => array());

		if(!empty($all_users->get_results())) {
			$i = 0;
			foreach($all_users->get_results() as $user ) {
				
				$output_data['data'][$i]['id'] = $user->data->ID;
				$output_data['data'][$i]['display_name'] = __($user->data->display_name,'bea-user-tables');
				$output_data['data'][$i]['username'] = $user->data->user_login;
				$output_data['data'][$i]['role'] = __(ucfirst($user->roles[0]),'bea-user-tables');



				$i++;
			}
		}

		// sending the response back to the caller plugin
		wp_send_json($output_data);

	}


	// Funciton to print basic script vars for Ajax Calls and Nonce. hooked through localized script
	public function local_vars_for_ajax() {
		$protocol = '';
		if(is_ssl()) {
			$protocol = 'https';
		} else {
			$protocol = 'http';
		}
		$params = array('bea_ajax_url' => admin_url('admin-ajax.php'),
						'bea_ajax_nonce' => wp_create_nonce());

		return $params;
	}

	

}

