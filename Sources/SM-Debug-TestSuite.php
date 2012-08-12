<?php

function debug_code()
{
	global $context, $sourcedir, $txt, $smcFunc;
	// Updating for legacy versions of the mod that do not wish to install through packagae manager.
	// "Easy mode" -  disregards making sure everything works
	//		in favor of trying out the functionality with less chance of running into issues.
	if ($context['test_suite']['debug'] == -1)
	{
		$txt['sm_testsuite'] = 'Test Suite'; // Old versions didn't add this to Modifications.english
		$txt['ts_admin'] = 'Manage Test Suite';
		
		// SM Test suite
		$txt['ts_error_no_name'] = 'Name field was left empty.';
		$txt['ts_error_no_description'] = 'Description field was left empty.';
		$txt['ts_error_no_message'] = 'Description field was left empty.';

		$txt['ts_error_no_steps'] = 'Steps field was left empty.';
		$txt['ts_error_no_expected_result'] = 'Expected Result field was left empty.';
		$txt['ts_error_no_feedback'] = 'Feedback field was left empty.';
		$txt['ts_error_no_result'] = 'Result field was left empty.';
		$txt['ts_error_long_description'] = 'The Description exceeds the maximum allowed length (65534 chracters).';

		$txt['ts_no_project'] = 'Sorry, the Project you are trying to edit doesn\'t exist.';
		$txt['ts_no_suite'] = 'Sorry, the Suite you are trying to edit doesn\'t exist.';
		$txt['ts_no_case'] = 'Sorry, the Case you are trying to edit doesn\'t exist.';
		$txt['ts_no_run'] = 'Sorry, the Run you are trying to edit doesn\'t exist.';
		
		
	}

	// "Strict mode" - makes things harder to work in favor of extensive testing.
	elseif ($context['test_suite']['debug'] == 1)
	{
		// Test permissions with admin account but ignores the automatic admin bypass.
		$context['user']['is_admin'] = false;
	}
	
	// "Print mode" - Prints out debug information.
	elseif ($context['test_suite']['debug'] == 2)
	{
		$context['test_suite']['debug_link'] = '<div style="text-align: center; padding: 10px;"><a style="color: white; font-size: 25px;" 
			href="#" id="show_debug">Show Debug Statements</a></div>';
	}
}

?>
