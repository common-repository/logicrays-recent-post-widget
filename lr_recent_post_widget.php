<?php
/*
Plugin Name: Logicrays Recent Post Widget
description: Recent Post Widget With Two Option Slider and List..
Version: 1.0
Author: Logicrays
Author URI: http://logicrays.com
*/

define("lr-recent-post-widget","lr_recent_post_widget" );

// Add Script and Styles
function lr_enqueue_widget_scripts() {	
	wp_enqueue_style( 'slick-css', plugins_url( '/css/slick.css', __FILE__ ) );
	wp_enqueue_style( 'custom-css', plugins_url( '/css/custom.css', __FILE__ ) );
	wp_enqueue_script( 'slick-js', plugins_url( '/js/slick.js', __FILE__ ) );
}
add_action( 'get_footer','lr_enqueue_widget_scripts');
// Register and load the widget
function lr_load_widget() {
	register_widget( 'lr_recent_post_widget' );
}
add_action( 'widgets_init', 'lr_load_widget' );
// Creating the widget 
class lr_recent_post_widget extends WP_Widget {

function __construct() {
	parent::__construct(
	// Base ID of your widget
	'lr_recent_post_widget', 
	// Widget name will appear in UI
	__('LR Recent Post', 'lr-recent-post-widget'), 
	// Widget description
	array( 'description' => __( 'Sample recent post widget With two option recent post display in slider or recent post display in list with selected option', 'lr-recent-post-widget' ), ) 
	);
}
// Creating widget front-end
public function widget( $args, $instance ) {
		
	$title 			 = apply_filters( 'widget_title', $instance['title'] );		
	$num_post 		 = $instance['num_post'];
	$post_type       = $instance['post_type'];
	$num_posts       = $num_post - 2;
	// before and after widget arguments are defined by themes
	echo $args['before_widget'];
	if ( ! empty( $title ) )
	echo $args['before_title'] . $title . $args['after_title'];	 
	?>
	<script>
	  jQuery(document).ready(function() {
		// slick carousel
		jQuery('.slider').slick({
		  dots: false,
		  vertical: true,
		  slidesToShow: <?php echo $num_posts;
		  ?>,
		  slidesToScroll: <?php echo $num_posts;
		  ?>,
		  autoplay:true,
		  autoplaySpeed: 10000,
		  verticalSwiping: true,
		});
	  });
	</script>
	<?php		
	if($instance['list_type'] == 'slider'){ // slider select
	?>
	<div class="widget-inner video-box viewport clearfix">
	  <div class="widget-posts-lists slider">
		<?php					
	$the_query = new WP_Query( array( 
	'posts_per_page' => $num_post,
	'post_type' => $post_type
	) );
	while ($the_query -> have_posts()) : $the_query -> the_post(); 	
	echo '<div class="post-warpper list ">
	<div class="post-item item">
	<div class="post-thumb">';
	if ( has_post_thumbnail( $_post->ID ) ) {
	echo '<a class="attachment-bd-small size-bd-small wp-post-image" href="' . get_permalink( $_post->ID ) . '" title="' . esc_attr( $_post->post_title ) . '">';
	echo get_the_post_thumbnail( $_post->ID, 'thumbnail' );
	echo '</a>';
	}	
	?>
	  </div>
	  <div class="post-caption">
		<h3 class="post-title"> 
		  <a href="<?php the_permalink() ?>">
			<?php the_title(); ?>
		  </a> 
		</h3>
		<div class="post-meta"> 
		  <span class="date updated bdayh-date">
			<?php echo get_the_date(); ?>
		  </span> 
		</div>
	  </div>
	</div>
	</div>
	<?php 
	endwhile;
	wp_reset_postdata();
	?>
	</div>
	</div>
	<?php
	}
	else{ // List select
	?>
	<div class="widget-inner video-box clearfix">
	  <div class="widget-posts-lists">
		<?php					
	$args = array( 'post_type' => $post_type, 'posts_per_page' => $num_post);
	$the_query = new WP_Query( $args );					
	while ($the_query -> have_posts()) : $the_query -> the_post(); 			
	echo '<div class="post-warpper">
	<div class="post-item">
	<div class="post-thumb">';
	if ( has_post_thumbnail( $_post->ID ) ) {
	echo '<a class="attachment-bd-small size-bd-small wp-post-image" href="' . get_permalink( $_post->ID ) . '" title="' . esc_attr( $_post->post_title ) . '">';
	echo get_the_post_thumbnail( $_post->ID, 'thumbnail' );
	echo '</a>';
	}
	?>
	  </div>
	  <div class="post-caption">
		<h3 class="post-title"> 
		  <a href="<?php the_permalink() ?>">
			<?php the_title(); ?>
		  </a> 
		</h3>
		<div class="post-meta"> 
		  <span class="date updated bdayh-date">
			<?php echo get_the_date(); ?>
		  </span> 
		</div>
	  </div>
	</div>
	</div>
	<?php 
	endwhile;
	wp_reset_postdata();
	?>
	</div>
	</div>
	<?php			
	}
	?>
	<?php
	echo $args['after_widget'];
}			 
// Widget Backend 
public function form( $instance ) {
	$instance 		 = wp_parse_args( (array) $instance, $this->w_arg );			
	$num_post 		 = $instance['num_post'];
	$post_type       = $instance['post_type'];
	if ( isset( $instance[ 'title' ] ) ) {
	$title = $instance[ 'title' ];
	}
	else {
	$title = __( 'Recent Post', 'lr-recent-post-widget' );
	}
	// Widget admin form
	?>
	<!-- Title -->
	<p>
	  <label for="<?php echo $this->get_field_id( 'title' ); ?>">
		<?php _e( 'Title:' ); ?>
	  </label>
	  <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<!-- Post Type-->
	<p>
	  <label for="<?php echo $this->get_field_id( 'post_type' ); ?>">
		<?php esc_html_e('Select Post:', 'widget-post-post'); ?>
	  </label>
	  <?php
	$args = array(
	'public'   => true,
	'_builtin' => false,				
	);
	$output = 'names'; // 'names' or 'objects' (default: 'names')
	$operator = 'and'; // 'and' or 'or' (default: 'and')		
	$post_types = get_post_types( $args, $output, $operator );
	if ( $post_types ) {
	?>
	  <select class="widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
		<option value='post' 
		<?php if ('post' == $instance['post_type']) echo 'selected="selected"'; ?>>
		<?php esc_html_e('post', 'widget-post-post') ?>
		</option>
	  <?php foreach ( $post_types  as $post_type ) {?>
	  <option value='<?php echo $post_type; ?>'
	  <?php if ($post_type == $instance['post_type']) echo 'selected="selected"'; ?>> 
	  <?php echo $post_type; ?> 
	</option>
	<?php } ?>
	</select>
	<?php } ?>
	</p>
	<!-- Select Type -->
	<p>
	  <label for="<?php echo $this->get_field_id( 'type_title' ); ?>">
		<?php _e( 'Select Type:' ); ?>
	  </label>
	  <select id="<?php echo $this->get_field_id('list_type'); ?>" name="<?php echo $this->get_field_name('list_type'); ?>" class="widefat" style="width:100%;">
		<option <?php selected( $instance['list_type'], 'slider'); ?> value="slider">Slider</option>
	    <option <?php selected( $instance['list_type'], 'list'); ?> value="list">List</option>
	  </select>
	</p>
	<!-- Number of Post -->
	<p>
	  <label for="<?php echo $this->get_field_id( 'num_post' ); ?>">
		<?php _e( 'Number of Post to show: :' ); ?>
	  </label>
	  <input class="tiny-text" id="<?php echo $this->get_field_id( 'num_post' ); ?>" name="<?php echo $this->get_field_name( 'num_post' ); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $num_post ); ?>" size="" />
	  <?php 
}		 
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title']		 = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	$instance['post_type'] 	 = ( ! empty( $new_instance['post_type'] ) ) ? strip_tags( $new_instance['post_type'] ) : '';		
	$instance['list_type'] 	 = ( ! empty( $new_instance['list_type'] ) ) ? strip_tags( $new_instance['list_type'] ) : '';
	$instance['num_post']    = ( ! empty( $new_instance['num_post'] ) ) ? strip_tags( $new_instance['num_post'] ) : '';
	return $instance;
}
} 
// Class lr_recent_post_widget ends here
?>