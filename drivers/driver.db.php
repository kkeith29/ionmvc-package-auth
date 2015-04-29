<?php

namespace ionmvc\packages\auth\drivers;

use ionmvc\classes\config;
use ionmvc\classes\event;
use ionmvc\classes\package;
use ionmvc\classes\security;
use ionmvc\exceptions\app as app_exception;
use ionmvc\packages\db\classes\db as db_class;

class db extends base {

	public function __construct() {
		parent::__construct();
		if ( !package::loaded('db') ) {
			throw new app_exception('DB package is required to use this driver');
		}
		if ( !$this->_has_event('attempt') ) {
			$this->bind('attempt',function( $auth,$user,$pass ) {
				if ( ( $db_table = config::get('auth.db.table') ) === false ) {
					throw new app_exception('Table is required');
				}
				if ( ( $column = config::get('auth.db.columns') ) === false ) {
					throw new app_exception('Column data is required');
				}
				$query = db_class::table( $db_table )->query('select')->fields( $column['id'],$column['password'] )->where( $column['username'],'=',$user )->limit(1)->execute();
				if ( $query->num_rows() !== 1 ) {
					return;
				}
				list( $id,$hash ) = $query->row();
				if ( !security::validate_password( $pass,$hash ) ) {
					return;
				}
				$auth->login( $id );
			});
		}
	}

}

?>