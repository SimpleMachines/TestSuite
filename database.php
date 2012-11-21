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
            array(
                'name' => 'groups_can_view',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_manage',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_edit',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_delete',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_create',
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
            array(
                'name' => 'groups_can_view',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_manage',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_edit',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_delete',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_create',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
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
            array(
                'name' => 'groups_can_view',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_manage',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_edit',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_delete',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_create',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
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
            array(
                'name' => 'groups_can_view',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_manage',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_edit',
                'type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => '',
            ),
            array(
                'name' => 'groups_can_delete',
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
    'testsuite_global_permissions' => array(
		'columns' => array(
			array(
				'name' => 'permission',
				'type' => 'varchar',
				'size' => 255,
                'null' => false,
				'default' => '',
			),
            array(
				'name' => 'member_groups',
				'type' => 'varchar',
				'size' => 255,
				'default' => '',
			),
		),
        'indexes' => array(),
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


$default_global_perms = array(
	array('permission' => 'create_new_projects', 'member_groups' => ''),
);

foreach ($default_global_perms as $perm)
{
	// Insert the Project.
	$smcFunc['db_insert']('',
		'{db_prefix}testsuite_global_permissions',
		array(
			'permission' => 'string-255', 'member_groups' => 'string-255',
		),
		array(
			$perm['permission'], $perm['member_groups'],
		),
		array('')
	);
}

add_integration_function('integrate_pre_include', $sourcedir . '/Hooks-SM-TestSuite.php', true);
add_integration_function('integrate_actions', 'TS_AddAction', true);
add_integration_function('integrate_menu_buttons', 'TS_AddToMenu', true);

if (SMF == 'SSI')
	echo 'Database adaptation successful!';

?>