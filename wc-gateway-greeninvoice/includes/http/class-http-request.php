<?php
/**
 * Class Http_Request
 *
 * @package    Morning\WC\Http
 * @subpackage Http_Request
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      2.0.0
 */

namespace Morning\WC\Http;

defined( 'ABSPATH' ) || exit;


/**
 * Class Http_Request
 *
 * @package Morning\WC\Http
 */
class Http_Request {
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	private string $url;
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	private array $headers = [];
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	private array $body = [];
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	private string $method = Http_Method::GET;


	/**
	 * Http_Request_Builder
	 *
	 * @param string $url Request url.
	 *
	 * @since 2.0.0
	 */
	public function __construct( string $url ) {
		$this->url = $url;
	}


	/**
	 * @param string $key Header name.
	 * @param string $value Header value.
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function add_header( string $key, string $value ): void {
		$this->headers[ $key ] = $value;
	}

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_body_json(): string {
		return wp_json_encode( $this->body );
	}


	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_url(): string {
		return $this->url;
	}

	/**
	 * @param string $url
	 *
	 * @since 2.0.0
	 */
	public function set_url( string $url ): void {
		$this->url = $url;
	}

	/**
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public function get_headers(): array {
		return $this->headers;
	}

	/**
	 * @param array $headers
	 *
	 * @since 2.0.0
	 */
	public function set_headers( array $headers ): void {
		$this->headers = $headers;
	}

	/**
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public function get_body(): array {
		return $this->body;
	}

	/**
	 * @param array $body
	 *
	 * @since 2.0.0
	 */
	public function set_body( array $body ): void {
		$this->body = $body;
	}

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_method(): string {
		return $this->method;
	}

	/**
	 * @param string $method
	 *
	 * @since 2.0.0
	 */
	public function set_method( string $method ): void {
		$this->method = $method;
	}
}
