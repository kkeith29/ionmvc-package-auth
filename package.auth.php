<?php

namespace ionmvc\packages;

use ionmvc\classes\app;
use ionmvc\classes\request;
use ionmvc\packages\auth\classes\auth as auth_class;

class auth extends \ionmvc\classes\package {

	const version = '1.0.0';
	const class_type_driver = 'ionmvc.auth_driver';

	public function setup() {
		$this->add_type('driver',[
			'type' => self::class_type_driver,
			'type_config' => [
				'file_prefix' => 'driver'
			],
			'path' => 'drivers'
		]);
		app::hook()->attach('request.create',function( $last_call,$request ) {
			if ( $request->mode() !== request::mode_uri ) {
				return;
			}
			auth_class::init();
		});
	}

	public static function package_info() {
		return [
			'author'      => 'Kyle Keith',
			'version'     => self::version,
			'description' => 'Auth handler',
			'require' => [
				'session' => ['1.0.0','>=']
			]
		];
	}

}

?>