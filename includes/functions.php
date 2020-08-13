<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Call a shortcode function by tag name.
 *
 * @since  1.0.0
 *
 * @param string $tag     The shortcode whose function to call.
 * @param array  $atts    The attributes to pass to the shortcode function. Optional.
 * @param array  $content The shortcode's content. Default is null (none).
 *
 * @return string|bool False on failure, the result of the shortcode on success.
 */
function addonse_do_shortcode( $tag, array $atts = array(), $content = null ) {
    global $shortcode_tags;
    if ( ! isset( $shortcode_tags[ $tag ] ) ) {
        return false;
    }
    return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
}

/**
 * Sanitize html class string
 *
 * @param $class
 * @return string
 */
function addonse_sanitize_html_class_param( $class ) {
    $classes = ! empty( $class ) ? explode( ' ', $class ) : [];
    $sanitized = [];
    if ( ! empty( $classes ) ) {
        $sanitized = array_map( function( $cls ) {
            return sanitize_html_class( $cls );
        }, $classes );
    }
    return implode( ' ', $sanitized );
}

function addonse_is_script_debug_enabled() {
    return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
}

function addonse_prepare_data_prop_settings( &$settings, $field_map = [] ) {
    $data = [];
    foreach ( $field_map as $key => $data_key ) {
        $setting_value = addonse_get_setting_value( $settings, $key );
        list( $data_field_key, $data_field_type ) = explode( '.', $data_key );
        $validator = $data_field_type . 'val';

        if ( is_callable( $validator ) ) {
            $val = call_user_func( $validator, $setting_value );
        } else {
            $val = $setting_value;
        }
        $data[ $data_field_key ] = $val;
    }
    return wp_json_encode( $data );
}

function addonse_get_setting_value( &$settings, $keys ) {
    if ( ! is_array( $keys ) ) {
        $keys = explode( '.', $keys );
    }
    if ( is_array( $settings[ $keys[0] ] ) ) {
        return addonse_get_setting_value( $settings[ $keys[0] ], array_slice( $keys, 1 ) );
    }
    return $settings[ $keys[0] ];
}

function addonse_is_localhost() {
    return isset( $_SERVER['REMOTE_ADDR'] ) && in_array( $_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'] );
}

function addonse_get_css_cursors() {
    return [
        'default' => __( 'Default', 'addonse' ),
        'alias' => __( 'Alias', 'addonse' ),
        'all-scroll' => __( 'All scroll', 'addonse' ),
        'auto' => __( 'Auto', 'addonse' ),
        'cell' => __( 'Cell', 'addonse' ),
        'context-menu' => __( 'Context menu', 'addonse' ),
        'col-resize' => __( 'Col-resize', 'addonse' ),
        'copy' => __( 'Copy', 'addonse' ),
        'crosshair' => __( 'Crosshair', 'addonse' ),
        'e-resize' => __( 'E-resize', 'addonse' ),
        'ew-resize' => __( 'EW-resize', 'addonse' ),
        'grab' => __( 'Grab', 'addonse' ),
        'grabbing' => __( 'Grabbing', 'addonse' ),
        'help' => __( 'Help', 'addonse' ),
        'move' => __( 'Move', 'addonse' ),
        'n-resize' => __( 'N-resize', 'addonse' ),
        'ne-resize' => __( 'NE-resize', 'addonse' ),
        'nesw-resize' => __( 'NESW-resize', 'addonse' ),
        'ns-resize' => __( 'NS-resize', 'addonse' ),
        'nw-resize' => __( 'NW-resize', 'addonse' ),
        'nwse-resize' => __( 'NWSE-resize', 'addonse' ),
        'no-drop' => __( 'No-drop', 'addonse' ),
        'not-allowed' => __( 'Not-allowed', 'addonse' ),
        'pointer' => __( 'Pointer', 'addonse' ),
        'progress' => __( 'Progress', 'addonse' ),
        'row-resize' => __( 'Row-resize', 'addonse' ),
        's-resize' => __( 'S-resize', 'addonse' ),
        'se-resize' => __( 'SE-resize', 'addonse' ),
        'sw-resize' => __( 'SW-resize', 'addonse' ),
        'text' => __( 'Text', 'addonse' ),
        'url' => __( 'URL', 'addonse' ),
        'w-resize' => __( 'W-resize', 'addonse' ),
        'wait' => __( 'Wait', 'addonse' ),
        'zoom-in' => __( 'Zoom-in', 'addonse' ),
        'zoom-out' => __( 'Zoom-out', 'addonse' ),
        'none' => __( 'None', 'addonse' ),
    ];
}

function addonse_get_css_blend_modes() {
    return [
        'normal' => __( 'Normal', 'addonse' ),
        'multiply' => __( 'Multiply', 'addonse' ),
        'screen' => __( 'Screen', 'addonse' ),
        'overlay' => __( 'Overlay', 'addonse' ),
        'darken' => __( 'Darken', 'addonse' ),
        'lighten' => __( 'Lighten', 'addonse' ),
        'color-dodge' => __( 'Color Dodge', 'addonse' ),
        'color-burn' => __( 'Color Burn', 'addonse' ),
        'saturation' => __( 'Saturation', 'addonse' ),
        'difference' => __( 'Difference', 'addonse' ),
        'exclusion' => __( 'Exclusion', 'addonse' ),
        'hue' => __( 'Hue', 'addonse' ),
        'color' => __( 'Color', 'addonse' ),
        'luminosity' => __( 'Luminosity', 'addonse' ),
    ];
}

/**
 * Check elementor version
 *
 * @param string $version
 * @param string $operator
 * @return bool
 */
function addonse_is_elementor_version( $operator = '<', $version = '2.6.0' ) {
    return defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, $version, $operator );
}

/**
 * Render icon html with backward compatibility
 *
 * @param array $settings
 * @param string $old_icon_id
 * @param string $new_icon_id
 * @param array $attributes
 */
function addonse_render_icon( $settings = [], $old_icon_id = 'icon', $new_icon_id = 'selected_icon', $attributes = [] ) {
    // Check if its already migrated
    $migrated = isset( $settings['__fa4_migrated'][ $new_icon_id ] );
    // Check if its a new widget without previously selected icon using the old Icon control
    $is_new = empty( $settings[ $old_icon_id ] );

    $attributes['aria-hidden'] = 'true';

    if ( addonse_is_elementor_version( '>=', '2.6.0' ) && ( $is_new || $migrated ) ) {
        \Elementor\Icons_Manager::render_icon( $settings[ $new_icon_id ], $attributes );
    } else {
        if ( empty( $attributes['class'] ) ) {
            $attributes['class'] = $settings[ $old_icon_id ];
        } else {
            if ( is_array( $attributes['class'] ) ) {
                $attributes['class'][] = $settings[ $old_icon_id ];
            } else {
                $attributes['class'] .= ' ' . $settings[ $old_icon_id ];
            }
        }
        printf( '<i %s></i>', \Elementor\Utils::render_html_attributes( $attributes ) );
    }
}


/**
 * Get elementor instance
 *
 * @return \Elementor\Plugin
 */
function addonse_elementor() {
    return \Elementor\Plugin::instance();
}

/**
 * Get a list of all the allowed html tags.
 *
 * @param string $level Allowed levels are basic and intermediate
 * @return array
 */
function addonse_get_allowed_html_tags( $level = 'basic' ) {
    $allowed_html = [
        'b' => [],
        'i' => [],
        'u' => [],
        'em' => [],
        'br' => [],
        'abbr' => [
            'title' => [],
        ],
        'span' => [
            'class' => [],
        ],
        'strong' => [],
    ];

    if ( $level === 'intermediate' ) {
        $allowed_html['a'] = [
            'href' => [],
            'title' => [],
            'class' => [],
            'id' => [],
        ];
    }

    return $allowed_html;
}

/**
 * Strip all the tags except allowed html tags
 *
 * The name is based on inline editing toolbar name
 *
 * @param string $string
 * @return string
 */
function addonse_kses_intermediate( $string = '' ) {
    return wp_kses( $string, addonse_get_allowed_html_tags( 'intermediate' ) );
}

/**
 * Strip all the tags except allowed html tags
 *
 * The name is based on inline editing toolbar name
 *
 * @param string $string
 * @return string
 */
function addonse_kses_basic( $string = '' ) {
    return wp_kses( $string, addonse_get_allowed_html_tags( 'basic' ) );
}

/**
 * Get a translatable string with allowed html tags.
 *
 * @param string $level Allowed levels are basic and intermediate
 * @return string
 */
function addonse_get_allowed_html_desc( $level = 'basic' ) {
    if ( ! in_array( $level, [ 'basic', 'intermediate' ] ) ) {
        $level = 'basic';
    }

    $tags_str = '<' . implode( '>,<', array_keys( addonse_get_allowed_html_tags( $level ) ) ) . '>';
    return sprintf( __( 'This input field has support for the following HTML tags: %1$s', 'addonse' ), '<code>' . esc_html( $tags_str ) . '</code>' );
}

function addonse_has_pro() {
    return defined( 'HAPPY_ADDONS_PRO_VERSION' );
}

function addonse_get_b64_icon() {
    return 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMiAzMiI+PGcgZmlsbD0iI0ZGRiI+PHBhdGggZD0iTTI4LjYgNy44aC44Yy41IDAgLjktLjUuOC0xIDAtLjUtLjUtLjktMS0uOC0zLjUuMy02LjgtMS45LTcuOC01LjMtLjEtLjUtLjYtLjctMS4xLS42cy0uNy42LS42IDEuMWMxLjIgMy45IDQuOSA2LjYgOC45IDYuNnoiLz48cGF0aCBkPSJNMzAgMTEuMWMtLjMtLjYtLjktMS0xLjYtMS0uOSAwLTEuOSAwLTIuOC0uMi00LS44LTctMy42LTguNC03LjEtLjMtLjYtLjktMS4xLTEuNi0xQzguMyAxLjkgMS44IDcuNC45IDE1LjEuMSAyMi4yIDQuNSAyOSAxMS4zIDMxLjIgMjAgMzQuMSAyOSAyOC43IDMwLjggMTkuOWMuNy0zLjEuMy02LjEtLjgtOC44em0tMTEuNiAxLjFjLjEtLjUuNi0uOCAxLjEtLjdsMy43LjhjLjUuMS44LjYuNyAxLjFzLS42LjgtMS4xLjdsLTMuNy0uOGMtLjQtLjEtLjgtLjYtLjctMS4xek0xMC4xIDExYy4yLTEuMSAxLjQtMS45IDIuNS0xLjYgMS4xLjIgMS45IDEuNCAxLjYgMi41LS4yIDEuMS0xLjQgMS45LTIuNSAxLjYtMS0uMi0xLjgtMS4zLTEuNi0yLjV6bTE0LjYgMTAuNkMyMi44IDI2IDE3LjggMjguNSAxMyAyN2MtMy42LTEuMi02LjItNC41LTYuNS04LjItLjEtMSAuOC0xLjcgMS43LTEuNmwxNS40IDIuNWMuOSAwIDEuNCAxIDEuMSAxLjl6Ii8+PHBhdGggZD0iTTE3LjEgMjIuOGMtMS45LS40LTMuNy4zLTQuNyAxLjctLjIuMy0uMS43LjIuOS42LjMgMS4yLjUgMS45LjcgMS44LjQgMy43LjEgNS4xLS43LjMtLjIuNC0uNi4yLS45LS43LS45LTEuNi0xLjUtMi43LTEuN3oiLz48L2c+PC9zdmc+';
}

function addonse_get_dashboard_link( $suffix = '#home' ) {
    return add_query_arg( [ 'page' => 'happy-addons' . $suffix ], admin_url( 'admin.php' ) );
}

function addonse_get_current_user_display_name() {
    $user = wp_get_current_user();
    $name = 'user';
    if ( $user->exists() && $user->display_name ) {
        $name = $user->display_name;
    }
    return $name;
}

/**
 * Twitter Feed Ajax call
 */
function addonse_twitter_feed_ajax() {

	define( 'HA_TWEETS_TOKEN', '_tweet_token' );
	define( 'HA_TWEETS_CASH', '_tweet_cash' );

	$security = check_ajax_referer('happy_addons_twitter_nonce', 'security');

	if ( true == $security && isset( $_POST['query_settings'] ) ) :
		$settings = $_POST['query_settings'];
		$loaded_item = $_POST['loaded_item'];

		$user_name = trim($settings['user_name']);

		$transient_key = $settings['id'] . '_' . $user_name . HA_TWEETS_CASH;
		$twitter_data = get_transient($transient_key);
		$credentials = $settings['credentials'];

		$auth_response = wp_remote_post('https://api.twitter.com/oauth2/token',
			array(
				'method' => 'POST',
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => [
					'Authorization' => 'Basic ' . $credentials,
					'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
				],
				'body' => ['grant_type' => 'client_credentials'],
			));

		$body = json_decode( wp_remote_retrieve_body( $auth_response ) );
		$token = $body->access_token;

		$tweets_response = wp_remote_get('https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $settings['user_name'] . '&count=999&tweet_mode=extended',
			array(
				'httpversion' => '1.1',
				'blocking' => true,
				'headers' => ['Authorization' => "Bearer $token",],
			));

		if ( !is_wp_error( $tweets_response ) ) {
			$twitter_data = json_decode( wp_remote_retrieve_body( $tweets_response ), true );
			set_transient($settings['id'] . '_' . $settings['user_name'] . HA_TWEETS_CASH, $twitter_data, 0); // 2 * MINUTE_IN_SECONDS
		}

		switch ($settings['sort_by']) {
			case 'old-posts':
				usort($twitter_data, function ($a,$b) {
					if ( $a['created_at'] == $b['created_at'] ) return 0;
					return ( $a['created_at'] < $b['created_at'] ) ? -1 : 1 ;
				});
				break;
			case 'favorite_count':
				usort($twitter_data, function ($a,$b){
					if ($a['favorite_count'] == $b['favorite_count']) return 0;
					return ($a['favorite_count'] > $b['favorite_count']) ? -1 : 1 ;
				});
				break;
			case 'retweet_count':
				usort($twitter_data, function ($a,$b){
					if ($a['retweet_count'] == $b['retweet_count']) return 0;
					return ($a['retweet_count'] > $b['retweet_count']) ? -1 : 1 ;
				});
				break;
			default:
				$twitter_data;
		}

		$items = array_splice($twitter_data, $loaded_item, $settings['tweets_limit'] );

		foreach ($items as $item) :
			?>
			<div class="ha-tweet-item">

				<?php if ( $settings['show_twitter_logo'] == 'yes' ) : ?>
					<div class="ha-tweeter-feed-icon">
						<i class="fa fa-twitter"></i>
					</div>
				<?php endif; ?>

				<div class="ha-tweet-inner-wrapper">

					<div class="ha-tweet-author">
						<?php if ( $settings['show_user_image'] == 'yes' ) : ?>
							<a href="<?php echo esc_url( 'https://twitter.com/'.$user_name ); ?>">
								<img
									src="<?php echo esc_url( $item['user']['profile_image_url_https'] ); ?>"
									alt="<?php echo esc_attr( $item['user']['name'] ); ?>"
									class="ha-tweet-avatar"
								>
							</a>
						<?php endif; ?>

						<div class="ha-tweet-user">
							<?php if ( $settings['show_name'] == 'yes' ) : ?>
								<a href="<?php echo esc_url( 'https://twitter.com/'.$user_name ); ?>" class="ha-tweet-author-name">
									<?php echo esc_html( $item['user']['name'] ); ?>
								</a>
							<?php endif; ?>

							<?php if ( $settings['show_user_name'] == 'yes' ) : ?>
								<a href="<?php echo esc_url( 'https://twitter.com/'.$user_name ); ?>" class="ha-tweet-username">
									<?php echo esc_html( $settings['user_name'] ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>

					<div class="ha-tweet-content">
						<p><?php echo esc_html( $item['full_text'] ); ?></p>

						<?php if ( $settings['show_date'] == 'yes' ) : ?>
							<div class="ha-tweet-date">
								<?php echo esc_html( date("M d Y", strtotime( $item['created_at'] ) ) );?>
							</div>
						<?php endif; ?>
					</div>

				</div>

				<?php if ( $settings['show_favorite'] == 'yes' || $settings['show_retweet'] == 'yes' ) : ?>
					<div class="ha-tweet-footer-wrapper">
						<div class="ha-tweet-footer">

							<?php if ( $settings['show_favorite'] == 'yes' ) : ?>
								<div class="ha-tweet-favorite">
									<?php echo esc_html( $item['favorite_count'] ); ?>
									<i class="fa fa-heart-o"></i>
								</div>
							<?php endif; ?>

							<?php if ( $settings['show_retweet'] == 'yes' ) : ?>
								<div class="ha-tweet-retweet">
									<?php echo esc_html( $item['retweet_count'] ); ?>
									<i class="fa fa-retweet"></i>
								</div>
							<?php endif; ?>

						</div>
					</div>
				<?php endif; ?>

			</div>
		<?php
		endforeach;
	endif;
	wp_die();

}
add_action( 'wp_ajax_addonse_twitter_feed_action', 'addonse_twitter_feed_ajax' );
add_action( 'wp_ajax_nopriv_addonse_twitter_feed_action', 'addonse_twitter_feed_ajax' );

function addonse_get_icon_for_label() {
    return '<i style="position: relative; top: 1px" class="hm hm-happyaddons"></i> ';
} 