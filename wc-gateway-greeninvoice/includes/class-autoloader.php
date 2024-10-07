<?php
/**
 * Class Autoloader
 *
 * @package Morning\WC
 * @author  Dor Zuberi <admin@dorzki.io>
 * @version 1.5.0
 * @since   1.5.0
 */

namespace Morning\WC;

defined( 'ABSPATH' ) || exit;


/**
 * Class Autoloader
 *
 * @package Morning\WC
 */
class Autoloader {
	/**
	 * Register autoloader.
	 *
	 * @return void
	 *
	 * @since 1.5.0
	 */
	public function register(): void {
		spl_autoload_register( [ $this, 'autoload' ] );
	}

	/**
	 * Plugin autoload mechanism.
	 *
	 * @param string $class_name Class namespace and name.
	 *
	 * @return void
	 *
	 * @since 1.5.0
	 */
	public function autoload( string $class_name ): void {
		if ( false === strpos( $class_name, 'Morning\\WC' ) ) {
			return;
		}

		$file_path = $this->build_class_file( $class_name );

		if ( is_file( $file_path ) ) {
			include_once $file_path;
		}
	}

	/**
	 * Convert namespace and class name to file path.
	 *
	 * @param string $class_name Class namespace and name.
	 *
	 * @return string
	 *
	 * @since 1.5.0
	 */
	public function build_class_file( string $class_name ): string {
		$file_path_parts  = explode( '\\', $class_name );
		$file_path_length = count( $file_path_parts ) - 1;

		// Generate file name.
		$file_name = strtolower( $file_path_parts[ $file_path_length ] );
		$file_name = str_replace( '_', '-', $file_name );
		$file_name = "class-{$file_name}.php";

		// Generate file path.
		$file_path = trailingslashit( __DIR__ );

		for ( $i = 2; $i < $file_path_length; $i ++ ) {
			$dir_name = strtolower( $file_path_parts[ $i ] );
			$dir_name = str_replace( '_', '-', $dir_name );

			$file_path .= trailingslashit( $dir_name );
		}

		return $file_path . $file_name;
	}
}
