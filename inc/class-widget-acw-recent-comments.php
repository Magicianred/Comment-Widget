<?php

/**
 * Widget_ACW_Recent_Comments Class
 *
 * Adds a Recent Comments widget with extended functionality
 *
 * @package ACW_Recent_Comments
 * @subpackage Widget_ACW_Recent_Comments
 *
 * @since 1.0
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


/**
 * Core class used to implement a Recent Comments widget.
 *
 * @version 1.1.2 Added method to build $defaults
 * @version 1.1.1 Updated "acw_form_field_{$name}" filter
 * @version 1.1 Added support for Selective Refresh
 * @version 1.0 Initial version
 *
 * @since 1.0
 *
 * @see WP_Widget
 */
class Widget_ACW_Recent_Comments extends WP_Widget {


	/**
	 * Sets up a new Comments widget instance.
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public function __construct()
	{
		$widget_options = array(
			'classname' => 'widget_acw_recent_comments',
			'description' => __( 'A comments widget with extended features.' ),
			'customize_selective_refresh' => true,
			);

		$control_options = array();

		parent::__construct(
			'acw-recent-comments',            // $this->id_base
			__( 'Advanced Recent Comments' ), // $this->name
			$widget_options,                  // $this->widget_options
			$control_options                  // $this->control_options
		);

		$this->alt_option_name = 'widget_acw_recent_comments';
	}


	/**
	 * Outputs the content for the current Recent Comments widget instance.
	 *
	 * Use 'widget_title' to filter the widget title.
	 * Use 'acw_widget_comment_query_args' to filter the comment query args.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Comments widget instance.
	 */
	public function widget( $args, $instance )
	{
		if ( ! isset( $args['widget_id'] ) ){
			$args['widget_id'] = $this->id;
		}

		$defaults = ACW_Recent_Comments_Utilities::instance_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );

		// build out the instance for plugin devs
		$instance['id_base'] = $this->id_base;
		$instance['widget_number'] = $this->number;
		$instance['widget_id'] = $this->id;

		// widget title
		$_title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '' ;
		$_title = apply_filters( 'widget_title', $_title, $instance, $this->id_base );

		// number of comments to show
		$_number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5 ;
		if ( ! $_number ) { $_number = 5; };

		// order of display
		$_order = ( ! empty( $instance['order'] ) ) ? $instance['order'] : 'desc' ;

		// post type
		if( 'all' === $instance['post_type'] ) {
			$post_types = ACW_Recent_Comments_Utilities::get_post_types();
			unset( $post_types['all'] );
			$_post_types = array_keys( $post_types );
		} else {
			$_post_types = $instance['post_type'];
		}

		// query
		$query_args = array(
			'number'      => $_number,
			'status'      => 'approve',
			'post_status' => 'publish',
			'post_type'   => $_post_types,
			'order'       => $_order,
		);
		if( ! empty( $instance['exclude_pings'] ) && $instance['exclude_pings'] ) {
			$query_args['type__not_in'] = 'pings';
		}
		$query_args = apply_filters( "acw_widget_comment_query_args", $query_args, $instance );

		$comments = get_comments( $query_args );
		$comments = apply_filters( 'acw_comments', $comments, $instance );
		?>

		<?php echo $args['before_widget']; ?>

		<?php if( $_title ) {
			echo $args['before_title'] . $_title . $args['after_title'];
		}; ?>

		<?php do_action( 'acw_widget_title_after', $instance ); ?>

		<div class="advanced-comments-widget acw-recent-comments acw-comments-wrap">

			<?php do_action( 'acw_comment_list_before', $instance, $comments ); ?>

			<?php
			if ( is_array( $comments ) && $comments ) {

				// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
				$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
				_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

				ob_start();

				ACW_Recent_Comments_Utilities::start_list( $instance, $comments );

				if( 'html5' === $instance['comment_format'] ) {
					ACW_Recent_Comments_Utilities::build_html5_comments( $comments, $instance );
				} else {
					ACW_Recent_Comments_Utilities::build_comments( $comments, $instance );
				}

				ACW_Recent_Comments_Utilities::end_list( $instance, $comments );

				$list = ob_get_clean();

				echo $list;

			}; ?>

			<?php do_action( 'acw_comment_list_after', $instance, $comments ); ?>

		</div><!-- /.acw-comments-wrap -->

		<?php ACW_Recent_Comments_Utilities::colophon(); ?>

		<?php echo $args['after_widget']; ?>

		<?php
	}


	/**
	 * Handles updating settings for the current widget instance.
	 *
	 * Use 'acw_update_instance' to filter updating/sanitizing the widget instance.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance                   = $old_instance;

		$instance['title']          = sanitize_text_field( $new_instance['title'] );
		$instance['post_type']      = sanitize_text_field( $new_instance['post_type'] );
		$instance['exclude_pings']  = isset( $new_instance['exclude_pings'] ) ? 1 : 0 ;
		$instance['number']         = absint( $new_instance['number'] );
		$instance['order']          = sanitize_text_field( $new_instance['order'] );
		$instance['show_thumbs']    = isset( $new_instance['show_thumbs'] ) ? 1 : 0 ;
		$instance['thumb_size']     = absint( $new_instance['thumb_size'] );
		$instance['show_excerpt']   = isset( $new_instance['show_excerpt'] ) ? 1 : 0 ;
		$instance['excerpt_length'] = absint( $new_instance['excerpt_length'] );
		$instance['list_style']     = ( '' !== $new_instance['list_style'] ) ? sanitize_key( $new_instance['list_style'] ) : 'ul ';
		$instance['comment_format'] = ( '' !== $new_instance['comment_format'] ) ? sanitize_key( $new_instance['comment_format'] ) : 'xhtml ';

		$instance = apply_filters('acw_update_instance', $instance, $new_instance, $old_instance );

		return $instance;
	}


	/**
	 * Outputs the settings form for the Recent Comments widget.
	 *
	 * Applies 'acw_form_defaults' filter on form fields to allow extension by plugins.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance )
	{

		$defaults = ACW_Recent_Comments_Utilities::instance_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );

		$_fields   =  array(
			'title'          => ACW_Recent_Comments_Utilities::build_field_title( $instance, $this ),
			'post_type'      => ACW_Recent_Comments_Utilities::build_field_post_type( $instance, $this ),
			'exclude_pings'  => ACW_Recent_Comments_Utilities::build_field_pings( $instance, $this ),
			'number'         => ACW_Recent_Comments_Utilities::build_field_number( $instance, $this ),
			'order'          => ACW_Recent_Comments_Utilities::build_field_order( $instance, $this ),
			'show_thumbs'    => ACW_Recent_Comments_Utilities::build_field_show_thumbs( $instance, $this ),
			'thumb_size'     => ACW_Recent_Comments_Utilities::build_field_thumb_size( $instance, $this ),
			'show_excerpt'   => ACW_Recent_Comments_Utilities::build_field_show_excerpt( $instance, $this ),
			'excerpt_length' => ACW_Recent_Comments_Utilities::build_field_excerpt_length( $instance, $this ),
			'list_style'     => ACW_Recent_Comments_Utilities::build_field_list_style( $instance, $this ),
			'comment_format' => ACW_Recent_Comments_Utilities::build_field_comment_format( $instance, $this ),
		);
		$acw_fields = apply_filters( 'acw_form_fields', $_fields, $instance, $this );

		$acw_field_keys = array_keys( $acw_fields );
		$first_field = reset( $acw_field_keys );
		$last_field  = end( $acw_field_keys );

		foreach ( $acw_fields as $name => $field ) {

			if ( $first_field === $name ) {
				do_action( 'acw_form_before_fields', $instance, $this );
			}

			do_action( "acw_form_before_field_{$name}", $instance, $this );

			echo apply_filters( "acw_form_field_{$name}", $field, $instance, $this ) . "\n";

			do_action( "acw_form_after_field_{$name}", $instance, $this );

			if ( $last_field === $name ) {
				do_action( 'acw_form_after_fields', $instance, $this );
			}

		}
	}



}
