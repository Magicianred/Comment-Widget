<?php

/**
 * ACW_Recent_Comments_Utilities Class
 *
 * All methods are static, this is basically a namespacing class wrapper.
 *
 * @package ACW_Recent_Comments
 * @subpackage ACW_Recent_Comments_Utilities
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
 * ACW_Recent_Comments_Utilities Class
 *
 * Group of utility methods for use by ACW_Recent_Comments
 *
 * @since 1.0
 */
class ACW_Recent_Comments_Utilities
{

	/**
	 * Sets default parameters
	 *
	 * Use 'acw_instance_defaults' filter to modify accepted defaults.
	 *
	 * @uses WordPress current_theme_supports()
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return array $defaults The default values for the widget.
	 */
	public static function instance_defaults()
	{
		$_comment_format = current_theme_supports( 'html5', 'comment-list' ) ? 'html5' : 'xhtml';
		$_list_style = ( 'html5' == $_comment_format ) ? 'div' : 'ul' ;

		$_defaults = array(
			'title'          => __('Recent Comments'),
			'post_type'      => 'post',
			'exclude_pings'  => 1,
			'number'         => 5,
			'order'          => 'desc',
			'show_thumbs'    => 1,
			'thumb_size'     => 55,
			'show_excerpt'   => 1,
			'excerpt_length' => 50,
			'comment_format' => $_comment_format,
			'list_style'     => $_list_style,
		);

		$defaults = apply_filters( 'acw_instance_defaults', $_defaults );

		return $defaults;
	}


	/**
	 * Builds form field: title
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_title( $instance, $widget )
	{
		ob_start();
		?>
		<p>
			<label for="<?php echo $widget->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $widget->get_field_id( 'title' ); ?>" name="<?php echo $widget->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: post_type
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_post_type( $instance, $widget )
	{
		$post_types = self::get_post_types();
		ob_start();
		?>
		<p>
			<label for="<?php echo $widget->get_field_id('post_type'); ?>">
				<?php _e( 'Post Type:' ); ?>
			</label>
			<select name="<?php echo $widget->get_field_name('post_type'); ?>" id="<?php echo $widget->get_field_id('post_type'); ?>" class="widefat">
				<?php foreach( $post_types as $query_var => $label  ) { ?>
					<option value="<?php echo esc_attr( $query_var ); ?>" <?php selected( $instance['post_type'] , $query_var ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: exclude_pings
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_pings( $instance, $widget )
	{
		ob_start();
		?>
		<p>
			<input id="<?php echo $widget->get_field_id( 'exclude_pings' ); ?>" name="<?php echo $widget->get_field_name( 'exclude_pings' ); ?>" type="checkbox" <?php checked( $instance['exclude_pings'], 1 ); ?> />
			<label for="<?php echo $widget->get_field_id( 'exclude_pings' ); ?>">
				<?php _e( 'Exclude pingbacks and trackbacks' ); ?>
			</label>
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: number
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_number( $instance, $widget )
	{
		ob_start();
		?>
		<p>
			<label for="<?php echo $widget->get_field_id( 'number' ); ?>">
				<?php _e( 'Number of comments to show:' ); ?>
			</label>
			<input class="widefat acw-number" id="<?php echo $widget->get_field_id( 'number' ); ?>" name="<?php echo $widget->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo absint( $instance['number'] ); ?>" size="3" />
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}
	
	
	/**
	 * Builds form field: order
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_order( $instance, $widget )
	{
		ob_start();
		?>
		<p>
			<label for="<?php echo $widget->get_field_id('order'); ?>">
				<?php _e( 'Order:' ); ?>
			</label>
			<select name="<?php echo $widget->get_field_name('order'); ?>" id="<?php echo $widget->get_field_id('order'); ?>" class="widefat">
				<option value="desc" <?php selected( $instance['order'] , 'desc' ); ?>><?php _e( 'Newer comments first'); ?></option>
				<option value="asc" <?php selected( $instance['order'] , 'asc' ); ?>><?php _e( 'Older comments first'); ?></option>
			</select>
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}	


	/**
	 * Builds form field: show_thumbs
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_show_thumbs( $instance, $widget )
	{
		ob_start();
		?>
		<h4>Avatars</h4>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $widget->get_field_id( 'show_thumbs' ); ?>" name="<?php echo $widget->get_field_name( 'show_thumbs' ); ?>" <?php checked( $instance['show_thumbs'], 1 ); ?>/>
			<label for="<?php echo $widget->get_field_id( 'show_thumbs' ); ?>">
				<?php _e( 'Display Avatars' ); ?>
			</label>
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: thumb_size
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_thumb_size( $instance, $widget )
	{
		$thumb_size = absint( $instance['thumb_size'] );
		ob_start();
		?>
		<p class="acw-thumb-size-wrap">
			<label for="<?php echo $widget->get_field_id( 'thumb_size' ); ?>">
				<?php _e( 'Avatar/Thumbnail Size' ); ?>
			</label>
			<span class="acw-avatar" style="font-size: <?php echo $thumb_size; ?>px; height:<?php echo $thumb_size; ?>px; width:<?php echo $thumb_size;?>px">
				<i class="acw-icon dashicons dashicons-admin-users"></i>
			</span>
			<input class="widefat acw-thumb-size" id="<?php echo $widget->get_field_id( 'thumb_size' ); ?>" name="<?php echo $widget->get_field_name( 'thumb_size' ); ?>" type="number" value="<?php echo absint( $instance['thumb_size'] ); ?>" />
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: show_excerpt
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_show_excerpt( $instance, $widget )
	{
		ob_start();
		?>
		<h4>Comment Content</h4>
		<p>
			<input id="<?php echo $widget->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $widget->get_field_name( 'show_excerpt' ); ?>" type="checkbox" <?php checked( $instance['show_excerpt'], 1 ); ?> />
			<label for="<?php echo $widget->get_field_id( 'show_excerpt' ); ?>">
				<?php _e( 'Display Comment Excerpt' ); ?>
			</label>
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: excerpt_length
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_excerpt_length( $instance, $widget )
	{
		ob_start();
		?>
		<p>
			<label for="<?php echo $widget->get_field_id( 'excerpt_length' ); ?>">
				<?php _e( 'Excerpt Length' ); ?>
			</label>
			<input class="widefat acw-excerpt-length" id="<?php echo $widget->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $widget->get_field_name( 'excerpt_length' ); ?>" type="number" step="1" min="0" value="<?php echo absint( $instance['excerpt_length'] ); ?>" />
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: list_style
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_list_style( $instance, $widget )
	{
		ob_start();
		?>
		<h4>Comment Format</h4>
		<p>
			<label for="<?php echo $widget->get_field_id('list_style'); ?>">
				<?php _e( 'Comment List Format:' ); ?>
			</label>
			<select name="<?php echo $widget->get_field_name('list_style'); ?>" id="<?php echo $widget->get_field_id('list_style'); ?>" class="widefat">
				<option value="ul" <?php selected( $instance['list_style'] , 'ul' ); ?>><?php _e( 'Unordered List (ul)'); ?></option>
				<option value="ol" <?php selected( $instance['list_style'] , 'ol' ); ?>><?php _e( 'Ordered List (ol)'); ?></option>
				<option value="div" <?php selected( $instance['list_style'] , 'div' ); ?>><?php _e( 'Div (div)'); ?></option>
			</select>
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: comment_format
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_comment_format( $instance, $widget )
	{
		ob_start();
		?>
		<p>
			<?php _e( 'Comment Format:' ); ?><br />
			<label>
				<input class="radio" id="<?php echo $widget->get_field_id( 'comment_format' ); ?>" name="<?php echo $widget->get_field_name( 'comment_format' ); ?>" type="radio" value="html5" <?php checked( $instance['comment_format'], 'html5'); ?>/>
				HTML5 &nbsp;
			</label>
			<label>
				<input class="radio" id="<?php echo $widget->get_field_id( 'comment_format' ); ?>" name="<?php echo $widget->get_field_name( 'comment_format' ); ?>" type="radio" value="xhtml" <?php checked( $instance['comment_format'], 'xhtml'); ?>/>
				XHTML
			</label>
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}

	/**
	 * Retrieves public post types
	 *
	 * Only returns post types that have comments enabled.
	 * Use 'acw_widget_post_type_args' to filter arguments for retrieving post types.
	 * Use 'acw_allowed_post_types' to filter post types that can be selected in the widget.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return array $_ptypes Filtered array of post types.
	 */
	public static function get_post_types()
	{
		$post_type_args = apply_filters( 'acw_widget_post_type_args', array( 'public' => true) );
		$post_types = get_post_types( $post_type_args, 'objects' );

		$_ptypes = array();
		$_ptypes['all'] = __('All');

		foreach( $post_types as $post_type ){
			if ( post_type_supports( $post_type->name, 'comments' ) ) {
				$query_var = ( ! $post_type->query_var ) ? $post_type->name : $post_type->query_var ;
				$label = $post_type->labels->singular_name;
				$_ptypes[ $query_var ] = $label;
			}
		}

		$_ptypes = apply_filters( 'acw_allowed_post_types', $_ptypes );
		$_ptypes = ( ! is_array( $_ptypes ) ) ? (array) $_ptypes : $_ptypes ;

		// Clean the values (since it can be filtered by other plugins)
		$_ptypes = array_map('esc_html', $_ptypes);

		/**
		 * Flip to clean the keys (used as <option> values in <select> field on form)
		 * Note: Keys *should* be post-type names e.g., "post", "page", "event", etc.
		 */
		$_ptypes = array_flip( $_ptypes );
		$_ptypes = array_map('sanitize_key', $_ptypes);

		// Flip back
		$_ptypes = array_flip( $_ptypes );

		asort( $_ptypes );

		return $_ptypes;

	}


	/**
	 * Opens the comment list for the current Recent Comments widget instance.
	 *
	 * Applies 'acw_start_list' filter on $output to allow extension by plugins.
	 * Applies 'acw_comment_list_class' filter on list classes to allow extension by plugins.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Settings for the current Recent Comments widget instance.
	 * @param array $comments Comments to display.
	 *
	 * @return string $output Opening tag element for the comment list.
	 */
	public static function start_list( $instance, $comments )
	{
        switch ( $instance['list_style'] ) {
            case 'div':
                $tag = 'div';
				break;
            case 'ol':
                $tag = 'ol';
                break;
            case 'ul':
            default:
                $tag = 'ul';
                break;
        }

		$classes = array();
		$classes[] = 'acw-comments-list';
		$classes = apply_filters( 'acw_comment_list_class', $classes, $instance, $comments );
		$classes = ( ! is_array( $classes ) ) ? (array) $classes : $classes ;
		$classes = array_map( 'sanitize_html_class', $classes );
		$class_str = implode( ' ', $classes );

		$output = sprintf( '<%1$s class="%2$s">', $tag, $class_str );

		echo apply_filters( 'acw_start_list', $output, $instance, $comments );
	}


	/**
	 * Closes the comment list for the current Recent Comments widget instance.
	 *
	 * Applies 'acw_end_list' filter on $output to allow extension by plugins.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Settings for the current Recent Comments widget instance.
	 * @param array $comments Comments to display.
	 *
	 * @return string $output Closing tag element for the comment list.
	 */
	public static function end_list( $instance, $comments )
	{
        switch ( $instance['list_style'] ) {
            case 'div':
                $output = "</div>\n";
				break;
            case 'ol':
                $output = "</ol>\n";
                break;
            case 'ul':
            default:
                $output = "</ul>\n";
                break;
        }

		echo apply_filters( 'acw_end_list', $output, $instance, $comments );
	}


	/**
	 * Builds HTML5 comment list
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $comments The comments returned from get_comments()
	 * @param array $instance Widget instance
	 *
	 * @return string $_comments HTML for comment list.
	 */
	public static function build_html5_comments( $comments, $instance )
	{
		$_comments = '';

		foreach ( (array) $comments as $comment ) {
			$_comments .= self::html5_comment( $comment, $instance );
		}

		return $_comments;
	}


	/**
	 * Builds XHTML comment list
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $comments The comments returned from get_comments()
	 * @param array $instance Widget instance
	 *
	 * @return string $_comments HTML for comment list.
	 */
	public static function build_comments( $comments, $instance )
	{
		$_comments = '';

		foreach ( (array) $comments as $comment ) {
			$_comments .= self::comment( $comment, $instance );
		}

		return $_comments;
	}


	/**
	 * Outputs a single comment in the HTML5 format.
	 *
	 * @uses ACW_Recent_Comments_Utilities::get_acw_comment_id()
	 * @uses ACW_Recent_Comments_Utilities::get_acw_comment_class()
	 * @uses ACW_Recent_Comments_Utilities::get_acw_comment_content()
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param object $comment WP_Comment Object
	 * @param object $instance Widget instance
	 */
	public static function html5_comment( $comment, $instance )
	{
		$acw_comment_id    = self::get_acw_comment_id( $comment, $instance );
		$acw_comment_class = self::get_acw_comment_class( $comment, $instance );
		$comment_content   = self::get_acw_comment_content( $comment, $instance  );
		$tag = ( 'div' === $instance['list_style'] ) ? 'div' : 'li';
		?>

		<?php do_action( 'acw_comment_before', $comment, $instance ); ?>

		<<?php echo $tag; ?> id="comment-<?php echo sanitize_html_class( $acw_comment_id ); ?>" class="<?php echo $acw_comment_class; ?>" >

			<article id="div-comment-<?php echo sanitize_html_class( $acw_comment_id ); ?>" class="comment-body acw-comment-body">

				<?php do_action( 'acw_comment_top', $comment, $instance ); ?>
				
				<footer class="comment-meta acw-comment-meta">

					<?php if ( $instance['show_thumbs'] ) : ?>
						<span class="comment-avatar acw-comment-avatar">
							<?php echo self::get_comment_author_avatar( $comment, $instance ); ?>
						</span>
					<?php endif; ?>

					<span class="comment-header acw-comment-header">
						<?php
						printf(
							_x( '%1$s <span class="on">on</span> %2$s', 'widgets' ),
							'<span class="comment-author acw-comment-author">' . get_comment_author_link( $comment ) . '</span>',
							'<span class="comment-link acw-comment-link"><a class="comment-link acw-comment-link" href="' . esc_url( get_comment_link( $comment ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a></span>'
						);
						?>
					</span>

					<?php do_action( 'acw_comment_meta', $comment, $instance ); ?>

				</footer>

				<?php if ( $instance['show_excerpt'] ) : ?>
					<div class="comment-content acw-comment-content">
						<?php echo wp_html_excerpt( $comment_content, absint( $instance['excerpt_length'] ), '&hellip;' ); ?>
					</div>
				<?php endif; ?>
				
				<?php do_action( 'acw_comment_bottom', $comment, $instance ); ?>

			</article>

		</<?php echo $tag; ?>>

		<?php do_action( 'acw_comment_after', $comment, $instance ); ?>

		<?php
	}


	/**
	 * Outputs a single comment in the XHTML format.
	 *
	 * @uses ACW_Recent_Comments_Utilities::get_acw_comment_id()
	 * @uses ACW_Recent_Comments_Utilities::get_acw_comment_class()
	 * @uses ACW_Recent_Comments_Utilities::get_acw_comment_content()
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param object $comment WP_Comment Object
	 * @param object $instance Widget instance
	 */
	public static function comment( $comment, $instance )
	{
		$acw_comment_id    = self::get_acw_comment_id( $comment, $instance );
		$acw_comment_class = self::get_acw_comment_class( $comment, $instance );
		$comment_content   = self::get_acw_comment_content( $comment, $instance  );
		$tag = ( 'div' === $instance['list_style'] ) ? 'div' : 'li';
		?>

		<?php do_action( 'acw_comment_before', $comment, $instance ); ?>

		<<?php echo $tag; ?> id="comment-<?php echo sanitize_html_class( $acw_comment_id ); ?>" class="<?php echo $acw_comment_class; ?>" >

			<div id="div-comment-<?php echo sanitize_html_class( $acw_comment_id ); ?>" class="comment-body acw-comment-body">

				<?php do_action( 'acw_comment_top', $comment, $instance ); ?>
				
				<div class="comment-meta acw-comment-meta">

					<?php if ( $instance['show_thumbs'] ) : ?>
						<span class="comment-avatar acw-comment-avatar">
							<?php echo self::get_comment_author_avatar( $comment, $instance ); ?>
						</span>
					<?php endif; ?>

					<span class="comment-header acw-comment-header">
						<?php
						printf(
							_x( '%1$s <span class="on">on</span> %2$s', 'widgets' ),
							'<span class="comment-author acw-comment-author">' . get_comment_author_link( $comment ) . '</span>',
							'<span class="comment-link acw-comment-link"><a class="comment-link acw-comment-link" href="' . esc_url( get_comment_link( $comment ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a></span>'
						);
						?>
					</span>

					<?php do_action( 'acw_comment_meta', $comment, $instance ); ?>

				</div>

				<?php if ( $instance['show_excerpt'] ) : ?>
					<div class="comment-content acw-comment-content">
						<?php echo wp_html_excerpt( $comment_content, absint( $instance['excerpt_length'] ), '&hellip;' ); ?>
					</div>
				<?php endif; ?>
				
				<?php do_action( 'acw_comment_bottom', $comment, $instance ); ?>

			</div>

		</<?php echo $tag; ?>>

		<?php do_action( 'acw_comment_after', $comment, $instance ); ?>

		<?php
	}


	/**
	 * Generate avatar markup
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param object $comment  Comment to display.
	 * @param array $instance Widget instance.
	 *
	 * @return string $avatar_string HTML of commenter avatar.
	 */
	public static function get_comment_author_avatar( $comment, $instance )
	{
		$avatar_string = get_avatar( $comment, $instance['thumb_size'] );
		$comment_author_url = get_comment_author_url( $comment );
		if ( '' !== $comment_author_url ) {
			$avatar_string = sprintf(
				'<a href="%1$s" class="author-link url" rel="external nofollow">%2$s</a>',
				esc_url($comment_author_url),
				$avatar_string
			);
		};
		return $avatar_string;
	}


	/**
	 * Generates unique comment id based on widget instance
	 *
	 * Applies 'acw_comment_id' filter on comment ID to allow extension by plugins.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param object $comment Comment to display.
	 * @param array $instance Widget instance.
	 *
	 * @return string $acw_comment_id Filtered comment ID.
	 */
	public static function get_acw_comment_id( $comment, $instance )
	{
		$acw_comment_id = $instance['widget_id'] . '-comment-' . $comment->comment_ID;

		return apply_filters( 'acw_comment_id', $acw_comment_id, $comment, $instance );
	}


	/**
	 * Generate comment classes
	 *
	 * Applies 'acw_comment_class' filter on comment classes to allow extension by plugins.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param object $comment  Comment to display.
	 * @param array $instance Widget instance.
	 *
	 * @return string $class_str Filtered comment classes.
	 */
	public static function get_acw_comment_class( $comment, $instance )
	{
		$type = ( empty( $comment->comment_type ) ) ? 'comment' : $comment->comment_type;

		$classes = array();
		$classes[] = 'comment';
		$classes[] = 'acw-comment';
		$classes[] = 'type-' . $type;
		$classes[] = 'acw-type-' . $type;
		$classes[] = 'recentcomments';

		if ( $comment->comment_parent > 0 ) {
			$classes[] = 'child-comment';
			$classes[] = 'parent-' . $comment->comment_parent;
		}

		$classes = apply_filters( 'acw_comment_class', $classes, $comment, $instance );
		$classes = ( ! is_array( $classes ) ) ? (array) $classes : $classes ;
		$classes = array_map('sanitize_html_class', $classes);

		$class_str = implode(' ', $classes);

		return $class_str;
	}


	/**
	 * Retrieves comment content
	 *
	 * Applies 'acw_comment_content' filter on comment content to allow extension by plugins.
	 * Applies 'comment_text' filter on comment content to allow extension by plugins.
	 * Note: 'comment_text' is Core WordPress filter
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param object $comment  Comment to display.
	 * @param array $instance Widget instance.
	 *
	 * @return string $comment_content Filtered comment content.
	 */
	public static function get_acw_comment_content( $comment, $instance )
	{
		$comment_content = apply_filters('acw_comment_content', $comment->comment_content, $comment, $instance );

		return apply_filters( 'comment_text', $comment_content, $comment, $instance );
	}


	/**
	 * Outputs plugin attribution
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return string Plugin attribution.
	 */
	public static function colophon( $echo = true )
	{
		$attribution = '<!-- Advanced Comments Widget by darrinb http://darrinb.com/plugins/advanced-comments-widget -->';

		if ( $echo ) {
			echo $attribution;
		} else {
			return $attribution;
		}
	}

}