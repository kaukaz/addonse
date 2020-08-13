<?php
namespace Addonse;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit;

class Addonse_Loader
{
  private static $_instance = null;
  private $_modules_manager;
  private $classes_aliases = [
    'ElementPack\Modules\PanelPostsControl\Module' => 'Addonse\Modules\QueryControl\Module',
    'ElementPack\Modules\PanelPostsControl\Controls\Group_Control_Posts' => 'Addonse\Modules\QueryControl\Controls\Group_Control_Posts',
    'ElementPack\Modules\PanelPostsControl\Controls\Query' => 'Addonse\Modules\QueryControl\Controls\Query',
  ]; 
  public function get_theme() {
    return wp_get_theme();
  } 
  public function __clone() {
    _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheating huh?', 'addonse' ), '1.0' );
  } 
  public function __wakeup() {
    _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheating huh?', 'addonse' ), '1.0' );
  }
  public static function elementor() {
    return \Elementor\Plugin::$instance;
  } 
  public static function instance() {
      if ( is_null( self::$_instance ) ) {
          self::$_instance = new self();
      }
      return self::$_instance;
  }
  private function includes(){
      include_once( ADDONSE_DIR_PATH . 'includes/modules-manager.php' );
  }
  private function __construct() {
      spl_autoload_register([ $this, 'autoload' ]);
      $this->includes();
      $this->setup_hooks(); 
  }

  public function autoload( $class ) {
    if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
      return;
    }

    $has_class_alias = isset( $this->classes_aliases[ $class ] );
    if ( $has_class_alias ) {
      $class_alias_name = $this->classes_aliases[ $class ];
      $class_to_load = $class_alias_name;
    } else {
      $class_to_load = $class;
    }

    if ( ! class_exists( $class_to_load ) ) {
      $filename = strtolower(
        preg_replace(
          [ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
          [ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
          $class_to_load
        )
      );
      $filename = ADDONSE_DIR_PATH . $filename . '.php';

      if ( is_readable( $filename ) ) {
        include( $filename );
      }
    }

    if ( $has_class_alias ) {
      class_alias( $class_alias_name, $class );
    }
  }
    
  private function setup_hooks() {
    add_action( 'elementor/init', [ $this, 'addonse_init' ] ); 
    add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] ); 
    add_action( 'elementor/frontend/before_register_styles', [ $this, 'register_site_styles' ] );
    add_action( 'elementor/frontend/before_register_scripts', [ $this, 'register_site_scripts' ] ); 
    add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_preview_styles' ] );
    add_action( 'elementor/frontend/after_register_styles', [ $this, 'enqueue_site_styles' ] );
    add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_site_scripts' ] );
    add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] ); 
  }
  public function enqueue_site_styles() {

		$direction_suffix = is_rtl() ? '.rtl' : '';
		wp_enqueue_style( 'addonse-site', ADDONSE_DIR_URL . 'assets/css/addonse-site' . $direction_suffix . '.css', [], ADDONSE_VERSION );		
	} 
  public function register_site_scripts() {
      $suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min'; 
      //wp_register_script( 'datatables', '//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js', ['jquery'], null, true ); 
      
  }
  public function register_site_styles() {
      $direction_suffix = is_rtl() ? '.rtl' : '';
      //wp_register_style( 'dataTables', '//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css', [], ADDONSE_VERSION ); 
  } 
  public function enqueue_site_scripts() {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    wp_enqueue_script( 'addonse-site', ADDONSE_DIR_URL . 'assets/js/addonse-site' . $suffix . '.js', ['jquery', 'elementor-frontend'], ADDONSE_VERSION ); 
    $script_config = [ 
      'ajax'       => ['url'=>admin_url( 'admin-ajax.php' ),'nonce'=>wp_create_nonce('addonse-site')],
      'elements_data' => $this->elements_data,
    ];
    $script_config = apply_filters( 'addonse/frontend/localize_settings', $script_config );
    wp_localize_script( 'addonse-site', 'AddonseConfig', $script_config ); 
  }

  public function enqueue_admin_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'addonse-admin', ADDONSE_DIR_URL . 'assets/js/addonse-admin' . $suffix . '.js', ['jquery'], ADDONSE_VERSION, true );
	}

  public function enqueue_preview_styles() {
		$direction_suffix = is_rtl() ? '.rtl' : '';
		wp_enqueue_style('addonse-preview', ADDONSE_DIR_URL . 'assets/css/addonse-preview' . $direction_suffix . '.css', '', ADDONSE_VERSION );
	}


	public function enqueue_editor_styles() {
		$direction_suffix = is_rtl() ? '.rtl' : '';
    wp_enqueue_style('addonse-editor', ADDONSE_DIR_URL . 'assets/css/addonse-editor' . $direction_suffix . '.css', '', ADDONSE_VERSION );
    wp_enqueue_style('addonse-icon-font', ADDONSE_DIR_URL . 'assets/fonts/addonse-icon-style' . $direction_suffix . '.css', '', ADDONSE_VERSION );
  } 
  
  public function addonse_init(){
      $this->_modules_manager = new Manager();
      $elementsManager = addonse_elementor()->elements_manager;
      $elementsManager->add_category(
          ADDONSE_SLUG,
          array(
            'title' => ADDONSE_TITLE,
            'icon'  => 'fa fa-smile-o',
          )
        );
      do_action( 'addonse/init' );
  }
}
if ( ! defined( 'ADDONSE_TESTS' ) ) {
	Addonse_Loader::instance();
}
function addonse_config() {
	return Addonse_Loader::instance();
}