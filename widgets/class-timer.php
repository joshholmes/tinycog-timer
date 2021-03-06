<?php
/**
 * TimerForElementor class.
 *
 * @category   Class
 * @package    Timer For Elementor
 * @subpackage WordPress
 * @author     Josh Holmes <josh@joshholmes.com>
 * @copyright  2021 Josh Holmes
 * @license    https://opensource.org/licenses/GPL-3.0 GPL-3.0-only
 * @link       link(https://github.com/joshholmes/timer-for-elementor,
 *             Timer for Elementor)
 * @since      1.0.0
 * php version 7.3.9
 */

namespace TimerForElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

// Security Note: Blocks direct access to the plugin PHP files.
defined( 'ABSPATH' ) || die();

/**
 * Timer For Elementor widget class.
 *
 * @since 1.0.0
 */
class Timer extends Widget_Base {
	/**
	 * Class constructor.
	 *
	 * @param array $data Widget data.
	 * @param array $args Widget arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'timer', plugins_url( '/assets/css/timer.css', TIMER_FOR_ELEMENTOR ), array(), '1.0.0' );
		wp_register_script( 'timer-script', plugin_dir_url( __FILE__ ) . 'assets/js/file.js', array( 'jquery' ), '1.0.0', true );
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'timer';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Timer for Elementor', 'elementor-timer' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-pencil';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'general' );
	}
	
	/**
	 * Enqueue styles.
	 */
	public function get_style_depends() {
		return array( 'timer' );
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'elementor-timer' ),
			)
		);

		$this->add_control(
			'preCountDownSeconds',
			array(
				'label'   => __( 'Pre Count down Seconds', 'elementor-timer' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => __( '10', 'elementor-timer' ),
			)
		);

		$this->add_control(
			'countDownLength',
			array(
				'label'   => __( 'Count down in Seconds', 'elementor-timer' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => __( '180', 'elementor-timer' ),
			)
		);

		$this->add_control(
			'startButtonText',
			array(
				'label'   => __( 'startButtonText', 'elementor-timer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'startButtonText', 'elementor-timer' ),
			)
		);

		$this->add_control(
			'endButtonText',
			array(
				'label'   => __( 'endButtonText', 'elementor-timer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'endButtonText', 'elementor-timer' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'countDownLength', 'basic' );
		$this->add_inline_editing_attributes( 'startButtonText', 'basic' );
		$this->add_inline_editing_attributes( 'endButtonText', 'basic' );
		?>

		<div class="timer">
			<input id="sw-preCountSeconds" type="hidden" value="<?php $settings['preCountDownSeconds']; ?>" />
			<input id="sw-totalSeconds" type="hidden" value="<?php $settings['countDownLength']; ?>" />
			<div id="sw-time" <?php echo $this->get_render_attribute_string( 'countDownLength' ); ?>></div>
			<div id="sw-go" onclick="startTimer()" class="timer-button" <?php echo $this->get_render_attribute_string( 'startButtonText' ); ?>><?php echo wp_kses( $settings['startButtonText'], array() ); ?></div>
			<div id="sw-rst" onclick="resetTimer()" class="timer-button" <?php echo $this->get_render_attribute_string( 'endButtonText' ); ?>><?php echo wp_kses( $settings['endButtonText'], array() ); ?></div>
		</div>
		<script type="text/javascript">
				var totalSeconds = jQuery('#sw-totalSeconds').val();
				var preCountSeconds = jQuery('#sw-preCountSeconds').val();

				var timer_hours   = Math.floor(totalSeconds/ 3600);
				var timer_minutes = Math.floor((totalSeconds - (timer_hours * 3600)) / 60);
				var timer_seconds = totalSeconds - (timer_hours * 3600) - (timer_minutes * 60);

				function timer_convert2HHMMSS(seconds) {
					var hours = timer_hours;
					var minutes = timer_minutes;
					var seconds = timer_seconds;
					
					if (hours   < 10) {hours   = "0"+hours;}
					if (minutes < 10) {minutes = "0"+minutes;}
					if (seconds < 10) {seconds = "0"+seconds;}
					return hours+':'+minutes+':'+seconds;
				}

				const timer = document.getElementById("sw-time");
				var timeInterval, timeDown, tempTimeInterval;

				const shortBeep = new Audio("/wp-content/uploads/short-beep.wav");
				const longBeep = new Audio("/wp-content/uploads/long-beep.wav");

				const playBeepSound = () => {
					shortBeep.play();
				};

				const playLongBeebSound = () => {
					longBeep.play();
				};

				startTimer = () => {
					tempTimeInterval = createTimeInterval(
						0,
						preCountSeconds,
						false,
						(temp = true),
						() => {
							timeInterval = createTimeInterval(3, 0, false, false);
						}
					);
				};

				resetTimer = () => {
					clearInterval(timeInterval);
					clearInterval(tempTimeInterval);
					timer.innerHTML = "03:00";
				};

				const createTimeInterval = (minute, second, odd, temp = false, fn) => {
					return setInterval(() => {
						// check if we are odd or even and append class to timer
						odd = !odd;
						if (odd) {
							timer.classList.add("odd");
						} else {
							timer.classList.remove("odd");
						}

						// We set the timer text to include a two digit representation
						timer.innerHTML =
							(minute < 10 ? "0" + minute : minute) +
							":" +
							(second < 10 ? "0" + second : second);

						// We check if the second equals 0
						if (second == 0) {
							if (minute === 0) {
								playLongBeebSound();
								clearInterval(temp ? tempTimeInterval : timeInterval);

								fn();
							}
							minute--;
							second = 60;
						}
						allowedSeconds = [10, 3, 2, 1];
						if (!minute && allowedSeconds.some((el) => el == second))
							playBeepSound();
						second--;
					}, 1000);
				};
				// Set the initial display text
				jQuery("#sw-time").text(timer_convert2HHMMSS(totalSeconds));
			</script>

		<?php
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _content_template() {
		?>
		<#
		view.addInlineEditingAttributes( 'countDownLength', 'basic' );
		view.addInlineEditingAttributes( 'startButtonText', 'basic' );
		view.addInlineEditingAttributes( 'endButtonText', 'basic' );
		#>
		<div class="timer">
		<input id="sw-totalSeconds" type="hidden" value="{{{ settings.countDownLength }}}" />
        <div id="sw-time">{{{ settings.countDownLength }}}</div>
        <button id="sw-go" class="timer-button">{{{ settings.startButtonText }}}</button>
        <button id="sw-rst" class="timer-button" onclick="resetTimer()">{{{ settings.endButtonText }}}</button>
    	</div>

		<script type="text/javascript">
				var totalSeconds = jQuery('#sw-totalSeconds').value();

				function timer_convert2HHMMSS(seconds) {
					var hours   = Math.floor(seconds/ 3600);
					var minutes = Math.floor((seconds - (hours * 3600)) / 60);
					var seconds = seconds - (hours * 3600) - (minutes * 60);

					if (hours   < 10) {hours   = "0"+hours;}
					if (minutes < 10) {minutes = "0"+minutes;}
					if (seconds < 10) {seconds = "0"+seconds;}
					return hours+':'+minutes+':'+seconds;
				}

				// Set display for number of seconds
				timer.text(timer_convert2HHMMSS(totalSeconds));
			</script>

		<?php
	}
}