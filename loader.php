<?php
/**
 * Dynamic stylesheets in WordPress.
 *
 * @package SeoThemes\DynamicStylesheet
 * @author  SEO Themes
 * @license GPL-3.0-or-later
 * @link    https://seothemes.com/
 */

namespace SeoThemes\DynamicStylesheet;

/**
 * Loader class.
 */
class Loader {

	/**
	 * CSS.
	 *
	 * @var string $css
	 */
	private $css;

	/**
	 * Stylesheet handle.
	 *
	 * @var string $handle
	 */
	private $handle;

	/**
	 * Static File.
	 *
	 * @var string $file
	 */
	private $file;

	/**
	 * Cached CSS.
	 *
	 * @var $cache
	 */
	private $cache;

	/**
	 * Loader constructor.
	 *
	 * @param        $css
	 * @param string $handle
	 */
	public function __construct( $css, $handle = 'dynamic' ) {
		$this->css    = $this->minify_css( $css );
		$this->handle = $handle;
		$this->file   = WP_CONTENT_DIR . '/cache/' . $handle . '.css';
		$this->cache  = get_option( $this->handle . '-css', '' );
	}

	/**
	 * Load hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		if ( $this->cache !== $this->css ) {
			update_option( $this->handle . '-css', $this->css );
			add_action( 'after_setup_theme', [ $this, 'write_to_file' ] );
		}

		if ( ! file_exists( $this->file ) ) {
			add_action( 'wp_ajax_dynamic_css', [ $this, 'dynamic_css' ] );
			add_action( 'wp_ajax_nopriv_dynamic_css', [ $this, 'dynamic_css' ] );
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'load_stylesheet' ] );
	}

	/**
	 * Conditionally load stylesheet.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_stylesheet() {
		$static  = content_url( '/cache/' . $this->handle . '.css' );
		$dynamic = admin_url( 'admin-ajax.php' ) . '?action=dynamic_css&wpnonce=' . wp_create_nonce( $this->handle . '-nonce' );

		wp_register_style(
			$this->handle,
			file_exists( $this->file ) ? $static : $dynamic,
			[],
			defined( 'WP_DEBUG' ) && WP_DEBUG ? filemtime( $this->file ) : '',
			'all'
		);

		wp_enqueue_style( $this->handle );
	}

	/**
	 * Load the dynamic CSS with ajax (if nonce is ok).
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function dynamic_css() {
		$nonce = $_REQUEST['wpnonce'];

		if ( ! wp_verify_nonce( $nonce, $this->handle . '-nonce' ) ) {
			die( 1 );

		} else {
			header( 'Content-type: text/css; charset: UTF-8' );
			echo $this->cache;
		}

		exit;
	}

	/**
	 * Quick way to mostly minify CSS with PHP.
	 *
	 * @since  1.0.0
	 * @author Gary Jones
	 *
	 * @param string $css CSS to minify.
	 *
	 * @return string Minified CSS
	 */
	public function minify_css( $css ) {
		$css = preg_replace( '/\s+/', ' ', $css );
		$css = preg_replace( '/(\s+)(\/\*(.*?)\*\/)(\s+)/', '$2', $css );
		$css = preg_replace( '~/\*(?![\!|\*])(.*?)\*/~', '', $css );
		$css = preg_replace( '/;(?=\s*})/', '', $css );
		$css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );
		$css = preg_replace( '/ (,|;|\{|}|\(|\)|>)/', '$1', $css );
		$css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );
		$css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );
		$css = preg_replace( '/0 0 0 0/', '0', $css );
		$css = preg_replace( '/#([a-f0-9])\\1([a-f0-9])\\2([a-f0-9])\\3/i', '#\1\2\3', $css );
		$css = strip_tags( trim( $css ) );

		return $css;
	}

	/**
	 * Generate static CSS file.
	 *
	 * @since 1.0.0
	 *
	 * @uses \WP_Filesystem()
	 *
	 * @return void
	 */
	public function write_to_file() {
		include_once ABSPATH . 'wp-admin/includes/file.php';
		\WP_Filesystem();
		global $wp_filesystem;
		wp_mkdir_p( dirname( $this->file ) );
		$wp_filesystem->put_contents( $this->file, $this->css );
	}
}
