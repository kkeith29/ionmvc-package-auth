<?php

namespace ionmvc\packages\auth\classes;

use ionmvc\classes\app;
use ionmvc\classes\autoloader;
use ionmvc\classes\config;
use ionmvc\classes\request;
use ionmvc\exceptions\app as app_exception;
use ionmvc\packages\auth as auth_pkg;

class auth {

	private $driver = null;

	public function __construct() {
		if ( ( $driver = config::get('auth.driver') ) === false ) {
			throw new app_exception('No auth driver specified in config');
		}
		$this->driver = autoloader::class_by_type( $driver,auth_pkg::class_type_driver,[
			'instance' => true
		] );
		if ( $this->driver === false ) {
			throw new app_exception( 'Unable to load auth driver: %s',$driver );
		}
	}

	public function driver() {
		return $this->driver;
	}

	public static function __callStatic( $method,$args ) {
		$class = request::auth()->driver();
		if ( !method_exists( $class,$method ) ) {
			throw new app_exception( "Method '%s' not found",$method );
		}
		return call_user_func_array( [ $class,$method ],$args );
	}

}

?>