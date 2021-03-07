<?php
/**
 * TinyCog Timer Stopwatch class.
 *
 * @category   Class
 * @package    Tinycog Timer
 * @subpackage WordPress
 * @author     Josh Holmes <josh@joshholmes.com>
 * @copyright  2021 Josh Holmes
 * @license    https://opensource.org/licenses/GPL-2.0 
 * @link       link(https://github.com/joshholmes/tinycog-timer,
 *             Tinycog Timer)
 * @since      0.0.1
 * php version 7.3.9
 */

namespace TinycogTimer\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

// Security Note: Blocks direct access to the plugin PHP files.
defined( 'ABSPATH' ) || die();

/**
 * Tinycog Stopwatch widget class.
 */
class Stopwatch extends Widget_Base {
	/**
	 * Class constructor.
	 *
	 * @param array $data Widget data.
	 * @param array $args Widget arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'stopwatch', plugins_url( '/assets/css/timer.css', TINYCOG_TIMER ), array(), '0.0.1' );
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'stopwatch';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Tinycog Timer - Stopwatch', 'tinycog-timer-stopwatch' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-clock-o';
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
		return array( 'stopwatch' );
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
				'label' => __( 'Content', 'tinycog-timer' ),
			)
		);

		$this->add_control(
			'preCountDownSeconds',
			array(
				'label'   => __( 'Pre Count down Seconds', 'tinycog-timer' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => __( '10', 'tinycog-timer' ),
			)
		);

		$this->add_control(
			'startButtonText',
			array(
				'label'   => __( 'startButtonText', 'tinycog-timer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Start', 'tinycog-timer' ),
			)
		);

		$this->add_control(
			'endButtonText',
			array(
				'label'   => __( 'endButtonText', 'tinycog-timer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'End', 'tinycog-timer' ),
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

		$this->add_inline_editing_attributes( 'startButtonText', 'basic' );
		$this->add_inline_editing_attributes( 'endButtonText', 'basic' );
		?>

		<div class="tinycog-timer">
		
			<input id="tinycog-timer-preCountSeconds" type="hidden" value="<?php echo wp_kses( $settings['preCountDownSeconds'], array() ); ?>" />
			<div id="tinycog-timer-time-display" <?php echo $this->get_render_attribute_string( 'countDownLength' ); ?>></div>
			<div id="tinycog-timer-start" onclick="startTimer()" class="tinycog-timer-button" <?php echo $this->get_render_attribute_string( 'startButtonText' ); ?>><?php echo wp_kses( $settings['startButtonText'], array() ); ?></div>
			<div id="tinycog-timer-reset" onclick="resetTimer()" class="tinycog-timer-button" <?php echo $this->get_render_attribute_string( 'endButtonText' ); ?>><?php echo wp_kses( $settings['endButtonText'], array() ); ?></div>
		</div>
		<script type="text/javascript">
				var preCountSeconds = document.getElementById('tinycog-timer-preCountSeconds').value;
				var totalSeconds = 0;

				function timer_convert2HHMMSS(seconds) {
					var hours   = Math.floor(seconds/ 3600);
					var minutes = Math.floor((seconds - (hours * 3600)) / 60);
					var seconds = seconds - (hours * 3600) - (minutes * 60);

					if (hours   < 10) {hours   = "0"+hours;}
					if (minutes < 10) {minutes = "0"+minutes;}
					if (seconds < 10) {seconds = "0"+seconds;}
					return hours+':'+minutes+':'+seconds;
				}

				const timer = document.getElementById("tinycog-timer-time-display");
				var preCountDownInterval, countUpInterval;

				const shortBeep = new Audio("<?php echo plugin_dir_url( __FILE__ ) ?>../assets/audio/short-beep.wav");
				const longBeep = new Audio("<?php echo plugin_dir_url( __FILE__ ) ?>../assets/audio/long-beep.wav");

				const playBeepSound = () => {
					shortBeep.play();
				};

				const playLongBeepSound = () => {
					longBeep.play();
				};

				timerRunning = false;
				startTimer = () => {
					if (!timerRunning) {
						shortBeep.play();
						longBeep.play();

						timerRunning = true;
						preCountDownInterval = preCountDown(preCountSeconds);
					}
				};

				resetTimer = () => {
					clearInterval(countUpInterval);
					clearInterval(preCountDownInterval);
					timer.innerHTML = timer_convert2HHMMSS(totalSeconds);
					timerRunning = false;
				};

				const countUp = (seconds) => {
					return setInterval(() => {
						timer.classList.remove("pre-countdown");

						timer.innerHTML = timer_convert2HHMMSS(seconds);

						seconds++;
					}, 1000);
				};

				const preCountDown = (seconds) => {
					return setInterval(() => {
						timer.classList.add("pre-countdown");

						timer.innerHTML = timer_convert2HHMMSS(seconds);

						// We check if the seconds equals 0
						if (seconds == 0) {
							playLongBeepSound();
							clearInterval(preCountDownInterval);
							countUpInterval = countUp(0);
						}
						allowedSeconds = [3, 2, 1];
						if (allowedSeconds.some((el) => el == seconds)) {
							playBeepSound();
						}
						seconds--;
					}, 1000);
				};
				// Set the initial display text
				timer.textContent = timer_convert2HHMMSS(totalSeconds);
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
		<div class="tinycog-timer">
		<input id="tinycog-timer-totalSeconds" type="hidden" value="{{{ settings.countDownLength }}}" />
        <div id="tinycog-timer-time-display">{{{ settings.countDownLength }}}</div>
        <button id="tinycog-timer-start" class="tinycog-timer-button">{{{ settings.startButtonText }}}</button>
        <button id="tinycog-timer-reset" class="tinycog-timer-button" onclick="resetTimer()">{{{ settings.endButtonText }}}</button>
    	</div>

		<script type="text/javascript">
				var totalSeconds = 0;

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