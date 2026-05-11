<?php
/**
 * Class Base_Notice
 *
 * @package    Morning\WC\Base
 * @subpackage Base_Notice
 * @author     Dor Zuberi <admin@dorzki.io>
 * @link       https://www.dorzki.io
 * @version    2.4.0
 * @since      2.4.0
 */

namespace Morning\WC\Base;

use Morning\WC\Config\Settings;
use Morning\WC\Enum\Notice_Type;

defined( 'ABSPATH' ) || exit;


/**
 * Class Base_Notice
 *
 * @package Morning\WC\Base
 */
abstract class Base_Notice {
	/**
	 * @var Settings
	 *
	 * @since 2.4.0
	 */
	protected Settings $settings;


	/**
	 * Base_Notice constructor.
	 *
	 * @param Settings $settings
	 *
	 * @since 2.4.0
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;

		$this->register_hooks();
	}


	/**
	 * @return void
	 *
	 * @since 2.4.0
	 */
	protected function register_hooks(): void {
		add_filter(
			'morning/wc/registered_notices',
			function ( array $notices ) {
				$notices[] = $this;

				return $notices;
			}
		);
	}


	/**
	 * @return string
	 *
	 * @since 2.4.0
	 */
	abstract public function get_id(): string;

	/**
	 * @return string
	 *
	 * @since 2.4.0
	 */
	abstract public function get_body(): string;

	/**
	 * @return string
	 *
	 * @since 2.4.0
	 */
	public function get_type(): string {
		return Notice_Type::INFO;
	}

	/**
	 * @return bool
	 *
	 * @since 2.4.0
	 */
	public function is_dismissible(): bool {
		return false;
	}

	/**
	 * @return bool
	 *
	 * @since 2.4.0
	 */
	public function should_display(): bool {
		return true;
	}
}
