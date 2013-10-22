<?php
/*
Plugin Name: Page Secondary Nav
Plugin URI: https://github.com/flaminghakama/wordpress-page-secondary-nav
Description: A widget that provides secondary navigation
Version: 0.1
Author: David Elaine Alt
Author URI: http://flaminghakama.com
License: GPL2
*/

require_once('page-nav-objects.php') ;  

class page_secondary_nav extends WP_Widget {

   //  fires off when an instance is created
   function __construct() {
      parent::__construct(false, $name = __('Page Secondary Nav', 'page_secondary_nav_plugin')) ; 
      //"Provide list of sibling links."
   }

   //  displayed in the dashboard when you configure the widget
   function form($instance) {

      // Check values
      if( $instance) {
         $title = esc_attr($instance['title']);
      } else {
         $title = '';
      } ?>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Page Secondary Nav', 'page_secondary_nav_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p> <?php
   }

   //  called when you update the widget configuration 
   // update widget
   function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
     return $instance;
   }

   //  called to display widget
   function widget($args, $instance) { 

      extract( $args );

      // these are the widget options
      $title = apply_filters('widget_title', $instance['title']);

      echo $before_widget;
      // Display the widget
      echo '<div class="widget-text wp_widget_plugin_box">';

      $post_id = $GLOBALS[_GET]['page_id'] ; 
      $ancestor = array_pop(get_post_ancestors($post_id)) ; 
      //echo "Page ID is $post_id, Ancestor ID is $ancestor" ; 

      $query_args = array(
	'sort_order' => 'ASC',
	'sort_column' => 'menu_order',
	'hierarchical' => 0,
	'child_of' => $post_id,
	'parent' => -1,
	'exclude_tree' => '',
	'offset' => 0,
	'post_type' => 'page',
	'post_status' => 'publish'
      ); 
      $pages = get_pages($args); 

      $section_title = get_the_title($ancestor) ; 
      echo $before_title . $section_title . $after_title ;

      $parentNav = NULL ; 
 
      foreach ( $pages as $page ) { 
	  //echo "\n<p border=>Anc. / Parent / ID :: $ancestor / " . $page->post_parent . " / " . $page->ID . "</p>\n" ;
	  if ( $parentNav == NULL ) { $parentNav = new Page_Nav($ancestor) ; }
	  $parentNav->sort_page($page) ;  
      }
      
      echo $parentNav->format() ; 

      echo $after_widget;
   }
}

//  Whenever widgets are iniatilized, run the specified function 
add_action('widgets_init', create_function( '', 'register_widget("page_secondary_nav");')) ; 
?>
