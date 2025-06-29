<?php
/**
 * Class HTTP_Client
 *
 * @package    Morning\WC\Http
 * @subpackage HTTP_Client
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.0.5
 * @since      2.0.0
 */

namespace Morning\WC\Http;

use Morning\WC\Utilities\Logger;
use WP_Error;
use WP_Http;

defined( 'ABSPATH' ) || exit;


/**
 * Class HTTP_Client
 *
 * @package Morning\WC\Http
 */
final class HTTP_Client {
	/**
	 * @var WP_Http
	 *
	 * @since 2.0.0
	 */
	private WP_Http $client;


	/**
	 * Http_Client constructor.
	 *
	 * @param WP_Http $client
	 *
	 * @since 2.0.0
	 */
	public function __construct( WP_Http $client ) {
		$this->client = $client;
	}


	/**
	 * @param Http_Request $request Http request.
	 *
	 * @return Http_Response
	 *
	 * @since 2.0.0
	 */
	public function get( Http_Request $request ): Http_Response {
		$request->set_method( Http_Method::GET );

		return $this->request( $request );
	}

	/**
	 * @param Http_Request $request Http request.
	 *
	 * @return Http_Response
	 *
	 * @since 2.0.0
	 */
	public function post( Http_Request $request ): Http_Response {
		$request->set_method( Http_Method::POST );

		return $this->request( $request );
	}

	/**
	 * @param Http_Request $request Http request.
	 *
	 * @return Http_Response
	 *
	 * @since 2.0.0
	 */
	public function put( Http_Request $request ): Http_Response {
		$request->set_method( Http_Method::PUT );

		return $this->request( $request );
	}

	/**
	 * @param Http_Request $request Http request.
	 *
	 * @return Http_Response
	 *
	 * @since 2.0.0
	 */
	public function delete( Http_Request $request ): Http_Response {
		$request->set_method( Http_Method::DELETE );

		return $this->request( $request );
	}


	/**
	 * @param Http_Request $request
	 *
	 * @return Http_Response
	 *
	 * @since 2.0.0
	 */
	public function request( Http_Request $request ): Http_Response {
		$params = [
			'method'      => $request->get_method(),
			'timeout'     => 60,
			'httpversion' => '1.1',
			'user-agent'  => $this->build_user_agent(),
			'headers'     => [
				'Content-Type' => 'application/json',
			],
		];

		if ( ! empty( $request->get_body() ) ) {
			$params['body'] = $request->get_body_json();
		}

		if ( ! empty( $request->get_headers() ) ) {
			$params['headers'] = array_merge( $params['headers'], $request->get_headers() );
		}

		$params = apply_filters( 'morning/wc/api_payload', $params, $request );

		Logger::debug( "HTTP Request to `{$request->get_url()}`", [ 'request' => $params ] );

		do_action( 'morning/wc/before_http_request', $request, $params );

		$response = $this->client->request( $request->get_url(), $params );

		do_action( 'morning/wc/after_http_request', $response, $params );

		Logger::debug( "HTTP Response from `{$request->get_url()}`", [ 'response' => $response ] );

		return $this->build_response( $request, $response );
	}


	/**
	 * @param Http_Request $request Http request.
	 * @param WP_Error|array $response Response from the client.
	 *
	 * @return Http_Response
	 *
	 * @since 2.0.0
	 */
	private function build_response( Http_Request $request, $response ): Http_Response {
		return new Http_Response( $request, $response );
	}

	/**
	 * @return string
	 *
	 * @since 2.0.0
	 */
	private function build_user_agent(): string {
		return 'Morning_WC/' . MRN_WC_VERSION . ' (WooCommerce ' . WC()->version . '; WordPress ' . get_bloginfo( 'version' ) . ')';
	}
}
