<?php
/**
 * Tinycog Timer WordPress Plugin
 *
 * @package TinycogTimer
 *
 * Plugin Name: TinycogTimer
 * Description: Tinycog Timer. 
 * Plugin URI:  https://github.com/joshholmes/tinycog-timer
 * Version:     0.0.1
 * Author:      Josh Holmes <josh@joshholmes.com>
 * Author URI:  https://joshholmes.com
 * Text Domain: tinycog-timer
 */

define( 'TINYCOG_TIMER', __FILE__ );

/**
 * Include the TINYCOG_TIMER class.
 */
require plugin_dir_path( TINYCOG_TIMER ) . 'class-tinycog-timer.php';