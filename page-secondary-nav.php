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

      // Check if title is set
      if ( $title ) { echo $before_title . $title . $after_title ; }

      $post_id = $GLOBALS[_GET]['page_id'] ; 
      $ancestor = array_pop(get_post_ancestors($post_id)) ; 
      echo "Page ID is $post_id, Ancestor ID is $ancestor" ; 

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
      $pages = get_pages($args); ?>

     <ul class="a"> <?php

     $relevant = false ; 
     $last_parent = -1 ; 
     foreach ( $pages as $page ) { 
        
        echo "\n<p border=>Ancestor / Parent :: $ancestor / " . $page->post_parent . "</p>\n" ;
    
        if ( $page->post_parent == $ancestor ) {
           if ( $relevant ) { ?>
           </ul> <?php
             $relevant = false ;  
           } else { 
             $relevant = true ; 
           }  ?>
           <li><a href="<?php get_permalink($page->ID) ?>"><?php echo $page->post_title; ?></a></li> <?php
        } else if ( $page->post_parent == 0 ) { 
           $relevant = false ; 
        } else if ( $relevant ) { 
          if ( $last_parent != $page->post_parent ) { ?>
           <ul> <?php 
           $last_parent = $page->post_parent ;
          } ?>
              <li><a href="<?php get_permalink($page->ID) ?>"><?php echo $page->post_title; ?></a></li> <?php
        }
     } ?>
     </ul> <?php 
   }
}

//  Whenever widgets are iniatilized, run the specified function 
add_action('widgets_init', function() { register_widget('page_secondary_nav') ; }) ;

?>