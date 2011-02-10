<?php

// If we're on the CLI then PHPUnit will already be loaded
if (class_exists('PHPUnit_Util_Filter', FALSE) OR function_exists('phpunit_autoload'))
{
	Kohana_Tests::configure_environment();

	// Stop kohana from processing the request
	define('SUPPRESS_REQUEST', TRUE);
}

Route::set('spriggen', 'spriggen(/<model>)')
	->defaults(array(
		'controller' => 'spriggen',
		'action'     => 'index',
	));
