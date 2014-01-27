<?php
/**
 * LinkedIn OAuth2.
 *
 * @package   LinkedIn_OAuth2
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-linkedin-oauth2-admin.php`
 *
 * @package LinkedIn_OAuth2
 * @author  Spoon <spoon4@gmail.com>
 */
class LinkedIn_OAuth2 {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'linkedin-oauth2';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		
		// PHP session management
		add_action( 'init', array( $this, 'start_linkedin_session'), 1 );
		add_action( 'wp_logout', array( $this, 'destroy_linkedin_session' ) );
		add_action( 'wp_login', array( $this, 'destroy_linkedin_session' ) );
		
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_shortcode('linkedin', array($this, 'render_shortcode'));

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

	/**
	 * Start a new PHP session if doesn't exists.
	 *
	 * @since    1.0.0
	 */
	public function start_linkedin_session() {
		if(!session_id()) {
			session_start();
		}
	}

	/**
	 * Destroy existing PHP session.
	 *
	 * @since    1.0.0
	 */
	public function destroy_linkedin_session() {
		session_destroy();
	}
	
	public function render_shortcode($atts) {
		$return = '';
	
		extract( shortcode_atts( array('id' => 'linkedin-1'), $atts ) );
	
		$token = get_linkedin_token();
	
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
		
			if($skills && !empty($skills)) {
				foreach($skills as $i => $skill) {
					$return .= ( $first == false ? ', ' : '') . $skill->{'skill'}->{'name'};
					$first = false;
				}
			}
			$return .= '</pre>';
			$return .= '<h2><a href="#" name="summary">Summary</a></h2>';
			$return .= '<pre>' . $json->{'summary'} . '</pre>';
			$return .= '</section>';
		
			$jobs = $json->{'positions'}->{'values'};
			$return .= '<section class="positions">';
			$return .= '<h2><a href="#" name="positions">Positions - ' . $json->{'industry'} . '</a></h2>';
			$return .= '<p>';
		
			if($jobs && !empty($jobs)) {
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
			}

			$return .= '</p>';
			$return .= '</section>';
		
			$recommendations = $json->{'recommendationsReceived'}->{'values'};
			$return .= '<section class="recommendations">';
			$return .= '<h2><a href="#" name="recommendations">Recommendations</a></h2>';
		

			if($recommendations && !empty($recommendations)) {
				foreach($recommendations as $i => $recommendation) {
					$recommendedBy = $recommendation->{'recommender'};
					$return .= '<h3>' . $recommendedBy->{'firstName'} . ' ' . $recommendedBy->{'lastName'} . '</h3>';
					$return .= '<blockquote>';
					$return .= $recommendation->{'recommendationText'};				
					$return .= '</blockquote>';
				}			
			}
			$return .= '</section>';

			return $return;
		}
	}

}
