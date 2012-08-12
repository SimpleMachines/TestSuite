<?php

	if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
		require_once(dirname(__FILE__) . '/SSI.php');
	elseif (!defined('SMF'))
		die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

	global $smcFunc, $sourcedir, $modSettings, $txt;
	
	if (!array_key_exists('db_add_column', $smcFunc))
		db_extend('packages');
	
	// Just to avoid logging errors...
	$txt['sm_testsuite'] = 'Test Suite';
	$txt['ts_admin'] = 'Manage Test Suite';

	add_integration_function('integrate_pre_include', $sourcedir . '/Hooks-SM-TestSuite.php', true);
	add_integration_function('integrate_actions', 'TS_AddAction', true);
	add_integration_function('integrate_menu_buttons', 'TS_AddToMenu', true);
	
	$tables = array(
			'testsuite_permissions' => array(
				'columns' => array(
					array(
						'name' => 'id_group',
						'type' => 'smallint',
						'size' => 5,
						'default' => 0,
					),
					array(
						'name' => 'permission',
						'type' => 'varchar',
						'size' => 30,
						'default' => '',
					),
					array(
						'name' => 'id_suite',
						'type' => 'smallint',
						'size' => 5,
						'default' => 0,
					),
					array(
						'name' => 'id_project',
						'type' => 'smallint',
						'size' => 5,
						'default' => 0,
					),
				),
			),
		);

		foreach ($tables as $table => $data)
		{
			$smcFunc['db_create_table']('{db_prefix}' . $table, $data['columns']);

		}

if (SMF == 'SSI')
	echo 'Database adaptation successful!';

redirectexit($boardurl . '?action=testsuite');
 
?>
