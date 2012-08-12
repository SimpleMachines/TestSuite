<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

global $smcFunc, $sourcedir, $modSettings;

if (!array_key_exists('db_add_column', $smcFunc))
	db_extend('packages');

$tables = array(
	'testsuite_projects' => array(
		'columns' => array(
			array(
				'name' => 'id_project',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'auto' => true,
			),
			array(
				'name' => 'project_name',
				'type' => 'varchar',
				'size' => 255,
				'default' => '',
			),
			array(
				'name' => 'description',
				'type' => 'text',
			),
			array(
				'name' => 'poster_name',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'poster_email',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'id_member',
				'type' => 'mediumint',
				'size' => 8,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
			array(
				'name' => 'poster_time',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => 0,
			),
			array(
				'name' => 'modified_time',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => 0,
			),
			array(
				'name' => 'modified_by',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
		),
		'indexes' => array(
			array(
				'type' => 'primary',
				'columns' => array('id_project'),
			),
		),
	),
	'testsuite_suites' => array(
		'columns' => array(
			array(
				'name' => 'id_suite',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'auto' => true,
			),
			array(
				'name' => 'id_project',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
			array(
				'name' => 'suite_name',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'description',
				'type' => 'text',
			),
			array(
				'name' => 'poster_name',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'poster_email',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'id_member',
				'type' => 'mediumint',
				'size' => 8,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
			array(
				'name' => 'poster_time',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
			array(
				'name' => 'modified_time',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => 0,
			),
			array(
				'name' => 'modified_by',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'count',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
			array(
				'name' => 'fail_count',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
		),
		'indexes' => array(
			array(
				'type' => 'primary',
				'columns' => array('id_suite'),
			),
		),
	),
	'testsuite_cases' => array(
		'columns' => array(
			array(
				'name' => 'id_case',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'auto' => true,
			),
			array(
				'name' => 'id_suite',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
			array(
				'name' => 'case_name',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'description',
				'type' => 'text',
				'null' => false,
			),
			array(
				'name' => 'steps',
				'type' => 'text',
				'null' => false,
			),
			array(
				'name' => 'expected_result',
				'type' => 'text',
				'null' => false,
			),
			array(
				'name' => 'poster_name',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'poster_email',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'id_member',
				'type' => 'mediumint',
				'size' => 8,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
			array(
				'name' => 'poster_time',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
			array(
				'name' => 'id_assigned',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'modified_time',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => 0,
			),
			array(
				'name' => 'modified_by',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'count',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
			array(
				'name' => 'fail_count',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
		),
		'indexes' => array(
			array(
				'type' => 'primary',
				'columns' => array('id_case'),
			),
		),
	),
	'testsuite_runs' => array(
		'columns' => array(
			array(
				'name' => 'id_run',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'auto' => true,
			),
			array(
				'name' => 'id_case',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
			array(
				'name' => 'result_achieved',
				'type' => 'varchar',
				'size' => 255,
				'default' => '',
			),
			array(
				'name' => 'feedback',
				'type' => 'text',
				'null' => false,
			),
			array(
				'name' => 'poster_name',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'poster_email',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
			array(
				'name' => 'id_member',
				'type' => 'mediumint',
				'size' => 8,
				'unsigned' => true,
				'null' => false,
				'default' => '0',
			),
			array(
				'name' => 'poster_time',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => 0,
			),
			array(
				'name' => 'id_bug',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => 0,
			),
			array(
				'name' => 'modified_time',
				'type' => 'int',
				'size' => 10,
				'unsigned' => true,
				'null' => false,
				'default' => 0,
			),
			array(
				'name' => 'modified_by',
				'type' => 'varchar',
				'size' => 255,
				'null' => false,
				'default' => '',
			),
		),
		'indexes' => array(
			array(
				'type' => 'primary',
				'columns' => array('id_run'),
			),
		),
	),
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
			/*array(
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
			),*/
			array(
				'name' => 'id_level',
				'type' => 'int',
				'size' => 10,
				'default' => 0,
			),
			array(
				'name' => 'level_name',
				'type' => 'varchar',
				'size' => 30,
				'default' => '',
			),
		),
		'indexes' => array(
		),
	),
);

foreach ($tables as $table => $data)
{
	$smcFunc['db_create_table']('{db_prefix}' . $table, $data['columns'], $data['indexes']);
}

$column = array(
		'name' => 'id_project',
		'type' => 'int',
		'size' => 10,
		'unsigned' => true,
		'null' => false,
		'default' => 0,
);

$smcFunc['db_add_column']('{db_prefix}members', $column);

/*
$perm_default_perms = array(
	array('id_group' => 2, 'permission' => 'view_all', 'id_suite' => 0, 'id_project' => 0),
);

foreach ($perm_default_perms as $perm)
{
	// Insert the Project.
	$smcFunc['db_insert']('',
		'{db_prefix}testsuite_permissions',
		array(
			'id_group' => 'int', 'permission' => 'string-30', 'id_suite' => 'int', 'id_project' => 'int',
		),
		array(
			$perm['id_group'], $perm['permission'], $perm['id_suite'], $perm['id_project'],
		),
		array('')
	);
}
*/
add_integration_function('integrate_pre_include', $sourcedir . '/Hooks-SM-TestSuite.php', true);
add_integration_function('integrate_actions', 'TS_AddAction', true);
add_integration_function('integrate_menu_buttons', 'TS_AddToMenu', true);

if (SMF == 'SSI')
	echo 'Database adaptation successful!';

?>