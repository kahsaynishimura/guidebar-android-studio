<?php
/**
 * Class: ECS_Frontend
 *
 * This file initialises the frontend widget area replacement
 * logic for the plugin. The frontend functionality has been 
 * rewritten to actually swap the complete widget areas at 
 * runtime.
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2014, Titanium Themes
 * @version     1.0
 * 
 */
if ( ! class_exists( 'ECS_Frontend' ) ) :
	class ECS_Frontend {
		/**
		 * Instance of this class.
		 * 
		 * @var      object
		 * @since    1.0
		 *
		 */
		protected static $instance = null;

		/**
		 * Slug of the plugin screen.
		 * 
		 * @var      string
		 * @since    1.0
		 *
		 */
		protected $plugin_screen_hook_suffix = 'easy-custom-sidebars';

		/**
		 * Boolean flag to track if widget areas
		 * have already been processed.
		 * 
		 * @var      boolean
		 * @since    1.0
		 *
		 */
		public $widget_areas_processed = false;

		/**
		 * Variable that stores the replacements
		 * for each widget area.
		 * 
		 * @var      array
		 * @since    1.0
		 *
		 */
		public $all_replacements = array();

		/**
		 * Variable that stores the dynamic sidebar
		 * data for the theme default sidebars that
		 * have replacements.
		 * 
		 * @var      array
		 * @since    1.0
		 *
		 */
		public $dynamic_sidebar_data = array();

		/**
		 * Variable that stores the active sidebar
		 * data for the theme default sidebars that
		 * have replacements.
		 * 
		 * @var      array
		 * @since    1.0
		 *
		 */
		public $active_sidebar_data = array();

		/**
		 * Constructor Function
		 * 
		 * Initialize the class and register all
		 * actions and filters.
		 *
		 * @since 1.0
		 * @version 1.0
		 * 
		 */
		function __construct() {

			$this->plugin_slug = 'easy-custom-sidebars';
			$this->register_actions();		
			$this->register_filters();
		}

		/**
		 * Return an instance of this class.
		 * 
		 * @return    object    A single instance of this class.
		 *
		 * @since 1.0
		 * @version 1.0
		 * 
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Register Custom Actions
		 *
		 * Add any custom actions in this function.
		 * 
		 * @link http://codex.wordpress.org/Function_Reference/add_action		add_action()
		 * 
		 * @since 1.0
		 * @version 1.0
		 * 
		 */
		public function register_actions() {
			add_action( 'wp_footer', array( $this, 'prepare_sidebars_for_customizer' ) );
		}
		
		/**
		 * Register Custom Filters
		 *
		 * Add any custom filters in this function.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_filter		add_filter()
		 * 
		 * @since 1.0
		 * @version 1.0
		 * 
		 */
		public function register_filters() {
			add_filter( 'sidebars_widgets', array( $this, 'swap_widgets' ) );
			add_filter( 'dynamic_sidebar_has_widgets', array( $this, 'remove_sidebar_via_dynamic_sidebar' ), 5, 2 );
			add_filter( 'is_active_sidebar', array( $this, 'remove_sidebar_via_is_active_sidebar' ), 5, 2 );
			add_filter( 'dynamic_sidebar_has_widgets', array( $this, 'restore_sidebar_via_dynamic_sidebar' ), 11, 2 );
			add_filter( 'is_active_sidebar', array( $this, 'restore_sidebar_via_is_active_sidebar' ), 11, 2 );
		}

		/**
		 * Remove Default Sidebars via dynamic_sidebar().
		 *
		 * Remove any default theme sidebars from the global
		 * 'wp_registered_sidebars' array when dynamic_sidebar() 
		 * is called in the template. We store the sidebar data
		 * in a class variable so that we can restore it later.
		 * The purpose of this function is to remove the theme
		 * sidebar when the customizer is running.
		 *
		 * @uses global $wp_customize
		 * @uses global $wp_registered_sidebars
		 *
		 * @param 	boolean	$has_widgets - Whether the current sidebar has widgets.
		 * @param 	string 	$sidebar_id  - Sidebar ID.
		 * @return  boolean $has_widgets 
		 *
		 * @since 1.0
		 * @version 1.0
		 * 
		 */
		public function remove_sidebar_via_dynamic_sidebar( $has_widgets, $sidebar_id ) {
			global $wp_customize;

			// Only store data in class if customizer is running
			if ( isset( $wp_customize ) && array_key_exists( $sidebar_id, $this->all_replacements ) ) {
				
				$this->dynamic_sidebar_data[ $sidebar_id ] = array( 
					'id'          => $sidebar_id,
					'has_widgets' => $has_widgets,
					'data'        => $GLOBALS['wp_registered_sidebars'][ $sidebar_id ],
				);

				unset( $GLOBALS['wp_registered_sidebars'][ $sidebar_id ] );
				return false;
			}
			
			return $has_widgets;
		}

		/**
		 * Remove Default Sidebars via is_active_sidebar().
		 *
		 * Remove any default theme sidebars from the global
		 * 'wp_registered_sidebars' array when is_active() 
		 * is called in the template. We store the sidebar data
		 * in a class variable so that we can restore it later.
		 * The purpose of this function is to remove the theme
		 * sidebar when the customizer is running.
		 * 
		 * @uses global $wp_customize
		 * @uses global $wp_registered_sidebars
		 *
		 * @param 	boolean  	$is_active  - Whether the sidebar is active.
		 * @param 	string 		$sidebar_id - Sidebar ID.
		 * @return 	boolean 	$is_active
		 *
		 * @since 1.0
		 * @version 1.0
		 * 
		 */
		public function remove_sidebar_via_is_active_sidebar( $is_active, $sidebar_id ) {
			global $wp_customize;

			// Only store data in class if customizer is running
			if ( isset( $wp_customize ) && array_key_exists( $sidebar_id, $this->all_replacements ) ) {

				$this->active_sidebar_data[ $sidebar_id ] = array( 
					'id'          => $sidebar_id,
					'is_active'   => $is_active,
					'data'        => $GLOBALS['wp_registered_sidebars'][ $sidebar_id ],
				);

				unset( $GLOBALS['wp_registered_sidebars'][ $sidebar_id ] );
				return false;
			}
			
			return $is_active;
		}

		/**
		 * Remove Default Sidebars via dynamic_sidebar().
		 *
		 * Restore any default theme sidebars to the global
		 * 'wp_registered_sidebars' array when dynamic_sidebar() 
		 * is called in the template. The purpose of this function 
		 * is to restore any modified data when the customizer 
		 * widget manager has finished running.
		 *
		 * @uses global $wp_customize
		 * @uses global $wp_registered_sidebars
		 *
		 * @param 	boolean	$has_widgets - Whether the current sidebar has widgets.
		 * @param 	string 	$sidebar_id  - Sidebar ID.
		 * @return  boolean $has_widgets 
		 *
		 * @since 1.0
		 * @version 1.0
		 * 
		 */
		public function restore_sidebar_via_dynamic_sidebar( $has_widgets, $sidebar_id ) {
			global $wp_customize;

			// Attempt to restore if the customizer is running and the variable has been modified
			if ( isset( $wp_customize ) && array_key_exists( $sidebar_id, $this->dynamic_sidebar_data ) ) {
				$GLOBALS['wp_registered_sidebars'][ $sidebar_id ] = $this->dynamic_sidebar_data[ $sidebar_id ]['data'];
				return $this->dynamic_sidebar_data[ $sidebar_id ]['has_widgets'];
			}

			return $has_widgets;
		}

		/**
		 * Restore Default Sidebars via is_active_sidebar().
		 *
		 * Restore any default theme sidebars to the global
		 * 'wp_registered_sidebars' array when is_active() 
		 * is called in the template. The purpose of this function 
		 * is to restore any modified data when the customizer 
		 * widget manager has finished running.
		 *
		 * @uses global $wp_customize
		 * @uses global $wp_registered_sidebars
		 *
		 * @param 	boolean  	$is_active  - Whether the sidebar is active.
		 * @param 	string 		$sidebar_id - Sidebar ID.
		 * @return 	boolean 	$is_active
		 *
		 * @since 1.0
		 * @version 1.0
		 * 
		 */
		public function restore_sidebar_via_is_active_sidebar( $is_active, $sidebar_id ) {
			global $wp_customize;

			// Attempt to restore if the customizer is running and the variable has been modified
			if ( isset( $wp_customize ) && array_key_exists( $sidebar_id, $this->active_sidebar_data ) ) {
				$GLOBALS['wp_registered_sidebars'][ $sidebar_id ] = $this->active_sidebar_data[ $sidebar_id ]['data'];
				return $this->active_sidebar_data[ $sidebar_id ]['is_active'];
			}

			return $is_active;
		}

		/**
		 * Prepare Custom Sidebars for Customizer
		 *
		 * Calls dynamic_sidebar() for each custom sidebar 
		 * in the output buffer so that it can be detected
		 * and shown in the WordPress customizer.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/dynamic_sidebar		dynamic_sidebar()
		 *
		 * @uses global $wp_customize
		 * 
		 * @since 1.0
		 * @version 1.0
		 * 
		 */
		public function prepare_sidebars_for_customizer() {
			global $wp_customize;

			if ( isset( $wp_customize ) && ! empty( $this->all_replacements ) ) {

				// Start the output buffer
				ob_start();

				// Call dynamic sidebar for each custom sidebar
				foreach ( $this->all_replacements as $id => $replacement_id ) {
					dynamic_sidebar( $replacement_id );
				}
								
				// Clean the output buffer 
				ob_get_clean();
			}
		}

		/**
		 * Swap Widgets
		 *
		 * Checks for any custom widget area replacements and
		 * swaps the widgets at runtime. By keeping track of
		 * wether the widget areas have been processed we 
		 * benefit from a performance benefit by ensuring that
		 * this function only runs once.
		 *
		 * @uses global $wp_customize
		 * @uses ECS_Widget_Areas::get_instance()  - defined in includes\frontend\class-ecs-widget-areas.php
		 * 
		 * @param  array $sidebars_widgets - Original Sidebar Widgets
		 * @return array $sidebars_widgets - Updated Sidebar Widgets
		 *
		 * @since 1.0
		 * @version 1.0
		 * 
		 */
		public function swap_widgets( $sidebars_widgets ) {
			global $wp_customize;

			// Return if we are in the admin area and not in the customizer
			if ( is_admin() && ! isset( $wp_customize ) ) {
				return $sidebars_widgets;
			}

			global $post;
			
			$widget_area_data     = ECS_Widget_Areas::get_instance();
			$default_widget_areas = $widget_area_data->get_default_widget_areas();

			/**
			 * Track Replacements:
			 * 
			 * Gets the theme default widget areas and 
			 * checks to see if the user has set any
			 * replacements. Only runs when the $post
			 * object exists and only runs once for
			 * performance.
			 * 
			 */
			if ( ! $this->widget_areas_processed && $post ) {
				foreach ( $default_widget_areas as $widget_area ) {
					$index          = $widget_area['id'];
					$replacement_id = $this->get_widget_area_replacement( $index );

					if ( $replacement_id ) {
						$this->all_replacements[ $index ] = $replacement_id;
					}
				}

				// Set to true as widgets have been processed
				$this->widget_areas_processed = true;
			}

			/**
			 * Swap Widgets:
			 * 
			 * Checks if any replacements exist and then 
			 * swaps out the widgets from the replacement
			 * sidebar into the original sidebar.
			 * 
			 */
			if ( $this->widget_areas_processed && $post ) {
				foreach ( $default_widget_areas as $widget_area ) {
					$index = $widget_area['id'];
					
					if ( array_key_exists( $index, $this->all_replacements ) ) {

						// Detect replacement with no widget
						if ( ! isset( $sidebars_widgets[ $this->all_replacements[ $index ] ] ) ) {
							$sidebars_widgets[ $this->all_replacements[ $index ] ] = array();
						}

						// Swap widget
						$sidebars_widgets[ $index ] = $sidebars_widgets[ $this->all_replacements[ $index ] ];
					}
				}
			}

			return $sidebars_widgets;
		}

		/**
		 * Get the Replacement Custom Sidebar
		 *
		 * First this function determines what kind of page/post etc
		 * that the user is currently on. Once this has been established
		 * this function attempts to find the best sidebar replacement
		 * if it exists.
		 *
		 * Note: If two different sidebars have the same post/taxonomy
		 * assigned to it then the latest sidebar will be applied only
		 * (in alphabetical order). Also this is an expensive function
		 * to run so please only call when needed.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/get_the_ID				get_the_ID()
		 * @link http://codex.wordpress.org/Function_Reference/wp_get_theme 			wp_get_theme()
		 * @link http://codex.wordpress.org/Function_Reference/get_page_templates 		get_page_templates()
		 * @link http://codex.wordpress.org/Function_Reference/WP_Query             	WP_Query()
		 * @link http://codex.wordpress.org/Function_Reference/have_posts             	have_posts()
		 * @link http://codex.wordpress.org/Function_Reference/the_post             	the_post()
		 * @link http://codex.wordpress.org/Function_Reference/get_post_meta          	get_post_meta()
		 * @link http://codex.wordpress.org/Function_Reference/is_404          			is_404()
		 * @link http://codex.wordpress.org/Function_Reference/is_home          		is_home()
		 * @link http://codex.wordpress.org/Function_Reference/is_search          		is_search()
		 * @link http://codex.wordpress.org/Function_Reference/is_author          		is_author()
		 * @link http://codex.wordpress.org/Function_Reference/is_date          		is_date()
		 * @link http://codex.wordpress.org/Function_Reference/is_page          		is_page()
		 * @link http://codex.wordpress.org/Function_Reference/is_single          		is_single()
		 * @link http://codex.wordpress.org/Function_Reference/is_tax          			is_tax()
		 * @link http://codex.wordpress.org/Function_Reference/get_post_type          	get_post_type()
		 * @link http://codex.wordpress.org/Function_Reference/get_queried_object       get_queried_object()
		 * @link http://codex.wordpress.org/Function_Reference/wp_reset_postdata		wp_reset_postdata()
		 * 
		 * @param 	string $index 			- The original widget area/dynamic sidebar index 
		 * @return 	string $replacement_id 	- The replacement id if it exists or false if no replacement is found.
		 *
		 * @since  1.0
		 * @version 1.0
		 * 
		 */
		public function get_widget_area_replacement( $index ) {

			// Get the current post, query and database variables
			global $post;
			global $wp_query;
			global $wpdb;

			/**
			 * 404 Condition
			 * 
			 * The 404 page not found condition needs its own loop
			 * as there is no post object that is currrently active
			 * when a 404 is triggered.
			 * 
			 */
			if ( is_404() ) {
				// Local variables
				$replacement_id     = '';
				$replacement_exists = false;

				// Get all sidebar instances that replace the default sidebar passed in the parameter
				$params = array(
					'post_type'      => 'sidebar_instance',
					'meta_key'       => 'sidebar_replacement_id',
					'meta_value'     => $index,
					'orderby'        => 'title',
					'order'          => 'ASC',
					'posts_per_page' => -1,
				);

				$query = new WP_Query( $params );

				/**
				 * Determine the best type of sidebar to fetch for this page
				 * and attempt to find the best sidebar replacement.
				 * 
				 */ 
				while ( $query->have_posts() ) : $query->the_post();
					$possible_id         = get_post_meta( get_the_ID(), 'sidebar_id', true );
					$sidebar_attachments = get_post_meta( get_the_ID(), 'sidebar_attachments', true );

					foreach ( $sidebar_attachments as $attachment ) {		
						// 404 Condition
						if ( is_404() ) {
							if ( '404' == $attachment['menu-item-object'] ) {
								$replacement_exists = true;
								$replacement_id     = $possible_id;
								continue; // exit the loop
							}
						}
					}	
				endwhile;

				// Reset the post query
				wp_reset_postdata();

				// Return the replacement if it exists
				if ( $replacement_exists ) {
					return $replacement_id;
				} else {
					return false;
				}
			} // end 404 condition

			/**
			 * Not 404 and no post object exists
			 * 
			 * Bail if no post object exists.
			 * 
			 */
			if ( ! $post ) {
				return false;
			}

			/**
			 * Post Object Condition
			 * 
			 * Now that we have determined that a post object exists
			 * we can attempt to find the replacement id for this
			 * widget area.
			 * 
			 */
			$post_id            = get_the_ID();
			$replacement_id     = '';
			$replacement_exists = false;
			
			/**
			 * Used to determine sidebar overrides for multiple 
			 * matched conditions. Initialised using a large int 
			 * value intentionally.
			 * 
			 */
			$sidebar_importance = 9999;

			/**
			 * Get Page Template information and store them as 
			 * local variables to be used later on in this 
			 * function.
			 * 
			 */
			if ( is_page() ) {
				$has_page_template  = false;
				$page_template_name = '';
				$page_templates     = wp_get_theme()->get_page_templates();

				foreach ( $page_templates as $template_filename => $template_name ) {
					if ( is_page_template( $template_filename ) ) {
						$has_page_template  = true;
						$page_template_name = $template_name;
					}
				}
			}

			/**
			 * Get all sidebar instances that replace the default 
			 * widget area index passed in the parameter in name
			 * order.
			 * 
			 */
			$params = array(
				'post_type'      => 'sidebar_instance',
				'meta_key'       => 'sidebar_replacement_id',
				'meta_value'     => $index,
				'orderby'        => 'title',
				'order'          => 'DESC',
				'posts_per_page' => -1,
			);

			$query = new WP_Query( $params );

			/**
			 * Loop through each custom sidebar:
			 * 
			 * Determine the best type of sidebar to fetch for this page
			 * and attempt to find the best sidebar replacement.
			 * 
			 */
			while ( $query->have_posts() ) : $query->the_post();

				// Get id and attachments for this sidebar
				$possible_id         = get_post_meta( get_the_ID(), 'sidebar_id', true );
				$sidebar_attachments = get_post_meta( get_the_ID(), 'sidebar_attachments', true );

				// Attempt to find replacements
				foreach ( $sidebar_attachments as $attachment ) {

					/**
					 * Template Hierachy Conditional Checks
					 * (Not including Page Templates)
					 * 
					 */
					
					// Frontpage Condition
					// -------------------
					if ( is_home() && isset( $attachment['menu-item-object'] ) ) {
						if ( ( 'index_page' == $attachment['menu-item-object'] ) && $sidebar_importance > 10 ) {
							$replacement_exists = true;
							$replacement_id     = $possible_id;
							$sidebar_importance = 10;
							continue; // exit the loop
						}
					}

					// Search Results Condition
					// ------------------------
					if ( is_search() && isset( $attachment['menu-item-object'] ) ) {
						if ( ( 'search_results' == $attachment['menu-item-object'] ) && $sidebar_importance > 10 ) {
							$replacement_exists = true;
							$replacement_id     = $possible_id;
							$sidebar_importance = 10;
							continue; // exit the loop
						}
					}

					// Author Archive Condition
					// ------------------------
					if ( is_author() ) {
						$author = $wp_query->get_queried_object();
						if ( isset( $attachment['menu-item-type'] ) && isset( $attachment['menu-item-object'] ) ) {
							
							if ( ( 'author_archive' == $attachment['menu-item-type'] ) && $sidebar_importance > 10 ) {
								if ( $author->ID == $attachment['menu-item-object'] ) {
									$replacement_exists = true;
									$replacement_id     = $possible_id;
									$sidebar_importance = 10;
									continue; // exit the loop							
								}
							}

							if ( ( 'author_archive_all' == $attachment['menu-item-object'] ) && $sidebar_importance > 20 ) {
								$replacement_exists = true;
								$replacement_id     = $possible_id;
								$sidebar_importance = 20;
								continue; // exit the loop
							}
						}
					}

					// Date Archive Condition
					// ----------------------
					if ( ( is_date() && isset( $attachment['menu-item-object'] ) ) && $sidebar_importance > 10 ) {
						if ( 'date_archive' == $attachment['menu-item-object'] ) {
							$replacement_exists = true;
							$replacement_id     = $possible_id;
							$sidebar_importance = 10;
							continue; // exit the loop
						}
					}

					/**
					 * Page Conditional Checks
					 * (Including Page Templates)
					 * 
					 */
					if ( ! is_home() && is_page() ) {

						// Specific Page Condition
						// -----------------------
						if ( isset( $attachment['menu-item-object-id'] ) ) { 
							if ( ( $post_id == $attachment['menu-item-object-id'] ) && $sidebar_importance > 10 ) {
								$replacement_exists = true;
								$replacement_id     = $possible_id;
								$sidebar_importance = 10;
								continue; // exit the loop							
							}
						}

						// Page Template Condition
						// -----------------------
						if ( 
							isset( $attachment['menu-item-title'] )  && 
							isset( $attachment['menu-item-object'] ) && 
							isset( $attachment['menu-item-type'] ) ) {

							// Get page templates
							if ( $has_page_template && $sidebar_importance > 20 ) {
								if ( 'page-template' == $attachment['menu-item-object'] && 'template_hierarchy' == $attachment['menu-item-type'] ) {
									
									// strpos() is used in order to cater for plugin translations
									$pos = strpos( $attachment['menu-item-title'], $page_template_name );
									
									if ( $pos !== false ) {
										$replacement_exists = true;
										$replacement_id     = $possible_id;
										$sidebar_importance = 20;
										continue; // exit the loop	
									}
								}
							}						
						}

						// All Pages Condition
						// -------------------
						if ( 
							isset( $attachment['menu-item-type'] )   && 
							isset( $attachment['menu-item-object'] ) &&
							$sidebar_importance > 30 ) {

							if ( 'post_type_all' == $attachment['menu-item-type'] && 'page' == $attachment['menu-item-object'] ) {
								$replacement_exists = true;
								$replacement_id     = $possible_id;
								$sidebar_importance = 30;
								continue; // exit the loop								
							}
						}
					} // endif

					/**
					 * Post Type Conditional Checks
					 * (Including All Post Types Condition)
					 * 
					 */
					if ( is_single() ) {

						// Get the current post type
						$current_post_type = get_post_type( $post_id );	

						// Specific Single Post Type Condition
						// -----------------------------------
						if ( 
							isset( $attachment['menu-item-object-id'] ) &&
							isset( $attachment['menu-item-object'] )    &&
							isset( $attachment['menu-item-type'] )      &&
							$sidebar_importance > 10 ) { 

							if (
								$attachment['menu-item-object-id'] == $post_id        &&
								$attachment['menu-item-object'] == $current_post_type &&
								'post_type' == $attachment['menu-item-type'] ) {
									$replacement_exists = true;
									$replacement_id     = $possible_id;
									$sidebar_importance = 10;
									continue; // exit the loop			
							}
						}

						// All Posts in Category Condition
						// -------------------------------
						if ( 
							isset( $attachment['menu-item-object-id'] ) &&
							isset( $attachment['menu-item-object'] )    &&
							isset( $attachment['menu-item-type'] )      &&
							$sidebar_importance > 15 ) {

							if ( 
								'post' == get_post_type( $post_id ) &&
								has_category( $attachment['menu-item-object-id'], $post_id ) &&
								'category_posts' == $attachment['menu-item-type'] ) {
									$replacement_exists = true;
									$replacement_id     = $possible_id;
									$sidebar_importance = 15;
									continue; // exit the loop								
							}
						}					

						// Post Format Condition
						// ---------------------
						if ( get_post_format( $post_id ) ) {
							if ( 
								isset( $attachment['menu-item-type'] )   && 
								isset( $attachment['menu-item-object'] ) &&
								$sidebar_importance > 20 ) {

								if ( 'taxonomy' == $attachment['menu-item-type']      && 
									 'post_format' == $attachment['menu-item-object'] &&
									  get_post_format( $post_id ) == strtolower( $attachment['menu-item-title'] ) ) {
									$replacement_exists = true;
									$replacement_id     = $possible_id;
									$sidebar_importance = 20;
									continue; // exit the loop	
								}
							}
						}

						// All Post Types Condition
						// ------------------------
						if ( 
							isset( $attachment['menu-item-type'] )   && 
							isset( $attachment['menu-item-object'] ) &&
							$sidebar_importance > 30 ) {

							if ( 'post_type_all' == $attachment['menu-item-type'] && $current_post_type == $attachment['menu-item-object'] ) {
								$replacement_exists = true;
								$replacement_id     = $possible_id;
								$sidebar_importance = 30;
								continue; // exit the loop								
							}
						}
					} // endif is_single()

					/**
					 * Taxonomy Conditional Checks
					 * (Including All Taxonomies Condition)
					 * 
					 */
					if ( is_tax() || is_category() || is_tag() ) {

						$queried_obj = get_queried_object();
						$tax_term_id = get_queried_object_id();
						$tax_name    = $queried_obj->taxonomy;

						// Specific Taxonomy Term Condition
						// --------------------------------
						if ( 
							isset( $attachment['menu-item-object-id'] ) &&
							isset( $attachment['menu-item-object'] )    &&
							isset( $attachment['menu-item-type'] )      &&
							$sidebar_importance > 10 ) {

							if (
								'taxonomy' == $attachment['menu-item-type']        &&
								$tax_term_id == $attachment['menu-item-object-id'] &&
								$tax_name == $attachment['menu-item-object'] ) {
								

								$replacement_exists = true;
								$replacement_id     = $possible_id;
								$sidebar_importance = 10;
								continue; // exit the loop	

							}
						}

						// All Taxonomy Terms Condition
						// ----------------------------
						if ( 
							isset( $attachment['menu-item-object'] )    &&
							isset( $attachment['menu-item-type'] )      &&
							$sidebar_importance > 20 ) {

							if (
								'taxonomy_all' == $attachment['menu-item-type']    &&
								$tax_name == $attachment['menu-item-object'] ) {
								
								$replacement_exists = true;
								$replacement_id     = $possible_id;
								$sidebar_importance = 20;
								continue; // exit the loop	
							}
						}
					} // endif is_tax()

					/**
					 * Post Type Archive Condition
					 * (Everything Except Post)
					 * 
					 */	
					if ( is_archive()   && 
						! is_category() && 
						! is_tax()      &&
						! is_tag() ) {
						
						// Get the current post type
						$current_post_type = get_post_type( $post_id );	

						if ( 
							isset( $attachment['menu-item-type'] )   && 
							isset( $attachment['menu-item-object'] ) &&
							$sidebar_importance > 40 ) {

							if ( 'post_type_archive' == $attachment['menu-item-type'] && 
								$current_post_type == $attachment['menu-item-object'] ) {

								$replacement_exists = true;
								$replacement_id     = $possible_id;
								$sidebar_importance = 40;
								continue; // exit the loop								
							}
						}
					}

				} // end foreach loop

			endwhile;

			// Reset the post query
			wp_reset_postdata();

			// Return the replacement if it exists
			if ( $replacement_exists ) {
				return $replacement_id;
			} else {
				return false;
			}
		}
	}
endif;