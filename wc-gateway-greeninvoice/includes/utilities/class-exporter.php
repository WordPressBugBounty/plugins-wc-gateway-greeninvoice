<?php
/**
 * Class Exporter
 *
 * @package    Morning\WC\Utilities
 * @subpackage Exporter
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    1.5.0
 * @since      1.4.0
 */

namespace Morning\WC\Utilities;

use Morning\WC\Enum\Report_Format;
use Morning\WC\Exceptions\FileSystem_Exception;
use WP_Filesystem_Base;
use ZipArchive;

defined( 'ABSPATH' ) || exit;


/**
 * Class Exporter
 *
 * @package Morning\WC\Utilities
 */
final class Exporter {
	/**
	 * WordPress filesystem class instance.
	 *
	 * @var WP_Filesystem_Base
	 *
	 * @since 1.4.0
	 */
	private $filesystem;


	/**
	 * Exporter constructor.
	 *
	 * @throws FileSystem_Exception
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		$this->maybe_initialize_filesystem();
		$this->maybe_create_working_directory();
		$this->clear_files();
	}


	/**
	 * Run exporter.
	 *
	 * @return void
	 *
	 * @since 1.4.0
	 */
	public function stream(): void {
		$this->copy_log_files();
		$this->generate_site_info();

		$file = $this->create_archive_file();

		$this->send_download_headers( $file );
		$this->stream_file( $file );
	}


	/**
	 * Send file streaming headers.
	 *
	 * @param string $file File path and name.
	 *
	 * @return void
	 *
	 * @since 1.4.0
	 */
	private function send_download_headers( string $file ): void {
		ignore_user_abort( true );
		wc_set_time_limit();
		wc_nocache_headers();

		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . basename( $file ) );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
	}

	/**
	 * Stream file contents.
	 *
	 * @param string $file File path and name.
	 *
	 * @return void
	 *
	 * @since 1.4.0
	 */
	private function stream_file( string $file ): void {
		// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->filesystem->get_contents( $file );
		// @phpcs:enable

		die;
	}


	/**
	 * Copy relevant log files from WooCommerce logs directory.
	 *
	 * @return void
	 *
	 * @since 1.4.0
	 */
	private function copy_log_files(): void {
		$logs = $this->filesystem->dirlist( WC_LOG_DIR );

		$relevant_logs = array_filter(
			$logs,
			function ( $file_name ) {
				return (bool) preg_match( '/^' . MRN_WC_SLUG . '/', $file_name );
			},
			ARRAY_FILTER_USE_KEY
		);

		foreach ( $relevant_logs as $log_file => $file_details ) {
			$this->filesystem->copy( WC_LOG_DIR . $log_file, "{$this->get_working_directory()}/logs/{$log_file}" );
		}
	}

	/**
	 * Generate site report file.
	 *
	 * @return void
	 *
	 * @since 1.4.0
	 */
	private function generate_site_info(): void {
		$site_info = new Site_Info();

		$this->filesystem->put_contents( "{$this->get_working_directory()}/site-report.md", $site_info->output( Report_Format::MARKDOWN ) );
		$this->filesystem->put_contents( "{$this->get_working_directory()}/site-report.json", $site_info->output( Report_Format::JSON ) );
	}

	/**
	 * Create a zip file.
	 *
	 * @return string
	 *
	 * @since 1.4.0
	 */
	private function create_archive_file(): string {
		$files = [
			"{$this->get_working_directory()}/site-report.md",
			"{$this->get_working_directory()}/site-report.json",
		];

		$logs = $this->filesystem->dirlist( "{$this->get_working_directory()}/logs" );

		foreach ( $logs as $log_file => $file ) {
			$files[] = "{$this->get_working_directory()}/logs/{$log_file}";
		}

		$zip = new ZipArchive();
		$zip->open( "{$this->get_working_directory()}/site-data.zip", ZipArchive::CREATE );

		foreach ( $files as $file ) {
			$filename = ( strpos( $file, '/logs/' ) !== false ) ?
				basename( dirname( $file ) ) . '/' . basename( $file ) :
				basename( $file );

			$zip->addFile( $file, $filename );
		}

		$zip->close();

		return "{$this->get_working_directory()}/site-data.zip";
	}


	/**
	 * Get exporter working directory dir path.
	 *
	 * @return string
	 *
	 * @since 1.4.0
	 */
	private function get_working_directory(): string {
		$uploads_dir = wp_get_upload_dir();

		return "{$uploads_dir['basedir']}/wc-morning";
	}

	/**
	 * Initialize filesystem class if not initiated.
	 *
	 * @return void
	 * @throws FileSystem_Exception
	 *
	 * @since 1.4.0
	 */
	private function maybe_initialize_filesystem() {
		global $wp_filesystem;

		if ( ! $wp_filesystem instanceof WP_Filesystem_Base ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';

			$method = get_filesystem_method();

			if ( 'direct' === $method && ! WP_Filesystem() ) {
				throw new FileSystem_Exception( 'Could not initialize filesystem' );
			}
		}

		$this->filesystem = $wp_filesystem;
	}

	/**
	 * Create working directory and set security.
	 *
	 * @return void
	 * @throws FileSystem_Exception
	 *
	 * @since 1.4.0
	 */
	private function maybe_create_working_directory(): void {
		if ( ! $this->filesystem->exists( $this->get_working_directory() ) ) {
			if ( ! $this->filesystem->mkdir( $this->get_working_directory() ) ) {
				throw new FileSystem_Exception( 'Could not create exporter working directory' );
			}
		}

		if ( ! $this->filesystem->exists( "{$this->get_working_directory()}/logs" ) ) {
			if ( ! $this->filesystem->mkdir( "{$this->get_working_directory()}/logs" ) ) {
				throw new FileSystem_Exception( 'Could not create exporter working directory `logs` directory' );
			}
		}

		if ( ! $this->filesystem->exists( "{$this->get_working_directory()}/.htaccess" ) ) {
			if ( ! $this->filesystem->put_contents( "{$this->get_working_directory()}/.htaccess", 'deny from all' ) ) {
				throw new FileSystem_Exception( 'Could not create exporter working directory `.htaccess` file' );
			}
		}

		if ( ! $this->filesystem->exists( "{$this->get_working_directory()}/index.html" ) ) {
			if ( ! $this->filesystem->put_contents( "{$this->get_working_directory()}/index.html", '' ) ) {
				throw new FileSystem_Exception( 'Could not create exporter working directory `index.html` file' );
			}
		}
	}

	/**
	 * Remove old files.
	 *
	 * @return void
	 *
	 * @since 1.4.0
	 */
	private function clear_files(): void {
		$logs = $this->filesystem->dirlist( "{$this->get_working_directory()}/logs" );

		foreach ( $logs as $log_file => $file ) {
			$this->filesystem->delete( "{$this->get_working_directory()}/logs/{$log_file}" );
		}

		$this->filesystem->delete( "{$this->get_working_directory()}/site-report.md" );
		$this->filesystem->delete( "{$this->get_working_directory()}/site-data.zip" );
	}
}
