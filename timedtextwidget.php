<?php 
/*
Plugin Name: Timed Textwidget
Description: Display your textwidget on a set time.
Author URI: http://newborndesign.be
Author: Luigi van den Borne
Author URI: http://newborndesign.be
Version: 1.1.0
*/

/*  Copyright 2014  Luigi van den Borne  (email : luigi@newborndesign.be)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined('ABSPATH') or die();

global $TimedTextWidget_version;
$TimedTextWidget_version = "1.1.0";

class TimedTextWidget extends WP_Widget{

/* Setup */
	public function __construct(){	
		
		parent::__construct(
			'timedtextwidget', __('Timed Text','TimedTextWidget'),
			array('description' => __('Display your textwidget on a set time and day.', 'TimedTextWidget'), 'customizer_support' => true)
		);
			
			add_action('init', array($this, 'load_plugin_languages'));
			add_action( 'admin_print_styles', array($this, 'TimedTextWidget_styles') );	
	}

/* Output */
	function widget($args, $instance){
		
		extract($args, EXTR_SKIP);
	
		$title = isset( $instance['title'] ) ? $instance['title']: '';
		$description = isset( $instance['description'] ) ? strip_tags($instance['description'],'<div><span><pre><p><br><hr><hgroup><h1><h2><h3><h4><h5><h6><ul><ol><li><dl><dt><dd><strong><em><b><i><u><img><a><abbr><address><blockquote><area><audio><video><form><fieldset><label><input><textarea><caption><table><tbody><td><tfoot><th><thead><tr><iframe>') : '';
		$start_time = isset( $instance['start_time'] ) ? esc_attr( $instance['start_time'] ) : '';
		$end_time = isset( $instance['end_time'] ) ? esc_attr( $instance['end_time'] ) : '';
		$mon = is_string( $instance[ 'monday' ] ) ? esc_attr( $instance[ 'monday' ] ) : '1';
		$tue = is_string( $instance[ 'tuesday' ] ) ? esc_attr( $instance[ 'tuesday' ] ) : '2';
		$wed = is_string( $instance[ 'wednesday' ] ) ? esc_attr( $instance[ 'wednesday' ] ) : '3';
		$thu = is_string( $instance[ 'thursday' ] ) ? esc_attr( $instance[ 'thursday' ] ) : '4';
		$fri = is_string( $instance[ 'friday' ] ) ? esc_attr( $instance[ 'friday' ] ) : '5';
		$sat = is_string( $instance[ 'saturday' ] ) ? esc_attr( $instance[ 'saturday' ] ) : '6';
		$sun = is_string( $instance[ 'sunday' ] ) ? esc_attr( $instance[ 'sunday' ] ) : '7';

		$dayarray = array( $mon,$tue,$wed,$thu,$fri,$sat,$sun );


		// set WP timezone
		$gmt_offset = get_option( 'gmt_offset' );
		$timezone = date_default_timezone_get();
		date_default_timezone_set('Etc/GMT'.(($gmt_offset < 0)?'+':'').-$gmt_offset);
		$day = date('N');

		$time = date('H:i', time());

		if ( $start_time > $end_time ) { $dif = ('24:00' - $start_time); $tot_sum = ($end_time + $dif); }
		// echo widget
		if(in_array($day, $dayarray)){
			if ( $start_time < $end_time && $time >= $start_time && $time < $end_time || $start_time == $end_time || $start_time > $end_time && $tot_sum > $start_time && $tot_sum > $end_time  ) {
				
				echo $args['before_widget'];
				if ( $title != '' ) { echo $before_title.$title.$after_title; }
				echo $description;
				echo $args['after_widget'];
			}
		}
	}

/*Processing */
	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ): '';
		$instance['description'] = ( !empty( $new_instance['description'] ) ) ? ( $new_instance['description'] ): '';
		$instance['start_time'] = ( !empty( $new_instance['start_time'] ) ) ? sanitize_text_field( $new_instance['start_time'] ): '';
		$instance['end_time'] = ( !empty( $new_instance['end_time'] ) ) ? sanitize_text_field( $new_instance['end_time'] ): '';
		$instance['monday'] = ( !empty( $new_instance['monday'] ) ) ? strip_tags( $new_instance['monday'] ): '';
		$instance['tuesday'] = ( !empty( $new_instance['tuesday'] ) ) ? strip_tags( $new_instance['tuesday'] ): '';
		$instance['wednesday'] = ( !empty( $new_instance['wednesday'] ) ) ? strip_tags( $new_instance['wednesday'] ): '';
		$instance['thursday'] = ( !empty( $new_instance['thursday'] ) ) ? strip_tags( $new_instance['thursday'] ): '';
		$instance['friday'] = ( !empty( $new_instance['friday'] ) ) ? strip_tags( $new_instance['friday'] ): '';
		$instance['saturday'] = ( !empty( $new_instance['saturday'] ) ) ? strip_tags( $new_instance['saturday'] ): '';
		$instance['sunday'] = ( !empty( $new_instance['sunday'] ) ) ? strip_tags( $new_instance['sunday'] ): '';

		return $instance;
	}	

/* Options admin */
	function form($instance){

		if ( isset( $instance[ 'title' ] ) ) { $title = $instance[ 'title' ]; } else { $title = __( '', 'TimedTextWidget' ); }
		if ( isset( $instance[ 'description' ] ) ) { $description = $instance[ 'description' ]; } else { $description = __( '', 'TimedTextWidget' ); }
		if ( isset( $instance[ 'start_time' ] ) ) { $start_time = $instance[ 'start_time' ]; } else { $start_time = __( '00:00', 'TimedTextWidget' ); }
		if ( isset( $instance[ 'end_time' ] ) ) { $end_time = $instance[ 'end_time' ]; } else { $end_time = __( '00:00', 'TimedTextWidget' ); }
		
		if ( isset( $instance[ 'monday' ] ) ) { $mon = $instance[ 'monday' ]; } else { $mon = __( '1', 'TimedTextWidget' ); }
		if ( isset( $instance[ 'tuesday' ] ) ) { $tue = $instance[ 'tuesday' ]; } else { $tue = __( '2', 'TimedTextWidget' ); }
		if ( isset( $instance[ 'wednesday' ] ) ) { $wed = $instance[ 'wednesday' ]; } else { $wed = __( '3', 'TimedTextWidget' ); }
		if ( isset( $instance[ 'thursday' ] ) ) { $thu = $instance[ 'thursday' ]; } else { $thu = __( '4', 'TimedTextWidget' ); }
		if ( isset( $instance[ 'friday' ] ) ) { $fri = $instance[ 'friday' ]; } else { $fri = __( '5', 'TimedTextWidget' ); }
		if ( isset( $instance[ 'saturday' ] ) ) { $sat = $instance[ 'saturday' ]; } else { $sat = __( '6', 'TimedTextWidget' ); }
		if ( isset( $instance[ 'sunday' ] ) ) { $sun = $instance[ 'sunday' ]; } else { $sun = __( '7', 'TimedTextWidget' ); }
		
		?>
		<div class="ttw-form">
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'TimedTextWidget' ); ?></label>
				<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>" class="widefat">
			</p>

			<p>
				<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" style="resize:vertical;overflow:hidden;"><?php echo esc_attr( $description ); ?></textarea>
			</p>
			
			<div class="col-6 first">
			<label for="<?php echo $this->get_field_id( 'start_time' ); ?>"><?php _e( 'Start time:', 'TimedTextWidget' ); ?></label> 
			<input class="widefat timer" style="text-align:center;" id="<?php echo $this->get_field_id( 'start_time' ); ?>" name="<?php echo $this->get_field_name( 'start_time' ); ?>" type="text" value="<?php echo esc_attr( $start_time ); ?>">
			</div>
			
			<div class="col-6">
			<label for="<?php echo $this->get_field_id( 'end_time' ); ?>"><?php _e( 'End time:', 'TimedTextWidget' ); ?></label> 
			<input class="widefat timer" style="text-align:center;" id="<?php echo $this->get_field_id( 'end_time' ); ?>" name="<?php echo $this->get_field_name( 'end_time' ); ?>" type="text" value="<?php echo esc_attr( $end_time ); ?>">
			</div>
			
			<div class="col-4 first">
			<label for="<?php echo $this->get_field_id( 'monday' ); ?>"><input id="<?php echo $this->get_field_id( 'monday' ); ?>" name="<?php echo $this->get_field_name( 'monday' ); ?>" type="checkbox" value="1" <?php if(strlen($mon) || !isset($mon)) echo "checked=checked"; ?>>
			<?php _e( 'Monday', 'TimedTextWidget' ); ?></label>
			</div>
			
			<div class="col-4">
			<label for="<?php echo $this->get_field_id( 'tuesday' ); ?>"><input id="<?php echo $this->get_field_id( 'tuesday' ); ?>" name="<?php echo $this->get_field_name( 'tuesday' ); ?>" type="checkbox" value="2" <?php if(strlen($tue) || !isset($tue)) echo "checked=checked"; ?>>
			<?php _e( 'Tuesday', 'TimedTextWidget' ); ?></label>
			</div>

			<div class="col-4">
			<label for="<?php echo $this->get_field_id( 'wednesday' ); ?>"><input id="<?php echo $this->get_field_id( 'wednesday' ); ?>" name="<?php echo $this->get_field_name( 'wednesday' ); ?>" type="checkbox" value="3" <?php if(strlen($wed) || !isset($wed)) echo "checked=checked"; ?>>
			<?php _e( 'Wednesday', 'TimedTextWidget' ); ?></label>
			</div>

			<div class="col-4 first">
			<input id="<?php echo $this->get_field_id( 'thursday' ); ?>" name="<?php echo $this->get_field_name( 'thursday' ); ?>" type="checkbox" value="4" <?php if(strlen($thu) || !isset($thu)) echo "checked=checked"; ?>>
			<label for="<?php echo $this->get_field_id( 'thursday' ); ?>"><?php _e( 'Thursday', 'TimedTextWidget' ); ?></label>	
			</div>

			<div class="col-4">
			<input id="<?php echo $this->get_field_id( 'friday' ); ?>" name="<?php echo $this->get_field_name( 'friday' ); ?>" type="checkbox" value="5" <?php if(strlen($fri) || !isset($fri)) echo "checked=checked"; ?>>
			<label for="<?php echo $this->get_field_id( 'friday' ); ?>"><?php _e( 'Friday', 'TimedTextWidget' ); ?></label>
			</div>

			<div class="col-4">
			<input id="<?php echo $this->get_field_id( 'saturday' ); ?>" name="<?php echo $this->get_field_name( 'saturday' ); ?>" type="checkbox" value="6" <?php if(strlen($sat) || !isset($sat)) echo "checked=checked"; ?>>
			<label for="<?php echo $this->get_field_id( 'saturday' ); ?>"><?php _e( 'Saturday', 'TimedTextWidget' ); ?></label>
			</div>
			
			<div class="col-4 first">
			<input id="<?php echo $this->get_field_id( 'sunday' ); ?>" name="<?php echo $this->get_field_name( 'sunday' ); ?>" type="checkbox" value="7" <?php if(strlen($sun) || !isset($sun)) echo "checked=checked"; ?>>
			<label for="<?php echo $this->get_field_id( 'sunday' ); ?>"><?php _e( 'Sunday', 'TimedTextWidget' ); ?></label>
			</div>

		</div>
		<?php  
	}

/* Load localization */
    function load_plugin_languages(){
		load_plugin_textdomain( 'TimedTextWidget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
    }

/* Load styles admin */
	function TimedTextWidget_styles() {
		global $TimedTextWidget_version;
	    wp_enqueue_style( 'TimedTextWidget', plugins_url( 'css/ttw-style.css', __FILE__ ), array(), $TimedTextWidget_version );
	}
}

add_action( 'widgets_init', function(){ register_widget( 'TimedTextWidget' ); });

?>