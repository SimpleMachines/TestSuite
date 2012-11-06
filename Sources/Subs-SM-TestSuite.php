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

/**
 * Queries the list of all active projects and returns the data as an array.
 * @global string $smcFunc
 * @global string $scripturl
 * @global array $context
 * @global array $txt
 * @return array the projects the user can see
 */
function TS_requestProjects()
{
	global $smcFunc, $scripturl, $context, $txt;
	
	$request = $smcFunc['db_query']('', '
		SELECT p.id_project, p.project_name, p.description, p.id_member, p.poster_name, p.poster_time, p.poster_email, p.modified_time, p.modified_by
		FROM {db_prefix}testsuite_projects as p
		ORDER BY id_project'
	);

	$projects = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$row['description'] = parse_bbc($row['description'], 0, $row['id_project']);

		$projects[$row['id_project']] = array(
			'id' => $row['id_project'],
			'name' => $row['project_name'],
			'description' => $row['description'],
			'link' => '<a href="' . $context['test_suite']['url'] . ';project=' . $row['id_project'] . '" target="_self">' . $row['project_name'] . '</a>',
			'member' => array(
				'id' => $row['id_member'],
				'username' => $row['poster_name'] != '' ? $row['poster_name'] : $txt['not_applicable'],
				'href' => $row['poster_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
				'link' => $row['poster_name'] != '' ? (!empty($row['id_member']) ? '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['poster_name'] . '</a>' : $row['poster_name']) : $txt['not_applicable'],
			),
			'modified_time' => timeformat($row['modified_time']),
			'modified_by' => $row['modified_by'],
		);
	}
	$smcFunc['db_free_result']($request);

	return $projects;
}

/**
 * Queries the database for all suites for current project
 * @global string $smcFunc
 * @global string $scripturl
 * @global array $context
 * @global array $txt
 * @param int $project_id
 * @param bool $load_suites whether to also load the suites with the project
 * @return array representing the data of the project 
 */
function TS_loadProject($project_id, $load_suites = true)
{
	global $smcFunc, $scripturl, $context, $txt;

	if (empty($project_id))
	{
	    fatal_lang_error("ts_no_project");
	}

	$request = $smcFunc['db_query']('', '
		SELECT p.id_project, p.project_name, p.description, p.id_member, p.poster_name, p.poster_time, p.poster_email, p.modified_time, p.modified_by
		FROM {db_prefix}testsuite_projects as p
		WHERE id_project = {int:current_project}
		LIMIT 1',
		array(
			'current_project' => $project_id,
		)
	);

	// Was the current project not found in the database?
	if ($smcFunc['db_num_rows']($request) == 0)
	{
		fatal_lang_error('ts_no_project', false);
	}

	$project = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$row['description'] = parse_bbc($row['description'], 0, $row['id_project']);

		$project = array(
			'id' => $row['id_project'],
			'name' => $row['project_name'],
			'description' => $row['description'],
			'link' => '<a href="' . $context['test_suite']['url'] . ';project=' . $row['id_project'] . '" target="_self">' . $row['project_name'] . '</a>',
			'member' => array(
				'id' => $row['id_member'],
				'username' => $row['poster_name'] != '' ? $row['poster_name'] : $txt['not_applicable'],
				'href' => $row['poster_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
				'link' => $row['poster_name'] != '' ? (!empty($row['id_member']) ? '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['poster_name'] . '</a>' : $row['poster_name']) : $txt['not_applicable'],
			),
			'modified_time' => timeformat($row['modified_time']),
			'modified_by' => $row['modified_by'],
			'suites' => array(),
		);
	}

	$smcFunc['db_free_result']($request);

	if ($load_suites)
	{
	    // Grab the sweets from the candy store, depending on the ID of the project.
	    $request = $smcFunc['db_query']('', '
			SELECT s.id_suite, s.id_project, s.suite_name, s.description, s.id_member, s.poster_name, s.poster_time, s.poster_email, s.count, s.fail_count, s.modified_time, s.modified_by
			FROM {db_prefix}testsuite_suites as s
			WHERE id_project = {int:current_project}
			ORDER BY id_suite',
			array(
				'current_project' => $project_id,
			)
		);
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
				$row['description'] = parse_bbc($row['description'], 0, $row['id_suite']);

				$project['suites'][$row['id_suite']] = array(
					'id' => $row['id_suite'],
					'id_project' => $row['id_project'],
					'name' => $row['suite_name'],
					'description' => $row['description'],
					'count' => $row['count'],
					'count_link' =>'<a href="' . $context['test_suite']['url'] . ';runsforsuite=' . $row['id_suite'] . ';result=' . $txt['ts_total'] .'" target="_self">' . $row['count'] . ' (' . $txt['ts_view'] . ')</a>',
					'pass_count' => ($row['count'] - $row['fail_count']),
					'pass_count_link' =>'<a href="' . $context['test_suite']['url'] . ';runsforsuite=' . $row['id_suite'] . ';result=pass" target="_self">' . ($row['count'] - $row['fail_count']) . ' (' . $txt['ts_view'] . ')</a>',
					'fail_count' => $row['fail_count'],
					'fail_count_link' =>'<a href="' . $context['test_suite']['url'] . ';runsforsuite=' . $row['id_suite'] . ';result=' . $txt['ts_fail'] .'" target="_self">' . $row['fail_count'] . ' (' . $txt['ts_view'] . ')</a>',
					'link' => '<a href="' . $context['test_suite']['url'] . ';suite=' . $row['id_suite'] . '" target="_self">' . $row['suite_name'] . '</a>',
					'member' => array(
						'id' => $row['id_member'],
						'username' => $row['poster_name'] != '' ? $row['poster_name'] : $txt['not_applicable'],
						'href' => $row['poster_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
						'link' => $row['poster_name'] != '' ? (!empty($row['id_member']) ? '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['poster_name'] . '</a>' : $row['poster_name']) : $txt['not_applicable'],
					),
					'modified_time' => timeformat($row['modified_time']),
					'modified_by' => $row['modified_by'],
				);
		}

		$smcFunc['db_free_result']($request);
	}

	return $project;
}

/**
 * Queries and returns information about the specified suite of a project.
 * @global string $smcFunc
 * @global string $scripturl
 * @global array $context
 * @param int $suite_id the id of the suite to query
 * @param bool $load_cases whether to load associated cases with the suite
 * @return array representation of the suite data 
 */

function TS_loadSuite($suite_id, $load_cases = true)
{
	global $smcFunc, $scripturl, $context;

	if (empty($suite_id))
	{
		fatal_lang_error("ts_no_suite");
	}

	$suite = array();

	// Load just the one suite that we are on. Don't need to load all suites when we don't need them.
	$request = $smcFunc['db_query']('', '
		SELECT p.id_project, p.project_name, s.id_suite, s.suite_name, s.description
		FROM {db_prefix}testsuite_suites as s
		INNER JOIN {db_prefix}testsuite_projects as p ON p.id_project = s.id_project
		WHERE s.id_suite = {int:current_suite}
		ORDER BY id_suite
		LIMIT 1',
		array(
			'current_suite' => $suite_id,
		)
	);

	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$suite = array(
			'id_project' => $row['id_project'],
			'project_name' => $row['project_name'],
			'id_suite' => $row['id_suite'],
			'name' => $row['suite_name'],
			'description' => $row['description'], 
			'link' => '<a href="' . $context['test_suite']['url'] . ';suite=' . $row['id_suite'] . '" target="_self">' . $row['suite_name'] . '</a>',
			'cases' => array(),
		);
	}

	// Was the current project not found in the database?
	if ($smcFunc['db_num_rows']($request) == 0)
	{
		fatal_lang_error('ts_no_suite', false);
	}

	$smcFunc['db_free_result']($request);

	if ($load_cases)
	{
	    $request = $smcFunc['db_query']('', '
			SELECT c.id_case, c.id_suite, c.case_name, c.description, c.steps, c.expected_result, c.id_member, c.poster_name, c.poster_time, c.poster_email, c.id_assigned, c.modified_time, c.modified_by, count, c.fail_count
			FROM {db_prefix}testsuite_cases as c
			WHERE id_suite = {int:current_suite}
			ORDER BY id_case',
			array(
				'current_suite' => $suite_id,
			)
		);
		$suite['cases'] = array();

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$row['description'] = parse_bbc($row['description'], 1, $row['id_case']);
			$row['steps'] = parse_bbc($row['steps'], 0, $row['id_case']);

			$suite['cases'][$row['id_case']] = array(
				'id' => $row['id_case'],
				'id_suite' => $row['id_suite'],
				'name' => $row['case_name'],
				'description' => $row['description'],
				'steps' => $row['steps'],
				'expected_result' => $row['expected_result'],
				'id_assigned' => $row['id_assigned'],
				'count' => $row['count'],
				'fail_count' => $row['fail_count'],
				'pass_count' => ($row['count'] - $row['fail_count']),
				'link' => '<a href="' . $context['test_suite']['url'] . ';case=' . $row['id_case'] . '" target="_self">' . $row['case_name'] . '</a>',
				'member' => array(
					'id' => $row['id_member'],
					'username' => $row['poster_name'] != '' ? $row['poster_name'] : $txt['not_applicable'],
					'href' => $row['poster_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
					'link' => $row['poster_name'] != '' ? (!empty($row['id_member']) ? '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['poster_name'] . '</a>' : $row['poster_name']) : $txt['not_applicable'],
				),
				'modified_time' => timeformat($row['modified_time']),
				'modified_by' => $row['modified_by'],
			);
		}
		$smcFunc['db_free_result']($request);
	}

	return $suite;
}

/**
 * Queries information about the specified case of a suite.
 * @global string $smcFunc
 * @global string $scripturl
 * @global array $context
 * @param int $case_id the id of the case to query
 * @param bool $load_runs whether to load associated runs with the case or not
 * @return string 
 */
function TS_loadCase($case_id, $load_runs = true)
{
	global $smcFunc, $scripturl, $context, $memberContext;
	
	// Load just the current case.
	$request = $smcFunc['db_query']('', '
		SELECT c.id_case, p.project_name, p.id_project, c.id_suite, s.suite_name, c.case_name, c.description, c.steps, c.expected_result, c.id_member, c.poster_name, c.poster_time, c.poster_email, c.id_assigned, c.modified_time, c.modified_by, c.count, c.fail_count
		FROM {db_prefix}testsuite_cases as c
		INNER JOIN {db_prefix}testsuite_suites as s ON (c.id_suite = s.id_suite)
		INNER JOIN {db_prefix}testsuite_projects as p ON (s.id_project = p.id_project)
		WHERE id_case = {int:id_case}
		ORDER BY id_case
		LIMIT 1',
		array(
			'id_case' => $case_id,
		)
	);

	$case = array();
	
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$row['description'] = parse_bbc($row['description'], 1, $row['id_case']);
		$row['steps'] = parse_bbc($row['steps'], 1, $row['id_case']);
		$row['expected_result'] = parse_bbc($row['expected_result'], 1, $row['id_case']);

		$case = array(
			'id' => $row['id_case'],
			'id_suite' => $row['id_suite'],
			'id_project' => $row['id_project'],
			'name' => $row['case_name'],
			'suite_name' => $row['suite_name'],
			'project_name' => $row['project_name'],
			'description' => $row['description'],
			'steps' => $row['steps'],
			'expected_result' => $row['expected_result'],
			'id_assigned' => $row['id_assigned'],
			'count' => $row['count'],
			'fail_count' => $row['fail_count'],
			'pass_count' => ($row['count'] - $row['fail_count']),
			'link' => '<a href="' . $context['test_suite']['url'] . ';case=' . $row['id_case'] . '" target="_self">' . $row['case_name'] . '</a>',
			'member' => array(
				'id' => $row['id_member'],
				'username' => $row['poster_name'] != '' ? $row['poster_name'] : $txt['not_applicable'],
				'href' => $row['poster_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
				'link' => $row['poster_name'] != '' ? (!empty($row['id_member']) ? '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['poster_name'] . '</a>' : $row['poster_name']) : $txt['not_applicable'],
			),
			'modified_time' => timeformat($row['modified_time']),
			'modified_by' => $row['modified_by'],
			'runs' => array(),
		);
	}

	if ($smcFunc['db_num_rows']($request) <= 0)
	{
		fatal_lang_error('ts_no_case');
	}
	$smcFunc['db_free_result']($request);

	if (!empty($case['id_assigned']))
	{
		$members = explode(',', $case['id_assigned']);

		$request = $smcFunc['db_query']('', '
		SELECT id_member, real_name
		FROM {db_prefix}members
		WHERE id_member IN ({array_int:members})',
			array(
				'members' => $members,
			)
		);
		$case['id_assigned'] =array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$case['id_assigned'][] = array(
					'id' => $row['id_member'],
					'name' => $row['real_name'],
					'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['real_name'] . '</a>',
				);
		}
		$smcFunc['db_free_result']($request);
	}
	
	$request = $smcFunc['db_query']('', '
		SELECT id_run, id_case, result_achieved, feedback, id_member, poster_name, poster_time, poster_email, id_bug, modified_time, modified_by
		FROM {db_prefix}testsuite_runs
		WHERE id_case = {int:current_case}
		ORDER BY id_run',
		array(
			'current_case' => $case_id,
		)
	);
	$context['test_suite']['run'] = array();
	
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$row['feedback'] = parse_bbc($row['feedback'], 0, $row['id_run']);

		$case['runs'][$row['id_run']] = array(
			'id' => $row['id_run'],
			'id_case' => $row['id_case'],
			'result_achieved' => $row['result_achieved'],
			'feedback' => $row['feedback'],
			'id_bug' => $row['id_bug'],
			'href' => $context['test_suite']['url'] . ';case=' . $row['id_case'] . ';#run' . $row['id_run'],
			'link' => '<a href="' . $context['test_suite']['url'] . ';case=' . $row['id_case'] . ';#run' . $row['id_run'] . '">' . $row['id_run'] . '</a>',
			'time' => timeformat($row['poster_time']),
			'member' => array(
				'id' => $row['id_member'],
				'username' => $row['poster_name'] != '' ? $row['poster_name'] : $txt['not_applicable'],
				'href' => $row['poster_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
				'link' => $row['poster_name'] != '' ? (!empty($row['id_member']) ? '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['poster_name'] . '</a>' : $row['poster_name']) : $txt['not_applicable'],
			),
			'modified_time' => timeformat($row['modified_time']),
			'modified_by' => $row['modified_by'],
		);
	}
	$smcFunc['db_free_result']($request);

	return $case;
}

/**
 * Queries and returns information about the specified run of a case.
 * @global string $smcFunc
 * @global string $scripturl
 * @global array $context
 * @param type $run_id the id of the run to query
 * @return array representation of information about the loaded run 
 */
function TS_loadRun($run_id)
{
	global $smcFunc, $scripturl, $context;
	
	// Load just the current case.
	$request = $smcFunc['db_query']('', '
		SELECT 
		r.id_run, r.id_case, r.result_achieved, r.feedback, r.id_member, r.poster_name, r.poster_time, r.poster_email, r.id_bug, r.modified_time, r.modified_by,
		c.id_case, c.case_name, s.id_suite, s.suite_name, p.id_project, p.project_name
		FROM {db_prefix}testsuite_runs as r
		INNER JOIN {db_prefix}testsuite_cases AS c ON (r.id_case = c.id_case)
		INNER JOIN {db_prefix}testsuite_suites AS s ON (c.id_suite = s.id_suite)
		INNER JOIN {db_prefix}testsuite_projects AS p ON (p.id_project = s.id_project)
		WHERE id_run = {int:id_run}
		LIMIT 1',
		array(
			'id_run' => $run_id,
		)
	);

	$run = array();
	
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$row['feedback'] = parse_bbc($row['feedback'], 0, $row['id_case']);

		$run = array(
			'id' => $row['id_run'],
			'id_case' => $row['id_case'],
			'id_suite' => $row['id_suite'],
			'id_project' => $row['id_project'],
			'project_name' => $row['project_name'],
			'suite_name' => $row['suite_name'],
			'case_name' => $row['case_name'],
			'result_achieved' => $row['result_achieved'],
			'feedback' => $row['feedback'],
			'id_bug' => $row['id_bug'],
			'href' => $context['test_suite']['url'] . '?;case=' . $row['id_case'] . ';#run' . $row['id_run'],
			'link' => '<a href="' . $context['test_suite']['url'] . ';case=' . $row['id_case'] . ';#run' . $row['id_run'] . '">' . $row['id_run'] . '</a>',
			'time' => timeformat($row['poster_time']),
			'member' => array(
				'id' => $row['id_member'],
				'username' => $row['poster_name'] != '' ? $row['poster_name'] : $txt['not_applicable'],
				'href' => $row['poster_name'] != '' && !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
				'link' => $row['poster_name'] != '' ? (!empty($row['id_member']) ? '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['poster_name'] . '</a>' : $row['poster_name']) : $txt['not_applicable'],
			),
			'modified_time' => timeformat($row['modified_time']),
			'modified_by' => $row['modified_by'],
		);
	}
	
	if ($smcFunc['db_num_rows']($request) <= 0)
	{
		fatal_lang_error('ts_no_case');
	}
	
	$smcFunc['db_free_result']($request);

	return $run;
}

/**
 * Queries and returns information of all the runs of a suite.
 * @todo return the information like other places
 * @global string $smcFunc
 * @global string $scripturl
 * @global type $context 
 */
function TS_requestRunsforSuites()
{
	global $smcFunc, $scripturl, $context, $txt;
	
	$suite_id = $context['test_suite']['current_suite'];
	$result = isset($_REQUEST['result']) ? strtolower($_REQUEST['result']) : '';
	
	$request = $smcFunc['db_query']('', '
		SELECT 
			r.id_run, r.id_case, c.case_name, r.result_achieved
		FROM {db_prefix}testsuite_runs AS r
			INNER JOIN {db_prefix}testsuite_cases AS c ON (c.id_case = r.id_case)
		WHERE c.id_suite = {int:current_id_suite}
		ORDER by r.id_run',
		array(
			'current_id_suite' => $suite_id,
		)
	);
	$runs = array();
	$context['test_suite']['run_links'] = array();
	
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		if ($result != 'total' && $row['result_achieved'] != $result)
		{
			continue;
		}
		$runs[$row['id_run']] = array(
			'id_run' => $row['id_run'],
			'run_link' => '<a href="' . $context['test_suite']['url'] . ';case=' . $row['id_case'] . '#run' . $row['id_run'] . '">(View)</a>',
			'run_href' => '' . $context['test_suite']['url'] . ';case=' . $row['id_case'] . '#run' . $row['id_run'] . '',
			'id_case' => $row['id_case'],
			'case_name' => $row['case_name'],
			'case_link' => '<a href="' . $context['test_suite']['url'] . ';case=' . $row['id_case'] . '">' . $row['case_name'] . ' (id: ' . $row['id_case'] . ')</a>',
			'case_href' => '' . $context['test_suite']['url'] . ';case=' . $row['id_case'] . '',
		);

		// Mister? Do we need to filter?
		
	}
	$smcFunc['db_free_result']($request);
	
	return $runs;
}

/**
 * Utility function to create a new fresh and empty project or suite.
 * @global array $user_info
 * @global string $smcFunc
 * @global array $context
 * @param array $projectOption
 * @param array $posterOptions
 * @return bool of whether the creation was successful 
 */
function TS_createProject(&$projectOption, &$posterOptions)
{
	global $user_info, $smcFunc, $context;

	$projectOption['id'] = empty($projectOption['id']) ? 0 : (int) $projectOption['id'];
	$posterOptions['id'] = empty($posterOptions['id']) ? 0 : (int) $posterOptions['id'];
	$posterOptions['name'] = $user_info['name'];
	$posterOptions['email'] = $user_info['email'];
	
	// Insert the Project.
	$smcFunc['db_insert']('',
		'{db_prefix}testsuite_projects',
		array(
			'id_project' => 'int', 'id_member' => 'int', 'project_name' => 'string-255', 'description' => 'string-65534',
			'poster_name' => 'string-255', 'poster_email' => 'string-255', 'poster_time' => 'int',
		),
		array(
			$projectOption['id'], $posterOptions['id'], $projectOption['project_name'], $projectOption['description'],
			$posterOptions['name'], $posterOptions['email'], time(),
		),
		array('project')
	);
	$projectOption['id'] = $smcFunc['db_insert_id']('{db_prefix}testsuite_projects', 'project');

	// Something went wrong creating the Project...
	if (empty($projectOption['id']))
		return false;
		
	// Success.
	return true;
}

/**
 * Removes specified projects.
 * Precondition: project_id belongs to a project that hasn't been deleted.
 * @global string $smcFunc
 * @param type $project_ids
 * @param type $remove_level is the hierarchy of what gets removed.
		Level 0 will just remove the project.
		Level 1 will also remove associated suites.
		Level 2 will remove the associated suites and the cases that belong to them.
		Level 3 will remove everything including the runs associated to those cases nested in the project hierarchy.
 * @global string $smcFunc
 */
function TS_removeProject($project_ids, $remove_level = 0)
{
	global $smcFunc;

	if (!is_array($project_ids))
		$project_ids = array($project_ids);

	if ($remove_level > 0)
	{
		$suite_ids = array();
		foreach ($project_ids as $id)
		{
			$project = TS_loadProject($id);

			if (!empty($project['suites']))
			{
				foreach ($project['suites'] as $suite)
				{
					$suite_ids[] = $suite['id'];
				}
			}
		}
		if (!empty($suite_ids))
		TS_removeSuite($suite_ids, $remove_level - 1);
	}

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}testsuite_projects
		WHERE id_project IN ({array_int:project_ids})',
		array(
			'project_ids' => $project_ids,
		)
	);
}

function TS_copyProject($project_ids, $copy_level = 0)
{
	global $context, $smcFunc, $user_info;

	$request = $smcFunc['db_query']('', '
		SELECT project_name, description
		FROM {db_prefix}testsuite_projects
		WHERE id_project = {int:project_id}
		LIMIT 1',
		array(
			'project_id' => $project_ids,
		)
	);

	$projects = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$project = array(
			'name' => $row['project_name'],
			'description' => $row['description'],
		);
	}
	$smcFunc['db_free_result']($request);

	$smcFunc['db_insert']('',
		'{db_prefix}testsuite_projects',
		array(
			'project_name' => 'string-255', 'description' => 'string-65534',
			'poster_name' => 'string-255', 'poster_email' => 'string-255', 'id_member' => 'int', 'poster_time' => 'int',
		),
		array(
			$project['name'], $project['description'],
			$user_info['name'], $user_info['email'], $user_info['id'], time(),
		),
		array('project')
	);
	$context['id_project_inserted'] = $smcFunc['db_insert_id']('{db_prefix}testsuite_projects', 'project');

	if ($copy_level > 0)
	{
		$suite_ids = array();
		$project = TS_loadProject($project_ids);

		if (empty($project['suites']))
		return false;

		else
		{
			foreach ($project['suites'] as $suite)
			{
				$suite_ids[] = $suite['id'];
			}
			TS_copySuite($suite_ids, $copy_level - 1);
		}
	}
}

/**
 * Creates a new empty suite.
 * @global array $user_info
 * @global string $smcFunc
 * @global array $context
 * @param type $suiteOptions
 * @param type $posterOptions
 * @return bool of whether it was successful 
 */
function TS_createSuite(&$suiteOptions, &$posterOptions)
{
	global $user_info, $smcFunc, $context;

	$suiteOptions['id'] = empty($suiteOptions['id']) ? 0 : (int) $suiteOptions['id'];
	$suiteOptions['id_project'] = empty($suiteOptions['id_project']) ? 0 : (int) $suiteOptions['id_project'];
	$posterOptions['id'] = empty($posterOptions['id']) ? 0 : (int) $posterOptions['id'];
	$posterOptions['name'] = $user_info['name'];
	$posterOptions['email'] = $user_info['email'];
	
	// Insert the Suite.
	$smcFunc['db_insert']('',
		'{db_prefix}testsuite_suites',
		array(
			'id_suite' => 'int', 'id_project' =>  'int', 'suite_name' => 'string-255', 'description' => 'string-65534',
			'poster_name' => 'string-255', 'poster_email' => 'string-255', 'id_member' => 'int', 'poster_time' => 'int',
		),
		array(
			$suiteOptions['id'], $suiteOptions['id_project'], $suiteOptions['suite_name'], $suiteOptions['description'],
			$posterOptions['name'], $posterOptions['email'], $posterOptions['id'], time(),
		),
		array('suite')
	);
	$suiteOptions['id'] = $smcFunc['db_insert_id']('{db_prefix}testsuite_suites', 'suite');

	// Something went wrong creating the Suite...
	if (empty($suiteOptions['id']))
		return false;
		
	// Success.
	return true;
}

/**
 * Removes the specified suites.
 * @global string $smcFunc
 * @param type $suite_ids
 * @param type $remove_level is the hierarchy of what gets removed.
		Level 0 will just remove the suite.
		Level 1 will remove the associated suites and the cases that belong to them.
		Level 2 will remove everything including the runs associated to those cases nested in the project hierarchy. 
 */
function TS_removeSuite($suite_ids, $remove_level = 0)
{
	global $smcFunc;

	if (!is_array($suite_ids))
		$suite_ids = array($suite_ids);

	if ($remove_level > 0)
	{
		$case_ids = array();
		foreach ($suite_ids as $id)
		{
			$suite = TS_loadSuite($id);

			if (!empty($suite['cases']))
			{
				foreach ($suite['cases'] as $case)
				{
					$case_ids[] = $case['id'];
				}
			}
		}
		if (!empty($case_ids))
		TS_removeCase($case_ids, $remove_level - 1);
	}

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}testsuite_suites
		WHERE id_suite IN ({array_int:suite_ids})',
		array(
			'suite_ids' => $suite_ids,
		)
	);
}

function TS_copySuite($suite_ids, $copy_level = 0)
{
	global $context, $smcFunc, $user_info;

	if (!is_array($suite_ids))
		$suite_ids = array($suite_ids);

	$request = $smcFunc['db_query']('', '
		SELECT id_suite, suite_name, description
		FROM {db_prefix}testsuite_suites
		WHERE id_suite IN ({array_int:suite_ids})',
		array(
			'suite_ids' => $suite_ids,
		)
	);

	$suites = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$suites[] = array(
			'id_suite' => $row['id_suite'],
			'name' => $row['suite_name'],
			'description' => $row['description'],
		);
	}
	$smcFunc['db_free_result']($request);

	
	foreach ($suites as $key => $suite)
	{
		$smcFunc['db_insert']('',
			'{db_prefix}testsuite_suites',
			array(
				'id_project' =>  'int', 'suite_name' => 'string-255', 'description' => 'string-65534',
				'poster_name' => 'string-255', 'poster_email' => 'string-255', 'id_member' => 'int', 'poster_time' => 'int',
			),
			array(
				$context['id_project_inserted'] , $suite['name'], $suite['description'],
				$user_info['name'], $user_info['email'], $user_info['id'], time(),
			),
			array('suite')
		);
		$suites[$key]['id_suite_inserted'] = $smcFunc['db_insert_id']('{db_prefix}testsuite_suites', 'suite');
	}
	/*print_r($suites);
	die();*/
	if ($copy_level > 0)
	{
		/*foreach($suites as $suite) {
			$case_ids = array();
			$suite_loaded = TS_loadSuite($suite['id_suite']);
	
			if (empty($suite_loaded['cases']))
			return false;
			
			else
			{
				foreach ($suite_loaded['cases'] as $case)
				{
					$case_ids[] = $case['id'];
				}
			}
		}*/
		$cases_data = array();
		foreach($suites as $key => $suite) {
			//print_r($suites[$key]);
			//echo '<br />';

			$suite_loaded = TS_loadSuite($suite['id_suite']);

			//print_r($suite_loaded);
			//echo '<br />';
			if (!empty($suite_loaded['cases'])) {
				//echo 'we got something';
				//echo '<br />';
				foreach ($suite_loaded['cases'] as $case)
				{
					//echo 'id of suite loaded'. $case['id'];
					//echo '<br />';
					$cases_data[$suite['id_suite_inserted']][] = $case['id'];
					//$suites[$key]['case_id'][] = $case['id'];
				}
			} else {
				//echo 'we got nothing';
				//echo '<br />';
				unset($suites[$key]);
			}
			//echo '<br />';
		}
		//print_r($suites);
		//echo '<br />';
		//print_r($case_ids);
		//die();
		TS_copyCase($cases_data, $copy_level - 1);
	}
}

/**
 * Creates a new empty case with given information.
 * @global array $user_info
 * @global string $smcFunc
 * @global array $context
 * @param array $caseOption
 * @param array $posterOptions
 * @return bool of whether the case was created successfully 
 */
function TS_createCase(&$caseOption, &$posterOptions)
{
	global $user_info, $smcFunc, $context, $modSettings;

	$caseOption['id'] = empty($caseOption['id']) ? 0 : (int) $caseOption['id'];
	$caseOption['id_suite'] = empty($caseOption['id_suite']) ? 0 : (int) $caseOption['id_suite'];
	$posterOptions['id'] = empty($posterOptions['id']) ? 0 : (int) $posterOptions['id'];
	$posterOptions['name'] = $user_info['name'];
	$posterOptions['email'] = $user_info['email'];
	$posterOptions['id_assigned'] = empty($posterOptions['id_assigned']) ? '' : $posterOptions['id_assigned'];

	// Insert the Test Case.
	$smcFunc['db_insert']('',
		'{db_prefix}testsuite_cases',
		array(
			'id_case' => 'int', 'id_suite' => 'int', 'case_name' => 'string-255', 'description' => 'string-65534', 'steps' => 'string-65534',
			'expected_result' => 'string-65534',
			'poster_name' => 'string-255', 'poster_email' => 'string-255', 'id_member' => 'int', 'poster_time' => 'int', 'id_assigned' => 'string',
		),
		array(
			$caseOption['id'], $caseOption['id_suite'], $caseOption['case_name'], $caseOption['description'], $caseOption['steps'],
			$caseOption['expected_result'],
			$posterOptions['name'], $posterOptions['email'], $posterOptions['id'], time(), $posterOptions['id_assigned'],
		),
		array('case')
	);
	$caseOption['id'] = $smcFunc['db_insert_id']('{db_prefix}testsuite_cases', 'case');

	// Something went wrong creating the Test Case...
	if (empty($caseOption['id']))
		return false;

	// Send mail to all members who are assigned test cases
	if (!empty($posterOptions['id_assigned']))
	{
		$members = explode(',', $posterOptions['id_assigned']);
		require_once($sourcedir . '/Subs-Post.php');
		$request = $smcFunc['db_query']('', '
		SELECT email_address, lngfile, email_address
		FROM {db_prefix}members
		WHERE id_member IN ({array_int:members})',
			array(
				'members' => $members,
			)
		);
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			//Right now just a test mail
			sendmail($row['email_address'], $caseOption['case_name'], $caseOption['case_name'], $row['email_address'], null, false, 2);
		}
		$smcFunc['db_free_result']($request);
	}

	// Success.
	return true;
}

/**
 * Removes the specified case.
 * @global string $smcFunc
 * @param type $case_ids
 * @param type $remove_level is the hierarchy of what gets removed.
	Level 0 will just remove the case.
	Level 1 will remove the runs that belong to the case. 
 */
function TS_removeCase($case_ids, $remove_level = false)
{
	global $smcFunc;

	if (!is_array($case_ids))
		$case_ids = array($case_ids);

	if ($remove_level > 0)
	{
		$run_ids = array();
		foreach ($case_ids as $id)
		{
			$case = TS_loadCase($id);

			if (!empty($case['runs']))
			{
				foreach ($case['runs'] as $case)
				{
					$run_ids[] = $case['id'];
				}
			}
		}
		if (!empty($run_ids))
		TS_removeRun($run_ids);
	}

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}testsuite_cases
		WHERE id_case IN ({array_int:case_ids})',
		array(
			'case_ids' => $case_ids,
		)
	);
}


function search($array, $value)
{

    if (is_array($array))
    {
        foreach ($array as $arrkey => $subarray)
	if (in_array($value, $subarray)) {
		return $arrkey;
	}
    }
}

function TS_copyCase($casesData, $copy_level = 0)
{
	global $context, $smcFunc, $user_info;

	if (!is_array($casesData))
		$casesData = array($casesData);

	$case_ids = array();
	foreach($casesData as $key => $caseData) {
		foreach($caseData as $key1 => $data){
			//echo 'key: ' . $key1 . '<br />';
			//echo 'value: ' . $data . '<br />';
			$case_ids[] = $data;
		}
	}
	//print_r($case_ids);
	//die();

	$request = $smcFunc['db_query']('', '
		SELECT id_case, case_name, description, steps, expected_result, id_assigned
		FROM {db_prefix}testsuite_cases
		WHERE id_case IN ({array_int:case_ids})',
		array(
			'case_ids' => $case_ids,
		)
	);

	$cases = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$cases[] = array(
			'id_case' => $row['id_case'],
			'name' => $row['case_name'],
			'description' => $row['description'],
			'steps' => $row['steps'],
			'expected_result' => $row['expected_result'],
			'id_assigned' => $row['id_assigned'],
		);
	}
	$smcFunc['db_free_result']($request);

	//print_r($casesData);
	//echo '<br />';
	//print_r($cases);
	//echo '<br />';
	//die();
	
	foreach ($cases as $case)
	{
		$id_inserted = search($casesData, $case['id_case']);

		//echo $id_inserted;
		//echo '<br />';
		//echo $case['id_case'];
		//die();
		$smcFunc['db_insert']('',
			'{db_prefix}testsuite_cases',
			array(
				'id_suite' => 'int', 'case_name' => 'string-255', 'description' => 'string-65534', 'steps' => 'string-65534',
				'expected_result' => 'string-65534',
				'poster_name' => 'string-255', 'poster_email' => 'string-255', 'id_member' => 'int', 'poster_time' => 'int', 'id_assigned' => 'string',
			),
			array(
				$id_inserted, $case['name'], $case['description'], $case['steps'],
				$case['expected_result'],
				$user_info['name'], $user_info['email'], $user_info['id'], time(), $case['id_assigned'],
			),
			array('case')
		);
		$context['id_case_inserted'] = $smcFunc['db_insert_id']('{db_prefix}testsuite_cases', 'case');

		/*if ($copy_level > 0)
		{
			$run_ids = array();
			$case_loaded = TS_loadCase($case['id_case']);

			if (empty($case_loaded['runs']))
			return false;

			else
			{
				foreach ($case_loaded['runs'] as $run)
				{
					$run_ids[] = $run['id'];
				}
				TS_copyRun($run_ids);
			}
		}*/
	}
}

/**
 * Creates a run from specified information.
 * @global array $user_info
 * @global string $smcFunc
 * @global array $context
 * @param type $runOption is the information that the new run will store
 * @param type $posterOptions is the information about the person making the run
 * @return bool of whether the run was created successfully 
 */
function TS_createRun(&$runOption, &$posterOptions)
{
	global $user_info, $smcFunc, $context;

	$runOption['id'] = empty($runOption['id']) ? 0 : (int) $runOption['id'];
	$runOption['id_case'] = empty($runOption['id_case']) ? 0 : (int) $runOption['id_case'];
	$posterOptions['id'] = empty($posterOptions['id']) ? 0 : (int) $posterOptions['id'];
	$posterOptions['name'] = $user_info['name'];
	$posterOptions['email'] = $user_info['email'];
	
	// Insert the Run.
	$smcFunc['db_insert']('',
		'{db_prefix}testsuite_runs',
		array(
			'id_run' => 'int', 'id_case' => 'int', 'result_achieved' => 'string-255', 'feedback' => 'string-65534', 'id_bug' => 'int',
			'poster_name' => 'string-255', 'poster_email' => 'string-255', 'id_member' => 'int', 'poster_time' => 'int',
		),
		array(
			$runOption['id'], $runOption['id_case'], $runOption['result_achieved'], $runOption['feedback'], $runOption['id_bug'],
			$posterOptions['name'], $posterOptions['email'], $posterOptions['id'], time(),
		),
		array('run')
	);
	$runOption['id'] = $smcFunc['db_insert_id']('{db_prefix}testsuite_runs', 'run');

	// Something went wrong creating the Run...
	if (empty($runOption['id']))
		return false;
		
	// Success.
	return true;
}

/**
 * Removes the associated runs.
 * @global string $smcFunc
 * @param array $run_ids the runs to remove by ID
 */
function TS_removeRun($run_ids)
{
	global $smcFunc;

	if (!is_array($run_ids))
		$run_ids = array($run_ids);

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}testsuite_runs
		WHERE id_run IN ({array_int:run_ids})',
		array(
			'run_ids' => $run_ids,
		)
	);
}

function TS_copyRun($run_ids)
{
	global $context, $smcFunc, $user_info;

	if (!is_array($run_ids))
		$run_ids = array($run_ids);

	$request = $smcFunc['db_query']('', '
		SELECT id_run, result_achieved, feedback, id_bug
		FROM {db_prefix}testsuite_runs
		WHERE id_run IN ({array_int:run_ids})',
		array(
			'run_ids' => $run_ids,
		)
	);

	$runs = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$runs[] = array(
			'id_run' => $row['id_run'],
			'result_achieved' => $row['result_achieved'],
			'feedback' => $row['feedback'],
			'id_bug' => $row['id_bug'],
		);
	}
	$smcFunc['db_free_result']($request);

	foreach ($runs as $run)
	{
		$smcFunc['db_insert']('',
			'{db_prefix}testsuite_runs',
			array(
				'id_case' => 'int', 'result_achieved' => 'string-255', 'feedback' => 'string-65534', 'id_bug' => 'int',
				'poster_name' => 'string-255', 'poster_email' => 'string-255', 'id_member' => 'int', 'poster_time' => 'int',
			),
			array(
				$context['id_case_inserted'], $run['result_achieved'], $run['feedback'], $run['id_bug'],
				$user_info['name'], $user_info['email'], $user_info['id'], time(),
			),
			array('run')
		);
		$context['id_run_inserted'] = $smcFunc['db_insert_id']('{db_prefix}testsuite_runs', 'run');
	}
}

/**
 * Modifies the project with specified data.
 * @global array $user_info
 * @global string $smcFunc
 * @global array $context
 * @param type $projectOptions is the information about the project to update
 * @param type $posterOptions
 * @return bool that the project was created successful
 */
function TS_modifyProject(&$projectOptions, &$posterOptions)
{
	global $user_info, $smcFunc, $context;

	// This is longer than it has to be, but makes it so we only set/change what we have to.
	$project_columns = array();
	if (isset($projectOptions['project_name']))
		$project_columns['project_name'] = $projectOptions['project_name'];
	if (isset($projectOptions['description']))
		$project_columns['description'] = $projectOptions['description'];

	if (!empty($projectOptions['modify_time']))
	{
		$project_columns['modified_time'] = $projectOptions['modify_time'];
		$project_columns['modified_by'] = $projectOptions['modified_by'];
	}

	// Which columns need to be ints?
	$projectInts = array('modified_time');
	$update_parameters = array(
		'id_project' => $projectOptions['id'],
	);

	foreach ($project_columns as $var => $val)
	{
		$project_columns[$var] = $var . ' = {' . (in_array($var, $projectInts) ? 'int' : 'string') . ':var_' . $var . '}';
		$update_parameters['var_' . $var] = $val;
	}

	// Nothing to do?
	if (empty($project_columns))
		return true;

	// Change the post.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}testsuite_projects
		SET ' . implode(', ', $project_columns) . '
		WHERE id_project = {int:id_project}',
		$update_parameters
	);

	return true;
}

/**
 * Modify the specified suite with specified information.
 * @global array $user_info
 * @global string $smcFunc
 * @global array $context
 * @param type $suiteOptions is the information about the suite to update
 * @param type $posterOptions
 * @return type 
 */
function TS_modifySuite(&$suiteOptions, &$posterOptions)
{
	global $user_info, $smcFunc, $context;

	// This is longer than it has to be, but makes it so we only set/change what we have to.
	$suite_columns = array();
	if (isset($suiteOptions['suite_name']))
		$suite_columns['suite_name'] = $suiteOptions['suite_name'];
	if (isset($suiteOptions['description']))
		$suite_columns['description'] = $suiteOptions['description'];

	if (!empty($suiteOptions['modify_time']))
	{
		$suite_columns['modified_time'] = $suiteOptions['modify_time'];
		$suite_columns['modified_by'] = $suiteOptions['modified_by'];
	}

	// Which columns need to be ints?
	$suiteInts = array('modified_time');
	$update_parameters = array(
		'id_suite' => $suiteOptions['id'],
	);

	foreach ($suite_columns as $var => $val)
	{
		$suite_columns[$var] = $var . ' = {' . (in_array($var, $suiteInts) ? 'int' : 'string') . ':var_' . $var . '}';
		$update_parameters['var_' . $var] = $val;
	}

	// Nothing to do?
	if (empty($suite_columns))
		return true;

	// Change the post.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}testsuite_suites
		SET ' . implode(', ', $suite_columns) . '
		WHERE id_suite = {int:id_suite}',
		$update_parameters
	);

	return true;
}

/**
 * Modify the specified case with the specified information.
 * @global array $user_info
 * @global string $smcFunc
 * @global array $context
 * @param type $caseOptions is the information about the case to update
 * @param type $posterOptions
 * @return bool that the case was modified successfully 
 */
function TS_modifyCase(&$caseOptions, &$posterOptions)
{
	global $user_info, $smcFunc, $context;

	// This is longer than it has to be, but makes it so we only set/change what we have to.
	$case_columns = array();
	if (isset($caseOptions['case_name']))
		$case_columns['case_name'] = $caseOptions['case_name'];
	if (isset($caseOptions['description']))
		$case_columns['description'] = $caseOptions['description'];
	if (isset($caseOptions['steps']))
		$case_columns['steps'] = $caseOptions['steps'];
	if (isset($caseOptions['expected_result']))
		$case_columns['expected_result'] = $caseOptions['expected_result'];
	if (isset($posterOptions['id_assigned']))
		$case_columns['id_assigned'] = $posterOptions['id_assigned'];

	if (!empty($caseOptions['modify_time']))
	{
		$case_columns['modified_time'] = $caseOptions['modify_time'];
		$case_columns['modified_by'] = $caseOptions['modified_by'];
	}

	// Which columns need to be ints?
	$caseInts = array('modified_time');
	$update_parameters = array(
		'id_case' => $caseOptions['id'],
	);

	foreach ($case_columns as $var => $val)
	{
		$case_columns[$var] = $var . ' = {' . (in_array($var, $caseInts) ? 'int' : 'string') . ':var_' . $var . '}';
		$update_parameters['var_' . $var] = $val;
	}

	// Nothing to do?
	if (empty($case_columns))
		return true;

	// Change the post.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}testsuite_cases
		SET ' . implode(', ', $case_columns) . '
		WHERE id_case = {int:id_case}',
		$update_parameters
	);

	return true;
}

/**
 * Modifies the specified run with the specifed data.
 * @global array $user_info
 * @global string $smcFunc
 * @global array $context
 * @param type $runOptions is the information about the run to update
 * @param type $posterOptions
 * @return bool that the modification was successful 
 */
function TS_modifyRun(&$runOptions, &$posterOptions)
{
	global $user_info, $smcFunc, $context;

	// This is longer than it has to be, but makes it so we only set/change what we have to.
	$run_columns = array();
	if (isset($runOptions['result_achieved']))
		$run_columns['result_achieved'] = $runOptions['result_achieved'];
	if (isset($runOptions['feedback']))
		$run_columns['feedback'] = $runOptions['feedback'];
	if (isset($runOptions['id_bug']))
		$run_columns['id_bug'] = $runOptions['id_bug'];

	if (!empty($runOptions['modify_time']))
	{
		$run_columns['modified_time'] = $runOptions['modify_time'];
		$run_columns['modified_by'] = $runOptions['modified_by'];
	}

	// Which columns need to be ints?
	$runInts = array('modified_time');
	$update_parameters = array(
		'id_run' => $runOptions['id'],
	);

	foreach ($run_columns as $var => $val)
	{
		$run_columns[$var] = $var . ' = {' . (in_array($var, $runInts) ? 'int' : 'string') . ':var_' . $var . '}';
		$update_parameters['var_' . $var] = $val;
	}

	// Nothing to do?
	if (empty($run_columns))
		return true;

	// Change the post.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}testsuite_runs
		SET ' . implode(', ', $run_columns) . '
		WHERE id_run = {int:id_run}',
		$update_parameters
	);

	return true;
}

/**
 * @todo Load permissions from database
 * @global array $context
 * @global string $sourcedir
 * @global array $modSettings used for loading custom file for permissions
 * @return array representation of the permissions set 
 */
function TS_load_permissions($level_name = false, $id_level = false)
{
	global $context, $smcFunc, $sourcedir, $modSettings, $user_info;

	$perms = array(
		// Guests and regular members.
		'view_all' => false,
		'manage_all' => false,
		'postruns_all' => false,
		// Test for whether the user belongs to a group that can submit runs for various test cases.
		'postruns_suite' => false,
		'postruns_proj' => false,
		// Manage permission includes: run creation/modification/deletion, suite and case creating/modifying/deleting/moving
		'manage_project' => false,
		'manage_suite' => false,
	);
	// Leaving the original code intact for future reference
	/*$request = $smcFunc['db_query']('', '
		SELECT p.id_group, p.permission, p.id_suite, p.id_project
		FROM {db_prefix}testsuite_permissions as p
		WHERE p.id_group IN ({array_int:id_group})',
		array(
				'id_group' => $user_info['groups'],
		)
	);
	
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		if (empty($row['id_suite']) && empty($row['id_project']))
		{
			$perms[$row['permission']] = true;
		}
		elseif (!empty($row['id_suite']))
		{
			$perms[$row['permission']][$row['id_suite']] = true;
		}
		else
		{
			$perms[$row['permission']][$row['id_project']][] = false;
		}
	}
		
	$smcFunc['db_free_result']($request);*/

	//Simple load all the permissions for now
	$request = $smcFunc['db_query']('', '
		SELECT p.id_group, p.permission, p.id_level, p.level_name
		FROM {db_prefix}testsuite_permissions as p ' . ($level_name ? '
		WHERE p.level_name = {string:level_name}' : '') . ($id_level ? '
		AND p.id_level = {int:id_level}' : '') .'
		ORDER BY permission ASC',
		array(
				'level_name' => $level_name,
				'id_level' => $id_level,
		)
	);

	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$perms[$row['permission']][$row['id_group']] = array(
			'id_group' => $row['id_group'],
			'permission' => $row['permission'],
			'id_level' => $row['id_level'],
			'level_name' => $row['level_name'],
		);
	}
	$smcFunc['db_free_result']($request);

	return $perms;
}

/**
 * Determines whether a user can do the specified permission.
 * @global array $context
 * @global array $user_info
 * @param type $action
 * @param mixed $secondary is additional info that may be needed for the permission check
 * @return bool whether the user could perform the specified action  
 */
function TS_can_do($action, $secondary = 0)
{
	global $context, $user_info;
	/**
	 * @todo Remove ignore permissions option
	 */
	if ($context['user']['is_admin'])
	{
		return true;
	}
	
	// Grab the groups that can do...
	if (!empty($context['test_suite']['perms']))
	{
		// Is this a complex test?
		if (!empty($secondary))
		{
			if ($action == 'postrun')
			{
				return TS_util_postrunperm($secondary);
			}
			elseif (isset($context['test_suite']['perms'][$action][$secondary]))
			{
				return $context['test_suite']['perms'][$action][$secondary];
			}
			// A secondary is set, but an invalid $action was given.
			else
			{
				return false;
			}
		}
		elseif (isset($context['test_suite']['perms'][$action]))
		{
			return $context['test_suite']['perms'][$action];
		}
	}
	// Something happened...no permissions found.
	return false;
}

/**
 * Used to bundle all checks for posting a run into one definitive conditional.
 * @global array $context
 * @param array $k is used for receiving the suite id and project id
 * @return bool whether the user post a run or not
 */
function TS_util_postrunperm($k)
{
	global $context;
	
	// What set of permissions to work with.
	$perm = $context['test_suite']['perms'];
	
	/**
	 * @todo May want to consider checking if the $k's are set, or just force
	 *		them to be specified.
	 */
	
	// Way too long conditional.
	return ($perm['postruns_all'] || $perm['manage_all'] || $perm['postruns_proj'][$k['id_proj']] 
			|| $perm['postruns_suite'][$k['id_suite']] || $perm['manage_project'][$k['id_proj']] || $perm['manage_suite'][$k['id_suite']]);
}

function TS_updatePermissions($perms)
{
	global $context, $smcFunc;

	// Pick up all the ID's of selected levels
	if (empty($context['test_suite']['database']['id_level']))
	{
		if ($context['test_suite']['database']['level_name'] == 'projects')
		{
			$request = $smcFunc['db_query']('', '
				SELECT id_project
				FROM {db_prefix}testsuite_projects'
			);

			$levels = array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$levels[] = $row['id_project'];
			}
			$smcFunc['db_free_result']($request);
		}
		elseif ($context['test_suite']['database']['level_name'] == 'suites')
		{
			$request = $smcFunc['db_query']('', '
				SELECT id_suite
				FROM {db_prefix}testsuite_suites'
			);

			$levels = array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$levels[] = $row['id_suite'];
			}
			$smcFunc['db_free_result']($request);
		}
		elseif ($context['test_suite']['database']['level_name'] == 'cases')
		{
			$request = $smcFunc['db_query']('', '
				SELECT id_case
				FROM {db_prefix}testsuite_cases'
			);

			$levels = array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$levels[] = $row['id_case'];
			}
			$smcFunc['db_free_result']($request);
		}
		elseif ($context['test_suite']['database']['level_name'] == 'runs')
		{
			$request = $smcFunc['db_query']('', '
				SELECT id_run
				FROM {db_prefix}testsuite_runs'
			);

			$levels = array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$levels[] = $row['id_run'];
			}
			$smcFunc['db_free_result']($request);
		}

		// If there is nothing in concerned level skip the whole thing
		if (!empty($levels))
		{

			foreach ($perms as $key => $val)
			{
				TS_clearPermissions($key, $context['test_suite']['database']['level_name']);
				foreach ($levels as $level)
				{
					//array keyword is used to make 'val' an array in some wierd cases
					foreach ((array)$val as $group)
					{
						$smcFunc['db_insert']('',
							'{db_prefix}testsuite_permissions',
							array(
								'id_group' => 'int',
								'permission' => 'string',
								'id_level' => 'int',
								'level_name' => 'string',
							),
							array(
								(int) $group, $key, $level, $context['test_suite']['database']['level_name'],
							),
							array()
						);
					}
				}
			}
		}
	}
	else
	{
		foreach ($perms as $key => $val)
		{
			TS_clearPermissions($key, $context['test_suite']['database']['level_name'], $context['test_suite']['database']['id_level']);

			//array keyword is used to make 'val' an array in some wierd cases
			foreach ((array)$val as $group)
			{
				$smcFunc['db_insert']('',
					'{db_prefix}testsuite_permissions',
					array(
						'id_group' => 'int',
						'permission' => 'string',
						'id_level' => 'int',
						'level_name' => 'string',
					),
					array(
						(int) $group, $key, $context['test_suite']['database']['id_level'], $context['test_suite']['database']['level_name'],
					),
					array()
				);
			}
		}
	}
}

function TS_clearPermissions($perm, $level_name, $id_level = false)
{
	global $smcFunc;

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}testsuite_permissions
		WHERE permission = {string:permission}
		AND level_name = {string:level_name}' . ($id_level ? '
		AND id_level = {int:id_level}' : '') .'',
		array(
			'permission' => $perm,
			'level_name' => $level_name,
			'id_level' => $id_level,
		)
	);
}

function TS_simple_GetProjects()
{
	global $context, $smcFunc;
	
	$request = $smcFunc['db_query']('', '
		SELECT id_project, project_name
		FROM {db_prefix}testsuite_projects');

	if ($smcFunc['db_num_rows']($request) == 0)
		return;

	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$projectos[$row['id_project']] = $row['project_name'];
	}
	$smcFunc['db_free_result']($request);

	return $projectos;
}

function TS_load_user_Project()
{
	global $context, $smcFunc, $user_info;
	
	$request = $smcFunc['db_query']('', '
		SELECT id_project
		FROM {db_prefix}members
		WHERE id_member = {int:current_member}
		LIMIT 1',
		array(
			'current_member' => $user_info['id'],
		)
	);
	if ($smcFunc['db_num_rows']($request) == 0)
		return;
	list ($project_selected) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	return $project_selected;
}

function TS_Validator($type)
{
	global $context, $modSettings, $smcFunc, $txt, $memberContext;

		if (empty($type))
			fatal_error("");
	
		if (!empty($context['post_error']))
		{
			// Validate inputs.
			if (empty($context['post_error']))
			{
				// Project validation.
				if ($type == 'project')
				{
					if (htmltrim__recursive(htmlspecialchars__recursive($_POST['name'])) == '')
						$context['post_error']['no_name'] = true;
					if (htmltrim__recursive(htmlspecialchars__recursive($_POST['description'])) == '')
						$context['post_error']['no_description'] = true;
					if (!empty($modSettings['max_messageLength']) && $smcFunc['strlen']($_POST['description']) > $modSettings['max_messageLength'])
						$context['post_error']['long_description'] = true;
				}
				
				// Case Validation.
				elseif ($type == 'case')
				{	
					if (htmltrim__recursive(htmlspecialchars__recursive($_POST['name'])) == '')
						$context['post_error']['no_name'] = true;
					if (htmltrim__recursive(htmlspecialchars__recursive($_POST['message'])) == '')
						$context['post_error']['no_message'] = true;
					if (htmltrim__recursive(htmlspecialchars__recursive($_POST['steps'])) == '')
						$context['post_error']['no_steps'] = true;
					if (htmltrim__recursive(htmlspecialchars__recursive($_POST['expected_result'])) == '')
						$context['post_error']['no_expected_result'] = true;
					if (!empty($modSettings['max_messageLength']) && ($smcFunc['strlen']($_POST['message']) > $modSettings['max_messageLength'] || $smcFunc['strlen']($_POST['steps']) > $modSettings['max_messageLength'] || $smcFunc['strlen']($_POST['expected_result']) > $modSettings['max_messageLength']))
						$context['post_error']['long_message'] = true;
				}
				elseif ($type == 'run')
				{
					if ($_POST['result'] == 'select')
						$context['post_error']['no_result'] = true;
					if (htmltrim__recursive(htmlspecialchars__recursive($_POST['feedback'])) == '')
						$context['post_error']['no_feedback'] = true;
					if ($smcFunc['strlen']($_REQUEST['feedback']) > $modSettings['max_messageLength'])
						$context['post_error']['long_message'] = true;
				}
			}
			else
			{
				if ($type == 'project')
				{
					if (!isset($_POST['name']))
						$_POST['name'] = '';
					if (!isset($_POST['description']))
						$_POST['description'] = '';
				}
				elseif ($type == 'case')
				{
					if (!isset($_POST['name']))
						$_POST['name'] = '';
					if (!isset($_POST['message']))
						$_POST['message'] = '';
					if (!isset($_POST['steps']))
						$_POST['steps'] = '';
					if (!isset($_POST['expected_result']))
						$_POST['expected_result'] = '';				
				}
				elseif ($type == 'run')
				{
					if (!isset($_POST['result']))
						$_POST['result'] = '';
					if (!isset($_POST['feedback']))
						$_POST['feedback'] = '';					
				}
			}

			// Set up the inputs for the form.
			if ($type == 'project')
			{
				$name = strtr($smcFunc['htmlspecialchars']($_POST['name']), array("\r" => '', "\n" => '', "\t" => ''));
				$project_description = $smcFunc['htmlspecialchars']($_POST['description'], ENT_QUOTES);
			}
			elseif ($type == 'case')
			{
				$name = strtr($smcFunc['htmlspecialchars']($_POST['name']), array("\r" => '', "\n" => '', "\t" => ''));
				$message = $smcFunc['htmlspecialchars']($_POST['message'], ENT_QUOTES);
				$step = $smcFunc['htmlspecialchars']($_POST['steps'], ENT_QUOTES);
				$expected_result = $smcFunc['htmlspecialchars']($_REQUEST['expected_result'], ENT_QUOTES);
			}
			elseif ($type == 'run')
			{
				$feedback = $smcFunc['htmlspecialchars']($_REQUEST['feedback'], ENT_QUOTES);
			}

			if (isset($name))
			{
				// Make sure the subject isn't too long - taking into account special characters.
				if ($smcFunc['strlen']($name) > 100)
				{
					$name = $smcFunc['substr']($name, 0, 100);
				}

				// Have we inadvertently trimmed off the subject of useful information?
				if ($smcFunc['htmltrim']($name) === '')
				{
					$context['post_error']['no_name'] = true;
				}
			}

			// Any errors occurred?
			if (!empty($context['post_error']))
			{
				loadLanguage('Errors');

				$context['error_type'] = 'minor';

				$context['post_error']['description'] = array();
				foreach ($context['post_error'] as $post_error => $dummy)
				{
					if ($post_error == 'description')
						continue;

					if ($post_error == 'long_description')
						$txt['ts_error_' . $post_error] = sprintf($txt['ts_error_' . $post_error], $modSettings['max_messageLength']);

					$context['post_error']['description'][] = $txt['ts_error_' . $post_error];

					$context['error_type'] = 'serious';
				}
			}

			if (isset($name))
			{
				$context['name'] = addcslashes($name, '"');
			}
			
			if ($type == 'project')
			{
				$context['description'] = str_replace(array('"', '<', '>', '&nbsp;'), array('&quot;', '&lt;', '&gt;', ' '), $project_description);
			}
			elseif ($type == 'case')
			{
				$context['message'] = str_replace(array('"', '<', '>', '&nbsp;'), array('&quot;', '&lt;', '&gt;', ' '), $message);
				$context['steps'] = str_replace(array('"', '<', '>', '&nbsp;'), array('&quot;', '&lt;', '&gt;', ' '), $step);
				$context['expected_result'] = str_replace(array('"', '<', '>', '&nbsp;'), array('&quot;', '&lt;', '&gt;', ' '), $expected_result);
				if (isset($_POST['id_assigned_list']) && is_array($_POST['id_assigned_list']))
				{
					$members = $_POST['id_assigned_list'];

					$request = $smcFunc['db_query']('', '
					SELECT id_member, real_name
					FROM {db_prefix}members
					WHERE id_member IN ({array_int:members})
					LIMIT ' . count($members),
						array(
							'members' => $members,
						)
					);
					while ($row = $smcFunc['db_fetch_assoc']($request))

					$context['id_assigned'][] = array(
						'id' => $row['id_member'],
						'name' => $row['real_name'],
					);
					$smcFunc['db_free_result']($request);
				}
			}
			elseif ($type == 'run')
			{
				$context['result_achieved'] = $_POST['result'];
				$context['feedback'] = str_replace(array('"', '<', '>', '&nbsp;'), array('&quot;', '&lt;', '&gt;', ' '), $feedback);

				if (!empty($id_bug))
				{
					$context['id_bug'] = $id_bug;
				}
			}
			if ($type == 'project')
			{
				checkSubmitOnce('register');
			}
			else
			{
				checkSubmitOnce('free');
			}
		}
}

?>
