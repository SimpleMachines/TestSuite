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

function TS_SMTestSuiteMain()
{
	global $context, $txt, $scripturl, $sourcedir, $settings, $user_info;

	require_once($sourcedir . '/Subs-SM-TestSuite.php');

	$default_action_func = 'TS_ShowProjectList';

	// Call CSS :P, which we have made with utmost love.
	$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] .  '/css/SM-TestSuite.css" />';

	// Load our template & language file.
	loadTemplate('SM-TestSuite');
	loadLanguage('SM-TestSuite');

	if($user_info['is_admin']) {
		$context['TS_can_view_query'] = '1=1';
	} else {
		$context['TS_can_view_query'] = '(FIND_IN_SET(' . implode(', groups_can_view) != 0 OR FIND_IN_SET(', $user_info['groups']) . ', groups_can_view) != 0' . ')';
	}

	$context['test_suite'] = array(
		'current_project' => isset($_REQUEST['project']) ? (int) $_REQUEST['project'] : 0,
		'current_suite' => isset($_REQUEST['suite']) ? (int) $_REQUEST['suite'] : 0,
		'current_case' => isset($_REQUEST['case']) ? (int) $_REQUEST['case'] : 0,
		'current_run' => isset($_REQUEST['run']) ? (int) $_REQUEST['run'] : 0,
		//'perms' => TS_load_permissions(),
		'url' => $scripturl . '?action=testsuite',
		'project_list' => TS_simple_GetProjects(),
		'project_selected' => TS_load_user_Project(),
		'debug' => 2,
	);

	/**
	 * @todo remove this on release
	 */
	if (!empty($context['test_suite']['debug']))
	{
		include_once($sourcedir . '/SM-Debug-TestSuite.php');
		debug_code();
	}
	
	if(!TS_can_do('view_all', 'project')) {
		fatal_lang_error('ts_cannot_permission_generic');
	}

	// Add a link to the Christmas tree!
	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';main',
		'name' => $txt['sm_testsuite_link'],
	);

	$subActions = array(
		// Main views.
		'main' => 'TS_ShowProjectList',
		'project' => 'TS_ShowCurrentProject',
		'suite' => 'TS_ShowCurrentSuite',
		'case' => 'TS_ShowCurrentCase',
		'runsforsuite' => 'TS_RunList',
		// Project related.
		'createproject' => 'TS_Project',
		'createproject2' => 'TS_Project2',
		'editproject' => 'TS_EditProject',
		'editproject2' => 'TS_EditProject2',
		'removeproject' => 'TS_DeleteItem',
		'copyproject' => 'TS_CopyItem',
		// Suite related.
		'editsuite' => 'TS_EditProject',
		'createsuite' => 'TS_Project',
		'removesuite' => 'TS_DeleteItem',
		'copysuite' => 'TS_CopyItem',
		// Case related.
		'createcase' => 'TS_Cases',
		'createcase2' => 'TS_Cases2',
		'editcase' => 'TS_EditCase',
		'editcase2' => 'TS_EditCase2',
		'removecase' => 'TS_DeleteItem',
		'copycase' => 'TS_CopyItem',
		// Run related.
 		'createrun' => 'TS_PostRun',
 		'createrun2' => 'TS_PostRun2',
		'editrun' => 'TS_EditRun',
		'editrun2' => 'TS_EditRun2',
		'removerun' => 'TS_DeleteItem',
		// Management.
		'copyitem2' => 'TS_CopyItem2',
		'admin' => 'TS_Admin',
		'adminperlevel' => 'TS_Admin_PerLevel',
		'updateproject' => 'TS_UpdateDefaultProject',
		'updatepermissions' => 'TS_Admin_UpdatePermissions',
	);

	foreach ($subActions as $key => $action)
	{
		if (isset($_REQUEST[$key]))
		{
			if (function_exists($subActions[$key]))
			{
				return $subActions[$key]();
			}
		}
	}

	// At this point we can just do our default.
	$default_action_func();
}

/**
 * The setup function for showing the project list.
 */
function TS_ShowProjectList()
{
	global $context, $sourcedir, $txt;
	// Define a page title...
	$context['page_title'] = $txt['sm_testsuite'];

	// Show off our projects how about it?
	require_once($sourcedir . '/Subs-SM-TestSuite.php');
	$context['test_suite']['projects'] = TS_requestProjects();
	$context['test_suite']['buttons'] = array(
		array(
			'href' => $context['test_suite']['url'] . ';createproject',
			'name' => $txt['ts_create_project'],
		),
	);
}

/**
 * The setup function for showing the selected project.
 */
function TS_ShowCurrentProject()
{
	global $scripturl, $context, $sourcedir, $txt, $settings;

	// Load our info! It's trustworthy, trust us.
	require_once($sourcedir . '/Subs-SM-TestSuite.php');

	// If the project is invalid, the function called will take care of it.
	$context['test_suite']['project'] = TS_loadProject($context['test_suite']['current_project']);

	$context['test_suite']['buttons'] = array();
	if($context['test_suite']['project']['groups_can_create']) {
		$context['test_suite']['buttons'] = array(
			array(
				'href' => $context['test_suite']['url'] . ';createsuite;proj=' . $context['test_suite']['current_project'] . '',
				'name' => $txt['ts_create_suite'],
			)
		);
	}

	// While it is good to separate template code from source, occassionly it is good be able to simplify the template code
	//and let the template designer know that there is a generic variable they can test for that the developer may add in the future
	//allowing no additional template changes to be made.
	$context['test_suite']['edit_link'] = '<a class="smalltext" href="' . $context['test_suite']['url'] . ';editproject=' . $context['test_suite']['current_project'] . '">[' . $txt['ts_edit'] . ']</a>';

	$context['sub_template'] = 'sm_testsuite_project_view';

	// Make a note of Project ID and build a linktree for Project
	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';project=' . $context['test_suite']['current_project'],
		'name' => $context['test_suite']['project']['name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? 
			' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_project'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ?
			' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);
	$context['page_title'] = $context['test_suite']['project']['name'];
}

/**
 * The setup function for showing the selected suite.
 */
function TS_ShowCurrentSuite()
{
	global $scripturl, $context, $sourcedir, $txt, $settings;

	// The heart and gold of our magic.
	require_once($sourcedir . '/Subs-SM-TestSuite.php');

	// If the suite is invalid, the function called will take care of it.
	$context['test_suite']['suite'] = TS_loadSuite($context['test_suite']['current_suite']);

	$context['sub_template'] = 'sm_testsuite_case_view';
	$context['page_title'] = $context['test_suite']['suite']['name'];

	// Add the project the suite belongs to to the linktree.
	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';project=' . $context['test_suite']['suite']['id_project'],
		'name' => $context['test_suite']['suite']['project_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? 
			' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_project'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ?
			' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);
	// Add the current suite to the linktree.
	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';suite=' . $context['test_suite']['current_suite'],
		'name' => $context['test_suite']['suite']['name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? 
			' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_suite'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ?
			' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);
	$context['test_suite']['edit_link'] = '<a class="smalltext" href="' . $context['test_suite']['url'] . ';editsuite=' . $context['test_suite']['current_suite'] . '">[' . $txt['ts_edit'] . ']</a>';

	$context['test_suite']['buttons'] = array();
	if($context['test_suite']['suite']['groups_can_create']) {
		$context['test_suite']['buttons'] = array(
			array(
				'href' => $context['test_suite']['url'] . ';createcase;s=' . $context['test_suite']['current_suite'],
				'name' => $txt['ts_create_case'],
			)
		);
	}
}

/**
 * The setup function for showing the selected case.
 */
function TS_ShowCurrentCase()
{
	global $scripturl, $context, $sourcedir, $txt, $settings;

	// Load the case for the template to have its way with.
	require_once($sourcedir . '/Subs-SM-TestSuite.php');
	
	// If the case is invalid, the calling function takes care of it.
	$context['test_suite']['case'] = TS_loadCase($context['test_suite']['current_case']);
	$context['page_title'] = $context['test_suite']['case']['name'];

	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';project=' . $context['test_suite']['case']['id_project'],
		'name' => $context['test_suite']['case']['project_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? 
			' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_project'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ?
			' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'

	);
	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';suite=' . $context['test_suite']['case']['id_suite'],
		'name' => $context['test_suite']['case']['suite_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? 
			' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_suite'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ?
			' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);
	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';case=' . $context['test_suite']['current_case'],
		'name' => $context['test_suite']['case']['name'],
		'name' => $context['test_suite']['case']['name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? 
			' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_case'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ?
			' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);

	if($context['test_suite']['case']['groups_can_create']) {
		$context['test_suite']['buttons'] = array(
			array(
				'href' => $context['test_suite']['url'] . ';createrun;c=' . $context['test_suite']['current_case'],
				'name' => $txt['ts_submit_run_result'],
			),
		);
	}

	$context['test_suite']['edit_link'] = '<a class="smalltext" href="' . $context['test_suite']['url'] . ';editcase=' . $context['test_suite']['current_case'] . '">[' . $txt['ts_edit'] . ']</a>';
	$context['sub_template'] = 'sm_testsuite_separate_case_view';
}

/**
 * The setup function for showing the runs of the currently selected suite.
 */
function TS_RunList()
{
	global $scripturl, $context, $txt, $sourcedir;

	$context['test_suite']['current_suite'] = isset($_REQUEST['runsforsuite']) ? (int) $_REQUEST['runsforsuite'] : 0;
	$context['test_suite']['result'] = isset($_REQUEST['result']) ? strtolower($_REQUEST['result']) : '';

	require_once($sourcedir . '/Subs-SM-TestSuite.php');
	
	// Runs for the suite.
	$context['test_suite']['run_links'] = TS_requestRunsforSuites();

	// Details about the suite.
	$context['test_suite']['suite'] = TS_loadSuite($context['test_suite']['current_suite']);

	$context['page_title'] = $txt['ts_runs_for_suites'];
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=testsuite;project=' . $context['test_suite']['suite']['id_project'],
		'name' => $context['test_suite']['suite']['project_name'],
	);
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=testsuite;runsforsuite=' . $context['test_suite']['suite']['id_suite'],
		'name' => $txt['ts_runs_for_suite'],
	);
	$context['sub_template'] = 'sm_testsuite_count_link';
}

//creation of world (Projects and Suites):P
function TS_Project()
{
	global $txt, $scripturl, $modSettings, $user_info, $context, $sourcedir, $smcFunc, $settings;

	// Get Project ID For suites
	$context['test_suite']['current_project'] = isset($_REQUEST['proj']) ? (int) $_REQUEST['proj'] : 0;

	if (empty($context['post_errors']))
	{
		$context['post_errors'] = array();
	}

	TS_Validator('project');
	
	// Set the destination action for submission.
	$context['destination'] = 'createProject2';
	$context['submit_label'] = $txt['ts_create'];

	if (empty($context['test_suite']['current_project']))
	{
		$context['page_title'] = $txt['ts_start_new_project'];
		if (!TS_can_do('manage_all')) 
		{
			fatal_lang_error('ts_cannot_permission_generic');
		}
	}

	else
	{
		$context['page_title'] = $txt['ts_start_new_suite'];
		if (!TS_can_do('manage_all') || !TS_can_do('manage_project', $context['test_suite']['current_project'])) 
		{
			fatal_lang_error('ts_cannot_permission_generic');
		}
	}

	// Call Our Sub-Template.
	$context['sub_template'] = 'create_project';
	
	// Add a link to the Christmas tree!
	if (empty($context['test_suite']['current_project']))
	{
		$context['linktree'][] = array(
			'name' => $txt['ts_create_project'],
		);
	}
	
	else
	{
		// Unfortunately a call needs to be made to loadProject just to get the project name to show on the linktree.
		$project = TS_loadProject($context['test_suite']['current_project'], false);
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=testsuite;project='. $context['test_suite']['current_project'],
			'name' => $project['name'],
			'extra_before' => '<span' . ($settings['linktree_inline'] ? 
				' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_project'] . ' ( </strong></span>',
			'extra_after' => '<span' . ($settings['linktree_inline'] ?
				' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
		);

		$context['linktree'][] = array(
			'url' => $scripturl . '?action=testsuite;createsuite;proj='. $context['test_suite']['current_project'],
			'name' => $context['page_title'],
		);
	}
}

function TS_Project2()
{
	global $txt, $modSettings, $sourcedir, $context, $scripturl, $user_info, $smcFunc;

	isAllowedTo('ts_post_projects');
	
	require_once($sourcedir . '/Subs-SM-TestSuite.php');

	// Sneaking off, are we?
	if (empty($_POST))
	{
		redirectexit($scripturl);
	}

	// Get Project ID For suites
	$context['test_suite']['current_project'] = isset($_REQUEST['proj']) ? (int) $_REQUEST['proj'] : 0; 
	$project_id = $context['test_suite']['current_project'];

	// Prevent double submission of this form.
	checkSubmitOnce('check');

	// No errors as yet.
	$post_errors = array();

	// If the session has timed out, let the user re-submit their form.
	if (checkSession('project', '', false) != '')
	{
		$post_errors[] = 'session_timeout';
	}

	// Check the name and description coming.
	if (!isset($_POST['name']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['name'])) === '')
	{
		$post_errors[] = 'no_name';
	}
	if (!isset($_POST['description']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['description']), ENT_QUOTES) === '')
	{
		$post_errors[] = 'no_description';
	}
	elseif (!empty($modSettings['max_messageLength']) && $smcFunc['strlen']($_POST['description']) > $modSettings['max_messageLength'])
	{
		$post_errors[] = 'long_description';
	}
	else
	{
		// Prepare the description a bit for some additional testing.
		$_POST['description'] = $smcFunc['htmlspecialchars']($_POST['description'], ENT_QUOTES);
	}

	// Any mistakes?
	if (!empty($post_errors))
	{
		loadLanguage('Errors');
		
		$context['post_error'] = array('description' => array());
		foreach ($post_errors as $post_error)
		{
			$context['post_error'][$post_error] = true;
			if ($post_error == 'long_description')
			{
				$txt['ts_error_' . $post_error] = sprintf($txt['ts_error_' . $post_error], $modSettings['max_messageLength']);
			}

			$context['post_error']['description'][] = $txt['ts_error_' . $post_error];
		}

		return TS_Project();
	}

	// At about this point, we're creating and that's that.
	ignore_user_abort(true);
	@set_time_limit(300);

	// Add special html entities to the Project Name, username and email.
	$_POST['name'] = strtr($smcFunc['htmlspecialchars']($_POST['name']), array("\r" => '', "\n" => '', "\t" => ''));
	$poster_name = htmlspecialchars($user_info['username']);
	$poster_email = htmlspecialchars($user_info['email']);

	// At this point, we want to make sure the Project Name isn't too long.
	if ($smcFunc['strlen']($_POST['name']) > 100)
		$_POST['name'] = $smcFunc['substr']($_POST['name'], 0, 100);

	// Collect all parameters for the creation of Project.
	if (empty($project_id))
	{
		$projectOption = array(
			'id' => empty($project_id) ? 0 : $project_id,
			'project_name' => $_POST['name'],
			'description' => $_POST['description'],
		);
		$posterOptions = array(
			'id' => $user_info['id'],
			'name' => $poster_name,
			'email' => $poster_email,
		);

		TS_createProject($projectOption, $posterOptions);

		// Dut-dut-duh-duh-DUH-duh-dut-duh-duh!  *dances to the Final Fantasy Fanfare...*
		redirectexit('action=testsuite');
	}
	// Collect all parameters for the creation of Suite.
	elseif (!empty($project_id))
	{
		$suiteOptions = array(
			'id' => empty($suite_id) ? 0 : $suite_id,
			'id_project' => empty($project_id) ? 0 : $project_id,
			'suite_name' => $_POST['name'],
			'description' => $_POST['description'],
		);
		$posterOptions = array(
			'id' => $user_info['id'],
			'name' => $poster_name,
			'email' => $poster_email,
		);

		TS_createSuite($suiteOptions, $posterOptions);

		// Dut-dut-duh-duh-DUH-duh-dut-duh-duh!  *dances to the Final Fantasy Fanfare...*
		redirectexit('action=testsuite;project=' . $project_id);
	}
}

function TS_EditProject()
{
	global $txt, $scripturl, $modSettings, $user_info, $context, $sourcedir, $smcFunc, $settings;

	/*
	 * @todo permissions
	 */

	if (empty($context['test_suite']['current_project']))
	{
		$context['test_suite']['current_project'] = isset($_REQUEST['editproject']) ? (int) $_REQUEST['editproject'] : 0;
	}

	if (empty($context['test_suite']['current_project']))
	{
		$project_id = isset($_REQUEST['proj']) ? (int) $_REQUEST['proj'] : 0;
	}
	else
	{
		$project_id = $context['test_suite']['current_project'];
	}
	
	if (empty($context['test_suite']['current_suite']))
	{
		$context['test_suite']['current_suite'] = isset($_REQUEST['editsuite']) ? (int) $_REQUEST['editsuite'] : 0;
	}

	if (empty($context['test_suite']['current_suite']))
	{
		$suite_id = isset($_REQUEST['s']) ? (int) $_REQUEST['s'] : 0;
	}
	else
	{
		$suite_id = $context['test_suite']['current_suite'];
	}
	
	if (empty($project_id) && empty($suite_id))
	{
		return false;
	}

	require_once($sourcedir . '/Subs-SM-TestSuite.php');

	// Get data for this specific suite.
	if (!empty($suite_id))
	{
		$context['test_suite']['suite'] = TS_loadSuite($suite_id, false);
		$context['name'] = $context['test_suite']['suite']['name'];
		$context['description'] = $context['test_suite']['suite']['description'];
		$context['test_suite']['current_project'] = $context['test_suite']['suite']['id_project'];
	}

	else
	{
		$context['test_suite']['project'] = TS_loadProject($context['test_suite']['current_project'], false);
		$context['name'] = $context['test_suite']['project']['name'];
		$context['description'] = $context['test_suite']['project']['description'];
	}
	
	if (empty($context['post_errors']))
	{
		$context['post_errors'] = array();
	}

	TS_Validator('project');

	// Set the destination action for submission.
	$context['destination'] = 'editproject2';
	$context['submit_label'] = $txt['ts_submit'];

	// Call Our Sub-Template.
	$context['sub_template'] = 'edit_project';

	if (!empty($suite_id))
	{
		$context['page_title'] = $txt['ts_edit'] . ' '. $txt['ts_suite'];
	}
	else
	{
		$context['page_title'] = $txt['ts_edit'] . ' '. $txt['ts_project'];
	}

	if (!empty($suite_id))
	{
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=testsuite;project='. $context['test_suite']['current_project'],
			'name' => $context['test_suite']['suite']['project_name'],
			'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_project'] . ' ( </strong></span>',
			'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
		);

		$context['linktree'][] = array(
			'url' => $context['test_suite']['url'] . ';suite='. $suite_id,
			'name' => $context['test_suite']['suite']['name'],
			'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $context['page_title'] . ' ( </strong></span>',
			'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
		);
	}

	else
	{
		$context['linktree'][] = array(
			'url' => $context['test_suite']['url'] . ';project=' . $project_id,
			'name' => $context['test_suite']['project']['name'],
			'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $context['page_title'] . ' ( </strong></span>',
			'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
		);
	}
}

function TS_EditProject2()
{
	global $txt, $modSettings, $sourcedir, $context, $scripturl, $user_info, $smcFunc;

	require_once($sourcedir . '/Subs-SM-TestSuite.php');

	/*
	 * @todo permissions
	 */

	$project_id = $context['test_suite']['current_project'];
	if (empty($project_id))
	{
	    $project_id = isset($_REQUEST['proj']) ? (int) $_REQUEST['proj'] : 0;
	}

	$suite_id = $context['test_suite']['current_suite'];
	if (empty($suite_id))
	{
		$suite_id = isset($_REQUEST['s']) ? (int) $_REQUEST['s'] : 0;
	}

	// Prevent double submission of this form.
	checkSubmitOnce('check');

	// No errors as yet.
	$post_errors = array();

	// If the session has timed out, let the user re-submit their form.
	if (checkSession('editproject', '', false) != '')
	{
		$post_errors[] = 'session_timeout';
	}

	// Get data for this specific suite.
	if (!empty($suite_id))
	{
		$context['test_suite']['suite'] = TS_loadSuite($suite_id, false);
	}

	// Get data for the project to be edited.
	else
	{
		$context['test_suite']['project'] = TS_loadProject($project_id, false);
	}

	// Check the name and description coming.
	if (!isset($_POST['name']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['name'])) === '')
		$post_errors[] = 'no_name';
	if (!isset($_POST['description']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['description']), ENT_QUOTES) === '')
		$post_errors[] = 'no_description';
	elseif (!empty($modSettings['max_messageLength']) && $smcFunc['strlen']($_POST['description']) > $modSettings['max_messageLength'])
		$post_errors[] = 'long_description';
	else
	{
		// Prepare the description a bit for some additional testing.
		$_POST['description'] = $smcFunc['htmlspecialchars']($_POST['description'], ENT_QUOTES);
	}

	// Any mistakes?
	if (!empty($post_errors))
	{
		loadLanguage('Errors');
		
		$context['post_error'] = array('description' => array());
		foreach ($post_errors as $post_error)
		{
			$context['post_error'][$post_error] = true;
			if ($post_error == 'long_description')
				$txt['ts_error_' . $post_error] = sprintf($txt['ts_error_' . $post_error], $modSettings['max_messageLength']);

			$context['post_error']['description'][] = $txt['ts_error_' . $post_error];
		}
		$context['test_suite']['current_project'] = $project_id;
		$context['test_suite']['current_suite'] = $suite_id;
		return TS_EditProject();
	}

	// At about this point, we're creating and that's that.
	ignore_user_abort(true);
	@set_time_limit(300);

	// Add special html entities to the Project Name, username and email.
	$_POST['name'] = strtr($smcFunc['htmlspecialchars']($_POST['name']), array("\r" => '', "\n" => '', "\t" => ''));
	$poster_name = htmlspecialchars($user_info['username']);
	$poster_email = htmlspecialchars($user_info['email']);

	// At this point, we want to make sure the Project Name isn't too long.
	if ($smcFunc['strlen']($_POST['name']) > 100)
	{
		$_POST['name'] = $smcFunc['substr']($_POST['name'], 0, 100);
	}

	if (!empty($suite_id))
	{
		$suiteOptions = array(
			'id' => empty($suite_id) ? 0 : $suite_id,
			'id_project' => empty($project_id) ? 0 : $project_id,
			'suite_name' => $_POST['name'],
			'description' => $_POST['description'],
		);
		$posterOptions = array();

		// Only consider marking as editing if they have edited the subject, message or icon.
		if ((isset($_POST['name']) && $_POST['name'] != $context['test_suite']['suite']['name']) || (isset($_POST['description']) && $_POST['description'] != $context['test_suite']['suite']['description']))
		{
				$suiteOptions['modify_time'] = time();
				$suiteOptions['modified_by'] = $user_info['name'];
		}
		TS_modifySuite($suiteOptions, $posterOptions);

		// If we didn't change anything this time but had before put back the old info.
		if (!isset($suiteOptions['modify_time']) && !empty($context['test_suite']['project']['modified_time']))
		{
			$suiteOptions['modify_time'] = $context['test_suite']['project']['modified_time'];
			$suiteOptions['modified_by'] = $context['test_suite']['project']['modified_by'];
		}
		redirectexit('action=testsuite;project='. $project_id);
	}

	elseif (!empty($project_id))
	{
		$projectOptions = array(
			'id' => empty($project_id) ? 0 : $project_id,
			'project_name' => $_POST['name'],
			'description' => $_POST['description'],
		);
		$posterOptions = array();

		// Only consider marking as editing if they have edited the subject, message or icon.
		if ((isset($_POST['name']) && $_POST['name'] != $context['test_suite']['project']['name']) || (isset($_POST['description']) && $_POST['description'] != $context['test_suite']['project']['description']))
		{
				$projectOptions['modify_time'] = time();
				$projectOptions['modified_by'] = $user_info['name'];
		}
		TS_modifyProject($projectOptions, $posterOptions);

		// If we didn't change anything this time but had before put back the old info.
		if (!isset($projectOptions['modify_time']) && !empty($context['test_suite']['project']['modified_time']))
		{
			$projectOptions['modify_time'] = $context['test_suite']['project']['modified_time'];
			$projectOptions['modified_by'] = $context['test_suite']['project']['modified_by'];
		}
		redirectexit('action=testsuite');
	}
}

// Ok complete new functions to create Test Cases and Runs
function TS_Cases()
{
	global $txt, $scripturl, $modSettings, $user_info, $context, $sourcedir, $smcFunc, $settings;

	if (empty($context['post_errors']))
	{
		$context['post_errors'] = array();
	}

	$context['test_suite']['current_suite'] = isset($_REQUEST['s']) ? (int) $_REQUEST['s'] : 0;
	$suite_id = $context['test_suite']['current_suite'];
	$context['test_suite']['suite'] = TS_loadSuite($suite_id, false);

	// Permissions...
	if (!TS_can_do('manage_all') || !TS_can_do('manage_project', $context['test_suite']['current_project']) || !TS_can_do('manage_suite', $suite_id))
	{
		fatal_lang_error('ts_cannot_permission_generic');
	}
	
	TS_Validator('case');

	// Set the destination action for submission.
	$context['destination'] = 'createCase2';
	$context['submit_label'] = $txt['ts_create'];

	// Create page titles and linktrees
	$context['page_title'] = $txt['ts_start_new_case'];

	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';project='. $context['test_suite']['suite']['id_project'],
		'name' => $context['test_suite']['suite']['project_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_project'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);

	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';suite='. $suite_id,
		'name' => $context['test_suite']['suite']['name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_suite'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);

	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';createcase;s='. $suite_id,
		'name' => $context['page_title'],
	);

	// Make some more effort for BBC Codes used in Test Cases
	$context['use_smileys'] = true;
	$context['sub_template'] = 'create_case';

	require_once($sourcedir . '/Subs-Editor.php');

	// Now create the editor for description.
	$editorOptions = array(
		'id' => 'message',
		'value' => isset($context['message']) ? $context['message'] : '',
		'height' => '150px',
		'width' => '80%',
		'disable_smiley_box' => true,
	);
	create_control_richedit($editorOptions);

	// For steps.
	$editorOptions = array(
		'id' => 'steps',
		'value' => isset($context['steps']) ? $context['steps'] : '',
		'height' => '150px',
		'width' => '80%',
		'disable_smiley_box' => true,
	);
	create_control_richedit($editorOptions);

	// For Expected Result.
	$editorOptions = array(
		'id' => 'expected_result',
		'value' => isset($context['expected_result']) ? $context['expected_result'] : '',
		'height' => '150px',
		'width' => '80%',
		'disable_smiley_box' => true,
	);
	create_control_richedit($editorOptions);
}

function TS_Cases2()
{
	global $txt, $modSettings, $sourcedir, $context, $scripturl, $user_info, $smcFunc;

	require_once($sourcedir . '/Subs-Post.php');

	$suite_id = isset($_REQUEST['s']) ? (int) $_REQUEST['s'] : 0;
	$id_case = isset($_REQUEST['c']) ? (int) $_REQUEST['c'] : 0;

	// Prevent double submission of this form.
	checkSubmitOnce('check');

	// No errors as yet.
	$post_errors = array();

	// If the session has timed out, let the user re-submit their form.
	if (checkSession('cases', '', false) != '')
	{
		$post_errors[] = 'session_timeout';
	}

	// Check the name and message and steps for Cases.
	if (!isset($_POST['name']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['name'])) === '')
		$post_errors[] = 'no_name';
	if (!isset($_POST['message']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['message']), ENT_QUOTES) === '')
		$post_errors[] = 'no_message';
	if (!isset($_POST['steps']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['steps']), ENT_QUOTES) === '')
		$post_errors[] = 'no_steps';
	if (!isset($_POST['expected_result']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['expected_result']), ENT_QUOTES) === '')
		$post_errors[] = 'no_expected_result';
	if (!empty($modSettings['max_messageLength']) && ($smcFunc['strlen']($_REQUEST['message']) > $modSettings['max_messageLength'] || $smcFunc['strlen']($_REQUEST['steps']) > $modSettings['max_messageLength'] || $smcFunc['strlen']($_REQUEST['expected_result']) > $modSettings['max_messageLength']))
		$post_errors[] = 'long_message';
	else
	{
		// Prepare the message and steps a bit for some additional testing.
		$_POST['message'] = $smcFunc['htmlspecialchars']($_POST['message'], ENT_QUOTES);
		$_POST['steps'] = $smcFunc['htmlspecialchars']($_POST['steps'], ENT_QUOTES);
		$_POST['expected_result'] = $smcFunc['htmlspecialchars']($_POST['expected_result'], ENT_QUOTES);
		
		// Let's see if there's still some content left without the tags.
		if ($smcFunc['htmltrim'](strip_tags(parse_bbc($_POST['message'], false), '<img>')) === '' && (!allowedTo('admin_forum') || strpos($_POST['message'], '[html]') === false))
			$post_errors[] = 'no_message';
		if ($smcFunc['htmltrim'](strip_tags(parse_bbc($_POST['steps'], false), '<img>')) === '' && (!allowedTo('admin_forum') || strpos($_POST['steps'], '[html]') === false))
			$post_errors[] = 'no_steps';
		if ($smcFunc['htmltrim'](strip_tags(parse_bbc($_POST['expected_result'], false), '<img>')) === '' && (!allowedTo('admin_forum') || strpos($_POST['expected_result'], '[html]') === false))
			$post_errors[] = 'no_expected_result';
	}

	if (isset($_POST['id_assigned_list']) && is_array($_POST['id_assigned_list']))
	{
		$id_assigned = array();
		foreach ($_POST['id_assigned_list'] as $assigned)
			$id_assigned[(int) $assigned] = (int) $assigned;

		$id_assigned = implode(',' , $id_assigned);
	}

	// Any mistakes?
	if (!empty($post_errors))
	{
		loadLanguage('Errors');
		
		//Check message from cases
		$context['post_error'] = array('message' => array());
		foreach ($post_errors as $post_error)
		{
			$context['post_error'][$post_error] = true;
			if ($post_error == 'long_message')
				$txt['ts_error_' . $post_error] = sprintf($txt['ts_error_' . $post_error], $modSettings['max_messageLength']);

			$context['post_error']['message'][] = $txt['ts_error_' . $post_error];
		}
		return TS_Cases();
	}

	// At about this point, we're posting and that's that.
	ignore_user_abort(true);
	@set_time_limit(300);

	// Add special html entities to the Test Case Name, username, email and expected result.
	$_POST['name'] = strtr($smcFunc['htmlspecialchars']($_POST['name']), array("\r" => '', "\n" => '', "\t" => ''));
	$poster_name = htmlspecialchars($user_info['username']);
	$poster_email = htmlspecialchars($user_info['email']);

	// At this point, we want to make sure the name isn't too long.
	if ($smcFunc['strlen']($_POST['name']) > 100)
	{
		$_POST['name'] = $smcFunc['substr']($_POST['name'], 0, 100);
	}
	
	// Collect all parameters for the creation of Test Cases.
	$caseOption = array(
		'id' => empty($case_id) ? 0 : $case_id,
		'id_suite' => empty($suite_id) ? 0 : $suite_id,
		'case_name' => $_POST['name'],
		'description' => $_POST['message'],
		'steps' => $_POST['steps'],
		'expected_result' => $_POST['expected_result'],
	);
	$posterOptions = array(
		'id' => $user_info['id'],
		'name' => $poster_name,
		'email' => $poster_email,
		'id_assigned' =>  empty($id_assigned) ? 0 : $id_assigned,
	);
	TS_createCase($caseOption, $posterOptions);

	// Dut-dut-duh-duh-DUH-duh-dut-duh-duh!  *dances to the Final Fantasy Fanfare...*
	redirectexit('action=testsuite;suite=' . $suite_id);
}

function TS_EditCase()
{
	global $txt, $scripturl, $modSettings, $user_info, $context, $sourcedir, $smcFunc, $settings;

	if (empty($context['test_suite']['current_case']))
	{
		$context['test_suite']['current_case'] = isset($_REQUEST['editcase']) ? (int) $_REQUEST['editcase'] : 0;
	}

	if (empty($context['test_suite']['current_case']))
	{
		$case_id = isset($_REQUEST['c']) ? (int) $_REQUEST['c'] : 0;
	}
	else
	{
		$case_id = $context['test_suite']['current_case'];
	}

	if (empty($case_id))
	{
		return false;
	}

	// Get data for this specific case. 	
	$context['test_suite']['case'] = TS_loadCase($context['test_suite']['current_case'], false);
	
	$context['test_suite']['current_suite'] = $context['test_suite']['case']['id_suite'];
	$context['test_suite']['current_project'] = $context['test_suite']['case']['id_project'];
	$context['name'] = $context['test_suite']['case']['name'];
	$context['message'] = $context['test_suite']['case']['description'];
	$context['steps'] = $context['test_suite']['case']['steps'];
	$context['expected_result'] = $context['test_suite']['case']['expected_result'];
	$context['id_assigned'] = $context['test_suite']['case']['id_assigned'];

	$names = array();
	if (!empty($context['test_suite']['case']['id_assigned'])) {
		foreach ($context['test_suite']['case']['id_assigned'] as $member)
			$names[] = $member['name'];
	}
	$context['id_assigned_list'] = empty($names) ? '' : '&quot;' . implode('&quot;, &quot;', $names) . '&quot;';

	if (!($context['user']['is_admin'] || TS_can_do('manage_all') || TS_can_do('manage_project', $context['test_suite']['current_project']) || 
			TS_can_do('manage_suite', $context['test_suite']['current_suite'])))
	{
		fatal_lang_error('ts_cannot_permission_generic');
	}
		
	if (empty($context['post_errors']))
	{
		$context['post_errors'] = array();
	}
		
	TS_Validator('case');

	// Set the destination action for submission.
	$context['destination'] = 'editcase2';
	$context['submit_label'] = $txt['ts_submit'];

	$context['page_title'] = $txt['ts_edit'] . ' '. $txt['ts_case'];

	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';project=' . $context['test_suite']['current_project'],
		'name' => $context['test_suite']['case']['project_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_project'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);

	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';suite=' . $context['test_suite']['current_suite'],
		'name' => $context['test_suite']['case']['suite_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_suite'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);

	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';case=' . $case_id,
		'name' => $context['name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $context['page_title'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);

	// Call Our Sub-Template.
	$context['sub_template'] = 'edit_case';
	
	// Make some more effort for BBC Codes used in Test Cases
	$context['use_smileys'] = true;

	require_once($sourcedir . '/Subs-Editor.php');

	// Now create the editor.
	$editorOptions = array(
		'id' => 'message',
		'value' => $context['message'],
		'height' => '150px',
		'width' => '80%',
	);
	create_control_richedit($editorOptions);
	
	// Now create the editor.
	$editorOptions = array(
		'id' => 'steps',
		'value' => $context['steps'],
		'height' => '150px',
		'width' => '80%',
	);
	create_control_richedit($editorOptions);

	// Now create the editor.
	$editorOptions = array(
		'id' => 'expected_result',
		'value' => $context['expected_result'],
		'height' => '150px',
		'width' => '80%',
	);
	create_control_richedit($editorOptions);
}

function TS_EditCase2()
{
	global $txt, $modSettings, $sourcedir, $context, $scripturl, $user_info, $smcFunc;

	require_once($sourcedir . '/Subs-Post.php');

	$case_id = $context['test_suite']['current_case'];
	if (empty ($case_id))
		$case_id = isset($_REQUEST['c']) ? (int) $_REQUEST['c'] : 0;

	// Prevent double submission of this form.
	checkSubmitOnce('check');

	// No errors as yet.
	$post_errors = array();

	// If the session has timed out, let the user re-submit their form.
	if (checkSession('editcase', '', false) != '')
		$post_errors[] = 'session_timeout';

	// !!! Incomplete, TBD.
	if (!TS_can_do('manage_all'))
	{
		fatal_lang_error('ts_cannot_permission_generic');
	}

	if (!empty($case_id))
	{
		$context['test_suite']['case'] = TS_loadCase($case_id, false);
	}

	// Check the name and message and steps for Cases.
	if (!isset($_POST['name']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['name'])) === '')
		$post_errors[] = 'no_name';
	if (!isset($_POST['message']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['message']), ENT_QUOTES) === '')
		$post_errors[] = 'no_message';
	if (!empty($modSettings['max_messageLength']) && $smcFunc['strlen']($_POST['message']) > $modSettings['max_messageLength'])
		$post_errors[] = 'long_message';
	if (!isset($_POST['steps']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['steps']), ENT_QUOTES) === '')
		$post_errors[] = 'no_steps';
	if (!empty($modSettings['max_messageLength']) && $smcFunc['strlen']($_POST['steps']) > $modSettings['max_messageLength'])
		$post_errors[] = 'long_message';
	if (!isset($_POST['expected_result']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['expected_result']), ENT_QUOTES) === '')
		$post_errors[] = 'no_expected_result';
	if (!empty($modSettings['max_messageLength']) && $smcFunc['strlen']($_POST['expected_result']) > $modSettings['max_messageLength'])
		$post_errors[] = 'long_message';
	else
	{
		// Prepare the message and steps a bit for some additional testing.
		$_POST['message'] = $smcFunc['htmlspecialchars']($_POST['message'], ENT_QUOTES);
		$_POST['steps'] = $smcFunc['htmlspecialchars']($_POST['steps'], ENT_QUOTES);
		$_POST['expected_result'] = $smcFunc['htmlspecialchars']($_POST['expected_result'], ENT_QUOTES);
		
		// Let's see if there's still some content left without the tags.
		if ($smcFunc['htmltrim'](strip_tags(parse_bbc($_POST['message'], false), '<img>')) === '' && (!allowedTo('admin_forum') || strpos($_POST['message'], '[html]') === false))
			$post_errors[] = 'no_message';
		if ($smcFunc['htmltrim'](strip_tags(parse_bbc($_POST['steps'], false), '<img>')) === '' && (!allowedTo('admin_forum') || strpos($_POST['steps'], '[html]') === false))
			$post_errors[] = 'no_steps';
		if ($smcFunc['htmltrim'](strip_tags(parse_bbc($_POST['expected_result'], false), '<img>')) === '' && (!allowedTo('admin_forum') || strpos($_POST['expected_result'], '[html]') === false))
			$post_errors[] = 'no_expected_result';
	}

	if (isset($_POST['id_assigned_list']) && is_array($_POST['id_assigned_list']))
	{
		$id_assigned = array();
		foreach ($_POST['id_assigned_list'] as $assigned)
			$id_assigned[(int) $assigned] = (int) $assigned;

		$id_assigned = implode(',',$id_assigned);
	}

	// Any mistakes?
	if (!empty($post_errors))
	{
		loadLanguage('Errors');
		
		//Check message from cases
		$context['post_error'] = array('description' => array());
		foreach ($post_errors as $post_error)
		{
			$context['post_error'][$post_error] = true;
			if ($post_error == 'long_message')
				$txt['ts_error_' . $post_error] = sprintf($txt['ts_error_' . $post_error], $modSettings['max_messageLength']);

			$context['post_error']['message'][] = $txt['ts_error_' . $post_error];
		}

		//Check steps from cases
		$context['post_error'] = array('steps' => array());
		foreach ($post_errors as $post_error)
		{
			$context['post_error'][$post_error] = true;
			if ($post_error == 'long_message')
				$txt['ts_error_' . $post_error] = sprintf($txt['ts_error_' . $post_error], $modSettings['max_messageLength']);

			$context['post_error']['steps'][] = $txt['ts_error_' . $post_error];
		}

		//Check steps from cases
		$context['post_error'] = array('expected_result' => array());
		foreach ($post_errors as $post_error)
		{
			$context['post_error'][$post_error] = true;
			if ($post_error == 'long_message')
				$txt['ts_error_' . $post_error] = sprintf($txt['ts_error_' . $post_error], $modSettings['max_messageLength']);

			$context['post_error']['expected_result'][] = $txt['ts_error_' . $post_error];
		}
		$context['test_suite']['current_case'] = $case_id;
		return TS_EditCase();
	}

	// At about this point, we're posting and that's that.
	ignore_user_abort(true);
	@set_time_limit(300);

	// Add special html entities to the Test Case Name, username, email and expected result.
	$_POST['name'] = strtr($smcFunc['htmlspecialchars']($_POST['name']), array("\r" => '', "\n" => '', "\t" => ''));
	$poster_name = htmlspecialchars($user_info['username']);
	$poster_email = htmlspecialchars($user_info['email']);

	// At this point, we want to make sure the name isn't too long.
	if ($smcFunc['strlen']($_POST['name']) > 100)
	{
		$_POST['name'] = $smcFunc['substr']($_POST['name'], 0, 100);
	}

	$caseOptions = array(
		'id' => empty($case_id) ? 0 : $case_id,
		'id_suite' => empty($suite_id) ? 0 : $suite_id,
		'case_name' => $_POST['name'],
		'description' => $_POST['message'],
		'steps' => $_POST['steps'],
		'expected_result' => $_POST['expected_result'],
	);
	$posterOptions = array(
		'id_assigned' =>  empty($id_assigned) ? 0 : $id_assigned,
	);

	// Only consider marking as editing if they have edited the subject, message or icon.
	if ((isset($_POST['name']) && $_POST['name'] != $context['test_suite']['case']['name']) || (isset($_POST['message']) && $_POST['message'] != $context['test_suite']['case']['description']) || (isset($_POST['steps']) && $_POST['steps'] != $context['test_suite']['case']['steps']) || (isset($_POST['expected_result']) && $_POST['expected_result'] != $context['test_suite']['case']['expected_result']))
	{
		$caseOptions['modify_time'] = time();
		$caseOptions['modified_by'] = $user_info['name'];
	}
	TS_modifyCase($caseOptions, $posterOptions);

	// If we didn't change anything this time but had before put back the old info.
	if (!isset($caseOptions['modify_time']) && !empty($context['test_suite']['case']['modified_time']))
	{
		$caseOptions['modify_time'] = $context['test_suite']['case']['modified_time'];
		$caseOptions['modified_by'] = $context['test_suite']['case']['modified_by'];
	}
	redirectexit('action=testsuite;case='. $case_id);
}

function TS_PostRun()
{
	global $txt, $scripturl, $modSettings, $user_info, $context, $sourcedir, $smcFunc, $settings;

	if (empty($context['post_errors']))
	{
		$context['post_errors'] = array();
	}

	$context['test_suite']['current_case'] = isset($_REQUEST['c']) ? (int) $_REQUEST['c'] : 0;

	if (empty($context['test_suite']['current_case']))
	{
		fatal_lang_error('ts_no_case');
	}

	// Project ID and Suite ID for the run are needed.
	$context['test_suite']['case'] = TS_loadCase($context['test_suite']['current_case'], false);

	// Permissions.
	if (!TS_can_do('postrun', array('id_proj' => $context['test_suite']['case']['id_project'], 'id_suite' => $context['test_suite']['case']['id_suite'])))
	{
		fatal_lang_error('ts_cannot_permission_generic');
	}

	$id_bug = isset($_REQUEST['id_bug']) ? (int) $_REQUEST['id_bug'] : 0;

	TS_Validator('run');
	
	// Set the destination action for submission.
	$context['destination'] = 'createRun2';
	$context['submit_label'] = $txt['ts_submit'];

	// Create page titles and linktrees
	$context['page_title'] = $txt['ts_submit_run_result'];

	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';project='. $context['test_suite']['case']['id_project'],
		'name' => $context['test_suite']['case']['project_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_project'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);

	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';suite='. $context['test_suite']['case']['id_suite'],
		'name' => $context['test_suite']['case']['suite_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_suite'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);

	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';case='. $context['test_suite']['case']['id'],
		'name' => $context['test_suite']['case']['name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_case'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
	);

	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';case='. $context['test_suite']['current_case'],
		'name' => $context['page_title'],
	);

	// Call Our Sub-Template.
	$context['sub_template'] = 'create_run';

	checkSubmitOnce('register');
}

function TS_PostRun2()
{
	global $txt, $modSettings, $sourcedir, $context, $scripturl, $user_info, $smcFunc;

	require_once($sourcedir . '/Subs-SM-TestSuite.php');

	$context['test_suite']['current_case'] = isset($_REQUEST['c']) ? (int) $_REQUEST['c'] : 0;

	// Project ID and Suite ID for the run are needed.
	$context['test_suite']['case'] = TS_loadCase($context['test_suite']['current_case'], false);

	// Permissions.
	if (!TS_can_do('postrun', array('id_proj' => $context['test_suite']['case']['id_project'], 'id_suite' => $context['test_suite']['case']['id_suite'])))
		fatal_lang_error('ts_cannot_permission_generic');

	// Are we hacking?
	if (empty($context['test_suite']['current_case']))
	{
		fatal_lang_error('ts_no_case');
	}

	$id_bug = isset($_REQUEST['id_bug']) ? (int) $_REQUEST['id_bug'] : 0;
	$case_id = $context['test_suite']['current_case'];
	// Prevent double submission of this form.
	checkSubmitOnce('check');

	// No errors as yet.
	$post_errors = array();

	// If the session has timed out, let the user re-submit their form.
	if (checkSession('createRun', '', false) != '')
		$post_errors[] = 'session_timeout';

	if ($_POST['result'] == 'select')
		$post_errors[] = 'no_result';
	if (!isset($_POST['feedback']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['feedback']), ENT_QUOTES) === '')
		$post_errors[] = 'no_feedback';
	else
	{
		// Prepare the feedback a bit for some additional testing.
		$_POST['feedback'] = $smcFunc['htmlspecialchars']($_POST['feedback'], ENT_QUOTES);
	}

	// Any mistakes?
	if (!empty($post_errors))
	{
		loadLanguage('Errors');

		$context['post_error'] = array('feedback' => array());
		foreach ($post_errors as $post_error)
		{
			$context['post_error'][$post_error] = true;
			if ($post_error == 'long_feedback')
				$txt['ts_error_' . $post_error] = sprintf($txt['ts_error_' . $post_error], $modSettings['max_messageLength']);

			$context['post_error']['feedback'][] = $txt['ts_error_' . $post_error];
		}
		
		return TS_PostRun();
	}

	// At about this point, we're posting and that's that.
	ignore_user_abort(true);
	@set_time_limit(300);

	// Add special html entities to the Test Case Name, username, email and expected result.
	$poster_name = htmlspecialchars($user_info['username']);
	$poster_email = htmlspecialchars($user_info['email']);

	$runOption = array(
		'id' => 0,
		'id_case' => empty($case_id) ? 0 : $case_id,
		'result_achieved' => $_POST['result'],
		'feedback' => $_POST['feedback'],
		'id_bug' => empty($id_bug) ? 0 : $id_bug,
	);
	$posterOptions = array(
		'id' => $user_info['id'],
		'name' => $poster_name,
		'email' => $poster_email,
	);
	TS_createRun($runOption, $posterOptions);
	$case = TS_loadCase($case_id);

	// Note: move this to subs
	// Update the counter for total no of runs made in suites table
	$smcFunc['db_query']('', '
			UPDATE {db_prefix}testsuite_suites
			SET count = count + {int:count}
			WHERE id_suite = {int:id_suite}',
				array(
					'id_suite' => $case['id_suite'],
					'count' => 1,
				)
	);

	// Note: move this to subs
	// Update the counter for no. of fail runs made in suites table
	if ($_POST['result'] == 'fail')
	{
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}testsuite_suites
			SET fail_count = fail_count + {int:fail_count}
			WHERE id_suite = {int:id_suite}',
				array(
					'id_suite' => $case['id_suite'],
					'fail_count' => 1,
			)
		);
	}

	// Note: move this to subs
	// Update the counter for total no of runs made in cases table
	$smcFunc['db_query']('', '
			UPDATE {db_prefix}testsuite_cases
			SET count = count + {int:count}
			WHERE id_case = {int:id_case}',
				array(
					'id_case' => $case_id,
					'count' => 1,
				)
	);

	// Note: move this to subs
	// Update the counter for no. of fail runs made in cases table
	if ($_POST['result'] == 'fail')
	{
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}testsuite_cases
			SET fail_count = fail_count + {int:fail_count}
			WHERE id_case = {int:id_case}',
				array(
					'id_case' => $case_id,
					'fail_count' => 1,
			)
		);
	}
	redirectexit('action=testsuite;case=' . $case_id);
}

function TS_EditRun()
{
	global $txt, $scripturl, $modSettings, $user_info, $context, $sourcedir, $smcFunc, $settings;

	if (empty($context['test_suite']['current_run']))
	{
		$context['test_suite']['current_run'] = isset($_REQUEST['editrun']) ? (int) $_REQUEST['editrun'] : 0; 
	}
	// Is editrun not set either?
	if (empty($context['test_suite']['current_run']))
	{
		$run_id = isset($_REQUEST['r']) ? (int) $_REQUEST['r'] : 0;
	}
	else
	{
		$run_id = $context['test_suite']['current_run'];
	}

	if (empty($run_id))
	{
		return false;
	}

	$id_bug = isset($_REQUEST['id_bug']) ? (int) $_REQUEST['id_bug'] : 0;
	
	// Get data for this specific suite.
	if (!empty($run_id))
	{
		$context['test_suite']['run'] = TS_loadRun($run_id);
		$case_id = $context['test_suite']['run']['id_case'];
		$context['result_achieved'] = $context['test_suite']['run']['result_achieved'];
		$context['feedback'] = $context['test_suite']['run']['feedback'];
	}

	if (empty($context['post_errors']))
	{
		$context['post_errors'] = array();
	}
	TS_Validator('run');

	// Set the destination action for submission.
	$context['destination'] = 'editrun2';
	$context['submit_label'] = $txt['ts_submit'];

	// Call Our Sub-Template.
	$context['sub_template'] = 'edit_run';
	$context['page_title'] = $txt['ts_edit'] . ' '. $txt['ts_run'];
	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';case=' . $case_id . '',
		'name' => $context['test_suite']['run']['project_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_project'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>',
	);
	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';suite=' . $context['test_suite']['current_suite'] . '',
		'name' => $context['test_suite']['run']['suite_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_suite'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>',
	);
	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';case=' . $case_id . '',
		'name' => $context['test_suite']['run']['case_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_case'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ? ' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>',
	);
	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';case=' . $case_id . '',
		'name' => $context['page_title'],
	);
}

function TS_EditRun2()
{
	global $txt, $modSettings, $sourcedir, $context, $scripturl, $user_info, $smcFunc;

	require_once($sourcedir . '/Subs-SM-TestSuite.php');

	$run_id = $context['test_suite']['current_run'];
	if (empty($run_id))
	{
		$run_id = isset($_REQUEST['r']) ? (int) $_REQUEST['r'] : 0;
	}
	
	$id_bug = isset($_REQUEST['id_bug']) ? (int) $_REQUEST['id_bug'] : 0;

	// Prevent double submission of this form.
	checkSubmitOnce('check');

	// No errors as yet.
	$post_errors = array();

	// If the session has timed out, let the user re-submit their form.
	if (checkSession('editrun', '', false) != '')
	{
		$post_errors[] = 'session_timeout';
	}

	// Note: write a proper function in subs for this
	// Get data for this specific run.
	$request = $smcFunc['db_query']('', '
		SELECT
			r.result_achieved, r.feedback, r.id_bug, r.id_member, r.poster_name, r.poster_time, r.poster_email, r.modified_by, r.modified_time, r.id_case
			FROM {db_prefix}testsuite_runs AS r
			WHERE r.id_run = {int:current_run}',
			array(
			'current_run' => $run_id,
			'current_member' => $user_info['id'],
		)
	);

	if ($smcFunc['db_num_rows']($request) == 0)
	{
		fatal_lang_error('no_run', false);
	}
	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);

	// If the session has timed out, let the user re-submit their form.
	if (checkSession('createRun', '', false) != '')
	{
		$post_errors[] = 'session_timeout';
	}

	if ($_POST['result'] == 'select')
	{
		$post_errors[] = 'no_result';
	}

	if (!isset($_POST['feedback']) || $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($_POST['feedback']), ENT_QUOTES) === '')
	{
		$post_errors[] = 'no_feedback';
	}
	else
	{
		// Prepare the feedback a bit for some additional testing.
		$_POST['feedback'] = $smcFunc['htmlspecialchars']($_POST['feedback'], ENT_QUOTES);
	}

	// Any mistakes?
	if (!empty($post_errors))
	{
		loadLanguage('Errors');
		
		$context['post_error'] = array('feedback' => array());
		foreach ($post_errors as $post_error)
		{
			$context['post_error'][$post_error] = true;
			if ($post_error == 'long_feedback')
			{
				$txt['ts_error_' . $post_error] = sprintf($txt['ts_error_' . $post_error], $modSettings['max_messageLength']);
			}

			$context['post_error']['feedback'][] = $txt['ts_error_' . $post_error];
		}
		$context['test_suite']['current_run'] = $run_id;
		return TS_EditRun();
	}

	// At about this point, we're posting and that's that.
	ignore_user_abort(true);
	@set_time_limit(300);

	// Add special html entities to the Test Case Name, username, email and expected result.
	$poster_name = htmlspecialchars($user_info['username']);
	$poster_email = htmlspecialchars($user_info['email']);

	$runOptions = array(
		'id' => empty($run_id) ? 0 : $run_id,
		'result_achieved' => $_POST['result'],
		'feedback' => $_POST['feedback'],
		'id_bug' => empty($id_bug) ? 0 : $id_bug,
	);
	$posterOptions = array();

	// Only consider marking as editing if they have edited the subject, message or icon.
	if ((isset($_POST['result']) && $_POST['result'] != $row['result_achieved']) || (isset($_POST['feedback']) && $_POST['feedback'] != $row['feedback']) || (isset($_POST['id_bug']) && $_POST['id_bug'] != $row['id_bug']))
	{
		$runOptions['modify_time'] = time();
		$runOptions['modified_by'] = $user_info['name'];
	}

	TS_modifyRun($runOptions, $posterOptions);

	// If we didn't change anything this time but had before put back the old info.
	if (!isset($runOptions['modify_time']) && !empty($row['modified_time']))
	{
		$runOptions['modify_time'] = $row['modified_time'];
		$runOptions['modified_by'] = $row['modified_by'];
	}

	redirectexit('action=testsuite;case='. $row['id_case']);
}

function TS_DeleteItem()
{
	global $sourcedir;

	isAllowedTo('admin_forum');

	$project_id = isset($_REQUEST['removeproject']) ? (int) $_REQUEST['removeproject'] : 0;
	$suite_id = isset($_REQUEST['removesuite']) ? (int) $_REQUEST['removesuite'] : 0;
	$case_id = isset($_REQUEST['removecase']) ? (int) $_REQUEST['removecase'] : 0;
	$run_id = isset($_REQUEST['removerun']) ? (int) $_REQUEST['removerun'] : 0;

	if (!empty($project_id))
	{
		// Make sure the project exists first.
		$project = TS_loadProject($project_id);
		TS_removeProject($project_id, '3');
		redirectexit('action=testsuite');
	}
	elseif (!empty($suite_id))
	{
		// So we can go back to where the suite was deleted and verify it exists.
		$suite = TS_loadSuite($suite_id);
		$project_id = $suite['id_project'];

		TS_removeSuite($suite_id, '2');
		redirectexit('action=testsuite;project=' . $project_id);
	}
	elseif (!empty($case_id))
	{
		$case = TS_loadCase($case_id, '1');
		$suite_id = $case['id_suite'];
		
		TS_removeCase($case_id);
		redirectexit('action=testsuite;suite=' . $suite_id);
	}
	elseif (!empty($run_id))
	{
		$run = TS_loadRun($run_id);
		$case_id = $run['id_case'];
		TS_removeRun($run_id);
		redirectexit('action=testsuite;case=' . $case_id);
	}
	// Should never get to here, but well, do it anyway!
	fatal_lang_error('ts_no_suite');
}

function TS_CopyItem()
{
	global $context, $settings, $txt, $sourcedir;

	isAllowedTo('admin_forum');
	require_once($sourcedir . '/Subs-SM-TestSuite.php');
	$context['sub_template'] = 'copy';

	$project_id = isset($_REQUEST['copyproject']) ? (int) $_REQUEST['copyproject'] : 0;
	$suite_id = isset($_REQUEST['copysuite']) ? (int) $_REQUEST['copysuite'] : 0;
	$case_id = isset($_REQUEST['copycase']) ? (int) $_REQUEST['copycase'] : 0;

	if (!empty($project_id))
	{
		// Make sure the project exists first.
		$project = TS_loadProject($project_id);

		if (empty($project))
		fatal_lang_error('ts_no_project');

		$context['linktree'][] = array(
			'url' => $context['test_suite']['url'] . ';project=' . $project_id,
			'name' => $project['name'],
			'extra_before' => '<span' . ($settings['linktree_inline'] ? 
				' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_project'] . ' ( </strong></span>',
			'extra_after' => '<span' . ($settings['linktree_inline'] ?
				' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
		);
		$context['page_title'] = $txt['ts_copy'] . ' '. $txt['ts_project'];

		//The hidden attributes to describe the level in which we are working
		$context['id'] = $project_id;
		$context['type'] = $txt['ts_project'];
	}
	elseif (!empty($suite_id))
	{
		// Make sure the project exists first.
		$suite = TS_loadSuite($suite_id);

		if (empty($suite))
		fatal_lang_error('ts_no_project');

		$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';project=' . $suite['id_project'],
		'name' => $suite['project_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? 
			' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_project'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ?
			' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
		);
		// Add the current suite to the linktree.
		$context['linktree'][] = array(
			'url' => $context['test_suite']['url'] . ';suite=' . $suite_id,
			'name' => $suite['name'],
			'extra_before' => '<span' . ($settings['linktree_inline'] ? 
				' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_suite'] . ' ( </strong></span>',
			'extra_after' => '<span' . ($settings['linktree_inline'] ?
				' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
		);
		$context['page_title'] = $txt['ts_copy'] . ' '. $txt['ts_suite'];

		//The hidden attributes to describe the level in which we are working
		$context['id'] = $suite_id;
		$context['type'] = $txt['ts_suite'];
	}
	elseif (!empty($case_id))
	{
		// Make sure the project exists first.
		$case = TS_loadCase($case_id);

		if (empty($case))
		fatal_lang_error('ts_no_project');

		$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';project=' . $case['id_project'],
		'name' => $case['project_name'],
		'extra_before' => '<span' . ($settings['linktree_inline'] ? 
			' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_project'] . ' ( </strong></span>',
		'extra_after' => '<span' . ($settings['linktree_inline'] ?
			' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
		);
		$context['linktree'][] = array(
			'url' => $context['test_suite']['url'] . ';suite=' . $case['id_suite'],
			'name' => $case['suite_name'],
			'extra_before' => '<span' . ($settings['linktree_inline'] ? 
				' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_suite'] . ' ( </strong></span>',
			'extra_after' => '<span' . ($settings['linktree_inline'] ?
				' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
		);
		$context['linktree'][] = array(
			'url' => $context['test_suite']['url'] . ';case=' . $case_id,
			'name' => $case,
			'extra_before' => '<span' . ($settings['linktree_inline'] ? 
				' class="smalltext"' : '') . '><strong class="nav">' . $txt['ts_case'] . ' ( </strong></span>',
			'extra_after' => '<span' . ($settings['linktree_inline'] ?
				' class="smalltext"' : '') . '><strong class="nav"> )</strong></span>'
		);
		$context['page_title'] = $txt['ts_copy'] . ' '. $txt['ts_case'];

		//The hidden attributes to describe the level in which we are working
		$context['id'] = $case_id;
		$context['type'] = $txt['ts_case'];
	}
}

function TS_CopyItem2()
{
	global $context, $settings, $txt, $sourcedir;

	isAllowedTo('admin_forum');
	require_once($sourcedir . '/Subs-SM-TestSuite.php');

	$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
	$level = isset($_REQUEST['level']) && is_array($_REQUEST['level']) ? count($_REQUEST['level']) : 0;

	// as the level(e.g project) exist, so no need to load it again
	if ($type == 'Project')
	{
		TS_copyProject($id, $level);
		redirectexit('action=testsuite');
	}
	elseif ($type == 'Suite')
	{
		TS_copySuite($id, $level);
		redirectexit('action=testsuite');
	}
	elseif ($type == 'Case')
	{
		TS_copyCase($id, $level);
		redirectexit('action=testsuite');
	}
}

function TS_Admin()
{
	global $context, $txt, $sourcedir;
	
	$subActions = array(
		'per_level' => 'TS_Admin_PerLevel',
		'main' => 'TS_Admin_Main'
	);

	$_REQUEST['admin'] = empty($_REQUEST['admin']) ? 'main' : $_REQUEST['admin'];

	// Pick the correct sub-action.
	if (isset($_REQUEST['admin']) && isset($subActions[$_REQUEST['admin']]))
		$context['sub_action'] = $_REQUEST['admin'];
	else
		$context['sub_action'] = 'main';

	$subActions[$context['sub_action']]();
}

function TS_Admin_Main()
{
	global $context, $txt, $sourcedir;

	$context['sub_template'] = 'testsuite_admin';
	$context['page_title'] = $txt['ts_admin'];
	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';admin',
		'name' => $txt['ts_admin'],
	);
}

function TS_Admin_PerLevel()
{
	global $context, $txt, $sourcedir, $smcFunc;

	$context['test_suite']['permission']['level_name'] = isset($_REQUEST['level_name']) ? strtolower($_REQUEST['level_name']) : '';
	$context['test_suite']['permission']['id_level'] = isset($_REQUEST['id_level']) && !empty($_REQUEST['id_level']) ? (int) $_REQUEST['id_level'] : '';

	if (empty($context['test_suite']['permission']['level_name']) || empty($context['test_suite']['permission']['id_level']))
		redirectexit('action=testsuite;admin');

	else
		$context['test_suite']['permission']['level_name'] =  $smcFunc['htmltrim']($smcFunc['htmlspecialchars']($context['test_suite']['permission']['level_name']), ENT_QUOTES);

	$context['sub_template'] = 'testsuite_admin_per_level';
	$context['page_title'] = $txt['ts_admin'];
	$context['linktree'][] = array(
		'url' => $context['test_suite']['url'] . ';admin',
		'name' => $txt['ts_admin'],
	);

	require_once($sourcedir . '/Subs-Membergroups.php');
	$context['test_suite']['groups'][-1] = array(
		'id_group' => -1,
		'group_name' => $txt['ts_guests'],
	);
	$context['test_suite']['groups'][0] = array(
		'id_group' => 0,
		'group_name' => $txt['ts_members'],
	);
	$context['test_suite']['groups'] += list_getMembergroups(null, null, 'id_group', 'regular');
	// Ugly hardcoded hack to get rid of unwanted local moderator option.
	unset($context['test_suite']['groups'][3]);
	unset($context['test_suite']['groups'][1]);

	$context['test_suite']['permissions'] = array();
	$context['test_suite']['perms'] = TS_load_permissions($context['test_suite']['permission']['level_name'], $context['test_suite']['permission']['id_level']);

	if (isset($_POST['submit']))
	{
		$context['test_suite']['database'] = array();
		$context['test_suite']['database']['level_name'] = $_POST['level_name'];
		$context['test_suite']['database']['id_level'] = $_POST['id_level'];
		$context['test_suite']['database']['groups_can_view'] = isset($_POST['groups_can_view']) && !empty($_POST['groups_can_view']) ? implode(",", $_POST['groups_can_view']) : '';
		$context['test_suite']['database']['groups_can_manage'] = isset($_POST['groups_can_manage']) && !empty($_POST['groups_can_manage']) ? implode(",", $_POST['groups_can_manage']) : '';
		$context['test_suite']['database']['groups_can_edit'] = isset($_POST['groups_can_edit']) && !empty($_POST['groups_can_edit']) ? implode(",", $_POST['groups_can_edit']) : '';
		$context['test_suite']['database']['groups_can_delete'] = isset($_POST['groups_can_delete']) && !empty($_POST['groups_can_delete']) ? implode(",", $_POST['groups_can_delete']) : '';
		$context['test_suite']['database']['groups_can_create'] = isset($_POST['groups_can_create']) && !empty($_POST['groups_can_create']) ? implode(",", $_POST['groups_can_create']) : '';

		TS_updatePermissions($context['test_suite']['database']);
		redirectexit('action=testsuite;admin=per_level;level_name='. $context['test_suite']['permission']['level_name'] .';id_level='.$context['test_suite']['permission']['id_level']);
	}
}

function TS_UpdateDefaultProject()
{
	global $context, $user_info;

	if (!isset($_POST['projects']))
	{
		return;
	}
	$id_project = (int) $_POST['projects'];

	updateMemberData($user_info['id'], array('id_project' => $id_project));
	redirectexit($context['test_suite']['url'] . ';project=' . $id_project);
}

?>
