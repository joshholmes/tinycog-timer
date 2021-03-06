<?php
/**
 * Timer for Elementor WordPress Plugin
 *
 * @package TimerForElementor
 *
 * Plugin Name: TimerForElementor
 * Description: Timer for Elementor. 
 * Plugin URI:  https://github.com/joshholmes/timer-for-elementor
 * Version:     1.0.0
 * Author:      Josh Holmes <josh@joshholmes.com>
 * Author URI:  https://joshholmes.com
 * Text Domain: timer-for-elementor
 */

define( 'TIMER_FOR_ELEMENTOR', __FILE__ );

/**
 * Include the Timer_for_Elementor class.
 */
require plugin_dir_path( TIMER_FOR_ELEMENTOR ) . 'class-timer-for-elementor.php';