<?php

/**
 * Simple Machines Forum (SMF) 
 *
 * @package testsuite
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

if (!defined('SMF'))
	die ('Hacking Attempt...');

function TS_AddAction(&$actionArray)
{
	$actionArray['testsuite'] = array('SM-TestSuite.php', 'TS_SMTestSuiteMain');
}

function TS_AddToMenu(&$buttons)
{
	global $txt, $scripturl, $user_info;

	$button['testsuite'] = array(
		'title' => $txt['sm_testsuite'],
		'href' => $scripturl . '?action=testsuite',
		'show' => !$user_info['is_guest'],
		'sub_buttons' => array(
			'admin' => array(
				'title' => $txt['ts_admin'],
				'href' => $scripturl . '?action=testsuite;admin',
				'show' => allowedTo('admin_forum'),
			),
		),
	);
	// Ugh such a mess. We just really don't want this to appear after Logout, so it is kind of needed.
	$buttons = array_merge(array_slice($buttons, 0, 1), $button, array_slice($buttons, 1, count($buttons)));
}

?>