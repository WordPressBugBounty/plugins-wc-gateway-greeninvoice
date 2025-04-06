<?php
/**
 * Class Http_Response
 *
 * @package    Morning\WC\Http
 * @subpackage Http_Response
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.0
 * @since      2.0.0
 */

namespace Morning\WC\Http;

use WP_Error;
use WP_Http_Cookie;

defined( 'ABSPATH' ) || exit;


/**
 * Class Http_Response
 *
 * @package Morning\WC\Http
 */
class Http_Response {
	/**
	 * @var Http_Request
	 *
	 * @since 2.0.0
	 */
	private Http_Request $request;
	/**
	 * @var WP_Error|array
	 *
	 * @since 2.0.0
	 */
	private $raw_response;
	/**
	 * @var int
	 *
	 * @since 2.0.0
	 */
	private int $status;
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	private array $headers = [];
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	private string $body;
	/**
	 * @var WP_Http_Cookie[]
	 *
	 * @since 2.0.0
	 */
	private array $cookies = [];
	/**
	 * @var string|null
	 *
	 * @since 2.0.0
	 */
	private ?string $error_code = null;
	/**
	 * @var string|null
	 *
	 * @since 2.0.0
	 */
	private ?string $error_message = null;


	/**
	 * Http_Response constructor.
	 *
	 * @param Http_Request $request Http request.
	 * @param WP_Error|array $response Raw http response.
	 *
	 * @since 2.0.0
	 */
	public function __construct( Http_Request $request, $response ) {
		$this->request      = $request;
		$this->raw_response = $response;

		if ( is_wp_error( $response ) ) {
			$this->status = Http_Code::BAD_REQUEST;
			$this->body   = '';
			$this->set_error_code( $response->get_error_code() );
			$this->set_error_message( $response->get_error_message() );
		} else {
			$this->status  = $response['response']['code'];
			$this->headers = $response['response']['headers'] ?? [];
			$this->body    = $response['body'];
			$this->cookies = $response['cookies'] ?? [];

			$json_body = $this->json_body();
			if ( ! empty( $json_body['errorCode'] ) ) {
				$this->set_error_code( $json_body['errorCode'] );
			}

			if ( ! empty( $json_body['errorMessage'] ) ) {
				$this->set_error_message( $json_body['errorMessage'] );
			}
		}
	}


	/**
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public function json_body(): array {
		$json = json_decode( $this->body, true );

		return is_array( $json ) ? $json : [ $json ];
	}

	/**
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function is_ok(): bool {
		return Http_Code::is_success( $this->status );
	}


	/**
	 * @return Http_Request
	 *
	 * @since 2.0.0
	 */
	public function get_request(): Http_Request {
		return $this->request;
	}

	/**
	 * @param Http_Request $request
	 *
	 * @since 2.0.0
	 */
	public function set_request( Http_Request $request ): void {
		$this->request = $request;
	}

	/**
	 * @return array|WP_Error
	 *
	 * @since 2.0.0
	 */
	public function get_raw_response() {
		return $this->raw_response;
	}

	/**
	 * @param array|WP_Error $raw_response
	 *
	 * @since 2.0.0
	 */
	public function set_raw_response( $raw_response ): void {
		$this->raw_response = $raw_response;
	}

	/**
	 * @return int
	 *
	 * @since 2.0.0
	 */
	public function get_status(): int {
		return $this->status;
	}

	/**
	 * @param int $status
	 *
	 * @since 2.0.0
	 */
	public function set_status( int $status ): void {
		$this->status = $status;
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
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public function get_body(): string {
		return $this->body;
	}

	/**
	 * @param string $body
	 *
	 * @since 2.0.0
	 */
	public function set_body( string $body ): void {
		$this->body = $body;
	}

	/**
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public function get_cookies(): array {
		return $this->cookies;
	}

	/**
	 * @param array $cookies
	 *
	 * @since 2.0.0
	 */
	public function set_cookies( array $cookies ): void {
		$this->cookies = $cookies;
	}

	/**
	 * @return string|null
	 *
	 * @since 2.0.0
	 */
	public function get_error_code(): ?string {
		return $this->error_code;
	}

	/**
	 * @param string|null $error_code
	 *
	 * @since 2.0.0
	 */
	public function set_error_code( ?string $error_code ): void {
		$this->error_code = $error_code;
	}

	/**
	 * @return string|null
	 *
	 * @since 2.0.0
	 */
	public function get_error_message(): ?string {
		return $this->error_message;
	}

	/**
	 * @param string|null $error_message
	 *
	 * @since 2.0.0
	 */
	public function set_error_message( ?string $error_message ): void {
		$this->error_message = $error_message;
	}
}
