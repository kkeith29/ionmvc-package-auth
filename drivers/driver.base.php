<?php

namespace ionmvc\packages\auth\drivers;

use ionmvc\classes\array_func;
use ionmvc\classes\config;
use ionmvc\exceptions\app as app_exception;
use ionmvc\packages\session\classes\session;

class base {

	public $config = [
		'session_id' => 'auth'
	];

	protected $logged_in = false;
	protected $id;
	protected $user = [];
	protected $events = [
		'init'    => [],
		'attempt' => [],
		'login'   => [],
		'logout'  => []
	];

	public function __construct() {
		if ( ( $session_id = config::get('auth.session_id') ) !== false ) {
			$this->config['session_id'] = $session_id;
		}
	}

	public function bind( $event,\Closure $closure ) {
		if ( !isset( $this->events[$event] ) ) {
			throw new app_exception( "Event '%s' does not exist",$event );
		}
		$this->events[$event][] = $closure;
	}

	protected function _has_event( $event ) {
		if ( !isset( $this->events[$event] ) ) {
			throw new app_exception( "Event '%s' does not exist",$event );
		}
		return ( count( $this->events[$event] ) > 0 );
	}

	protected function _trigger() {
		$args = func_get_args();
		$event = array_shift( $args );
		if ( !isset( $this->events[$event] ) ) {
			throw new app_exception( "Event '%s' does not exist",$event );
		}
		$args = [ &$this ] + $args;
		foreach( $this->events[$event] as $closure ) {
			call_user_func_array( $closure,$args );
		}
	}

	public function init() {
		if ( session::is_set( $this->config['session_id'] ) ) {
			$id = session::get( $this->config['session_id'] );
			$this->_trigger('init');
		}
	}

	public function logged_in() {
		return $this->logged_in;
	}

	public function id( $data=null ) {
		if ( !is_null( $data ) ) {
			$this->id = $data;
		}
		return $this->id;
	}

	public function user( $data=null ) {
		if ( !is_null( $data ) ) {
			$this->user = $data;
		}
		return $this->user;
	}

	public function attempt( $user,$pass ) {
		$this->_trigger( 'attempt',$user,$pass );
		return $this->logged_in;
	}

	public function login( $id ) {
		session::set( $this->config['session_id'],$id );
		$this->logged_in = true;
		$this->id = $id;
		$this->_trigger( 'login',$this->id );
	}

	public function logout() {
		if ( !$this->logged_in ) {
			return;
		}
		session::remove( $this->config['session_id'] );
		$this->_trigger( 'logout',$this->id );
	}

}

?>