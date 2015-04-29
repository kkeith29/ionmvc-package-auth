<?php

namespace ionmvc\packages\auth;

$config = [
	'auth' => [
		'enabled'    => true,
		'driver'     => 'db',
		'session_id' => 'auth',
		'db' => [
			'table'   => 'users',
			'columns' => [
				'id'       => 'id',
				'username' => 'username',
				'password' => 'password'
			]
		]
	]
];

?>