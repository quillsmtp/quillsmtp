<?php
/**
 * Settings_Controller class.
 *
 * @since 1.0.0
 * @package QuillSMTP
 */

namespace QuillSMTP\Mailers\Gmail\REST;

use QuillSMTP\Mailer\Provider\REST\Settings_Controller as Abstract_Settings_Controller;

/**
 * Settings_Controller class.
 *
 * @since 1.0.0
 */
class Settings_Controller extends Abstract_Settings_Controller {

	/**
	 * Retrieves schema, conforming to JSON Schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_schema() {
		$schema = [
			'$schema'              => 'http://json-schema.org/draft-04/schema#',
			'title'                => 'settings',
			'type'                 => 'object',
			'context'              => [ 'view' ],
			'properties'           => [
				'app' => [
					'type'       => 'object',
					'context'    => [ 'view' ],
					'properties' => [
						'client_id'     => [
							'type'     => 'string',
							'required' => true,
							'context'  => [ 'view' ],
						],
						'client_secret' => [
							'type'     => 'string',
							'required' => true,
							'context'  => [ 'view' ],
						],
					],
				],
			],
			'additionalProperties' => [
				'context' => [],
			],
		];

		return rest_default_additional_properties_to_false( $schema );
	}

}
