<?php
// /*Widget code*/

class Footer_Widget extends WP_Widget {

  /**
   * Sets up the widgets name etc
   */

  public function __construct() {
    // widget actual processes
  		parent::__construct( 'widgetID_1', 'Footer_Widget');
  		}
  }

  /**
   * Outputs the content of the widget
   *
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance ) {
    // outputs the content of the widget
  }

  /**
   * Outputs the options form on admin
   *
   * @param array $instance The widget options
   */
  public function form( $instance ) {
    // outputs the options form on admin
  }

  /**
   * Processing widget options on save
   *
   * @param array $new_instance The new options
   * @param array $old_instance The previous options
   */
  public function update( $new_instance, $old_instance ) {
    // processes widget options to be saved
  }
}

function register_footer_widget() {
	register_widget( 'Footer_Widget' );
}
add_action( 'widgets_init', 'register_footer_widget' );


add_action('widgets_init',
     create_function('', 'return register_widget("Footer_Widget");')
);