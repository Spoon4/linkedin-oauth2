<?php
/**
 * Plugin Name.
 *
 * @package   LinkedIn_OAuth2_Admin
 * @author    Spoon <spoon4@gmail.com>
 * @license   MIT
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-linkedin-oauth2.php`
 *
 * @package LinkedIn_OAuth2_Admin
 * @author  Spoon <spoon4@gmail.com>
 */
class LinkedIn_OAuth2_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = LinkedIn_OAuth2::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		add_shortcode('linkedin', array($this, 'render_shortcode'));
		
		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), LinkedIn_OAuth2::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), LinkedIn_OAuth2::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'LinkedIn Options', $this->plugin_slug ),
			__( 'LinkedIn', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	public function render_shortcode($atts) {
		$return = '';
		
		extract( shortcode_atts( array('id' => 'linkedin-1'), $atts ) );
		
		$token = get_option( 'LINKEDIN_AUTHENTICATION_TOKEN' );
		
		if($token) {
			add_filter('https_ssl_verify', '__return_false');
			$api_url = "https://api.linkedin.com/v1/people/~:(id,first-name,last-name,headline,industry,summary,positions,picture-url,skills,languages,educations,recommendations-received)?oauth2_access_token=$token&format=json";
			
			$response = wp_remote_get( $api_url );
			
			$json = json_decode( $response['body'] );
			
			$return .= '<section class="shortcuts">';
			$return .= '<h2><a href="#shortcuts">Quick links</a></h2>';
			$return .= '<ul>';
			$return .= '<li><a href="#skills">Skills</a></li>';
			$return .= '<li><a href="#summary">Summary</a></li>';
			$return .= '<li><a href="#positions">Positions</a></li>';
			$return .= '<li><a href="#recommendations">Recommendations</a></li>';
			$return .= '</ul>';
			$return .= '</section>';
			
			$return .= '<section class="about">';
			$return .= '<h2>' . $json->{'firstName'} . ' ' . $json->{'lastName'} . '</h2>';
			$return .= '<p>' . $json->{'headline'} . '</p>';
			$return .= '</section>';
			
			$skills = $json->{'skills'}->{'values'};
			$first = true;
			$return .= '<section class="skills">';
			$return .= '<h2><a href="#" name="skills">Skills</a></h2>';
			$return .= '<pre style="font-size: smaller;">';
			foreach($skills as $i => $skill) {
				$return .= ( $first == false ? ', ' : '') . $skill->{'skill'}->{'name'};
				$first = false;
			}
			$return .= '</pre>';
			$return .= '<h2><a href="#" name="summary">Summary</a></h2>';
			$return .= '<pre>' . $json->{'summary'} . '</pre>';
			$return .= '</section>';
			
			$jobs = $json->{'positions'}->{'values'};
			$return .= '<section class="positions">';
			$return .= '<h2><a href="#" name="positions">Positions - ' . $json->{'industry'} . '</a></h2>';
			$return .= '<p>';
			
			foreach($jobs as $i => $job) {
				$return .= '<h2>' . $job->{'title'} . '</h2>';
				$return .= '<h3>' . $job->{'company'}->{'name'};
				$return .= ' ( ' . $job->{'startDate'}->{'year'} . ' - ';
				if($job->{'isCurrent'} == "true"){
					$return .= 'Current';
				} else {
					$return .= $job->{'endDate'}->{'year'};
				}
				$return .= ' )</h3>';
				$return .= '<pre>' . $job->{'summary'} . '</pre>';

			}

			$return .= '</p>';
			$return .= '</section>';
			
			$recommendations = $json->{'recommendationsReceived'}->{'values'};
			$return .= '<section class="recommendations">';
			$return .= '<h2><a href="#" name="recommendations">Recommendations</a></h2>';
			foreach($recommendations as $i => $recommendation) {
				$recommendedBy = $recommendation->{'recommender'};
				$return .= '<h3>' . $recommendedBy->{'firstName'} . ' ' . $recommendedBy->{'lastName'} . '</h3>';
				$return .= '<blockquote>';
				$return .= $recommendation->{'recommendationText'};				
				$return .= '</blockquote>';				
			}
			$return .= '</section>';

			return $return;
		}
	}
	
	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

}
