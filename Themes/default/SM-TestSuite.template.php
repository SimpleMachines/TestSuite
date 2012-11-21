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

function TS_show_header()
{
	global $context, $txt;

	if (!empty($context['test_suite']['debug']))
	{
		echo '
		<div style="background-color: #f7f7f7; border: 1px dashed red; padding: 10px;"><strong>Debug Information Bar: </strong>';
		if (!empty($context['test_suite']['tests']['can_do']))
		{
			echo '<br /><br />
			<em>Permission Checked For This Page:</em> ' . implode(', ', $context['test_suite']['tests']['can_do']) . '';
		}
		echo '
		</div>';
	}

	echo '
	<div id="ts_head">
		<form name="set_project" action="', $context['test_suite']['url'], ';updateproject" method="post">
			<label for="projects">' . $txt['ts_select_project'] . ':</label> 
			<select name="projects" id="projects" onchange="document.forms.set_project.submit();">';
			if (!empty($context['test_suite']['project_list']))
			{
				foreach ($context['test_suite']['project_list'] as $id => $name)
				{
					$url = $context['test_suite']['url'] . ';project=' . $id;

					echo '
						<option value="' . $id . '"' . ($id == $context['test_suite']['project_selected'] ? ' selected="selected"' : '') . '>' . $name . '</option>';
				}
			}
		echo '
			</select>
		</form>
	</div>';
}

function template_main()
{
	global $context, $scripturl, $settings, $txt;

	TS_show_header();

	// Start our template...
	echo ' 
	<div class="cat_bar">
		<h3 class="catbg">', $txt['sm_testsuite_main'], '</h3>
	</div>
	<div class="information">', $txt['sm_testsuite_desc'], '</div>';

	TS_show_buttons();

	// Show info of the projects created so far
	echo '
	<div class="suite_frame">';
	if (empty($context['test_suite']['projects']))
	{
		echo '
		<div class="error">' . $txt['ts_no_loaded_projects'] . '</div>';
	}

	echo '
		<ul>';
	foreach ($context['test_suite']['projects'] as $key => $value)
	{
		echo '
			<li class="header">
				', $value['link'], '
				<div class="floatright">';

				if($value['groups_can_edit']) {
				echo '
					<a class="active" href="', $context['test_suite']['url'], ';editproject=', $value['id'], '">
						<img src="' . $settings['images_url'] . '/buttons/sm_edit.png" alt="', $txt['ts_edit_project'], '" title="', $txt['ts_edit_project'], '" />
					</a>';
				}

				if($value['groups_can_manage']) {
					echo '
					<a class="active" href="', $context['test_suite']['url'], ';copyproject=', $value['id'], '">
						<img src="' . $settings['images_url'] . '/buttons/sm_copy.png" alt="', $txt['ts_copy_project'], '" title="', $txt['ts_copy_project'], '" />
					</a>';
				}

				if($value['groups_can_manage']) {
				echo '
					<a class="active" href="', $context['test_suite']['url'], ';admin=per_level;level_name=project;id_level='. $value['id'] . '">
						<img src="' . $settings['images_url'] . '/buttons/sm_manage.png" alt="', $txt['ts_manage_project'], '" title="', $txt['ts_manage_project'], '" />
					</a>';
				}

				if($value['groups_can_delete']) {
					echo '
					<a class="active" href="', $context['test_suite']['url'], ';removeproject='. $value['id'] . '" onclick="return confirm(\'', $txt['ts_remove_project'], '?\');">
						<img src="' . $settings['images_url'] . '/buttons/sm_delete.png" alt="', $txt['ts_remove_project'], '" title="', $txt['ts_remove_project'], '" />
					</a>';
				}

				echo '
				</div>
			</li>';

		echo '
			<li class="suite_frame_right">', $value['description'], '</li>';

		if (!empty($value['modified_by']))
		{
			echo '
			<li class="modified">
				', $txt['ts_last_edit_by'], ' ', $value['modified_by'], ' ', $txt['at'], ' ', $value['modified_time'], '
			</li>';
		}

		echo '
		<li><hr class="clear" /></li>';
	}
		echo '
		</ul>
	</div>';
}

function template_sm_testsuite_project_view()
{
	global $txt, $scripturl, $suite, $context, $settings;
	// Start our template...

	TS_show_header();

	echo '
	<div class="cat_bar">   
		<h3 class="catbg">', $txt['ts_project_action'], ' <em>', $context['test_suite']['project']['name'], '</em> 
			' . (isset($context['test_suite']['edit_link']) ? $context['test_suite']['edit_link'] : '') . '
		<h3>
	</div>
	<div class="information">', $context['test_suite']['project']['description'], '</div>';

	TS_show_buttons();

	// Show info of the suites created so far
	if (isset($context['test_suite']['project']['suites']))
	{
		echo '
		<div class="suite_frame">';
		if (empty($context['test_suite']['project']['suites']))
		{
			if($context['test_suite']['project']['groups_can_create']) {
				echo '
				<div class="error">' . $txt['ts_no_loaded_suites_create'] . '</div>';
			} else {
				echo '
				<div class="error">' . $txt['ts_no_loaded_suites'] . '</div>';
			}
		}
		foreach ($context['test_suite']['project']['suites'] as $key => $value)
		{
			echo '
			<ul>
				<li class="header">', $value['link'], '
					<div class="floatright">';

					if($value['groups_can_edit']) {
					echo '
						<a class ="active" href="', $context['test_suite']['url'], ';editsuite='. $value['id'] . '">
							<img src="' . $settings['images_url'] . '/buttons/sm_edit.png" alt="', $txt['ts_edit_suite'], '" title="', $txt['ts_edit_suite'], '" />
						</a>';
					}

					if($value['groups_can_manage']) {
						echo '
						<a class="active" href="', $context['test_suite']['url'], ';copysuite=', $value['id'], '">
							<img src="' . $settings['images_url'] . '/buttons/sm_copy.png" alt="', $txt['ts_copy_suite'], '" title="', $txt['ts_copy_suite'], '" />
						</a>';
					}

					if($value['groups_can_manage']) {
					echo '
						<a class="active" href="', $context['test_suite']['url'], ';admin=per_level;level_name=suite;id_level='. $value['id'] . '">
							<img src="' . $settings['images_url'] . '/buttons/sm_manage.png" alt="', $txt['ts_manage_suite'], '" title="', $txt['ts_manage_suite'], '" />
						</a>';
					}

					if($value['groups_can_delete']) {
						echo '
						<a class ="active" href="', $context['test_suite']['url'], ';removesuite='. $value['id'] . '" onclick="return confirm(\'', $txt['ts_remove_suite'], '?\');">
								<img src="' . $settings['images_url'] . '/buttons/sm_delete.png" alt="', $txt['ts_remove_suite'], '" title="', $txt['ts_remove_suite'], '" />
						</a>';
					}

					echo '
					</div>
				</li>';

				echo '
				<li class="text_style">
					<li class="suite_frame_right">', $value['description'], '</li>
				</li>';

				if (empty($value['count']))
				echo '
					<li class="text_style">
						<strong>', $txt['ts_total_run_count'], '</strong>: ', $value['count'], '
					</li>';

				else
				echo '
					<li class="text_style">
						<strong>', $txt['ts_total_run_count'], '</strong>: ', $value['count_link'], '
					</li>';

				if (empty($value['pass_count']))
				echo '
					<li class="text_style">
							<strong>', $txt['ts_pass_count'], '</strong>: ', $value['pass_count'], '
					</li>';

				else
				echo '
					<li class="text_style">
							<strong>', $txt['ts_pass_count'], '</strong>: ', $value['pass_count_link'], '
					</li>';

				if (empty($value['fail_count']))
					echo '
						<li class="text_style">
								<strong>', $txt['ts_fail_count'], '</strong>: ', $value['fail_count'], '
						</li>';
				else
					echo '
						<li class="text_style">
								<strong>', $txt['ts_fail_count'], '</strong>: ', $value['fail_count_link'], '
						</li>';

				if (!empty($value['modified_by']))
				{
					echo '
									<li class="modified">', 'Last edited by', ' ', $value['modified_by'], ' ', $txt['at'], ' ', $value['modified_time'], '</li>';
				}

				echo '
				<li><hr class="clear" /></li>
			</ul>';
		}

		echo '
		</div>';
	}
}

function template_sm_testsuite_case_view()
{
	global $txt, $scripturl, $context, $settings;

	// Start our template...
	TS_show_header();

	echo '
		<div class="cat_bar">   
			<h3 class="catbg">
			', $txt['ts_suite_action'], ' <em>', $context['test_suite']['suite']['link'], '</em>
			' . (isset($context['test_suite']['edit_link']) ? $context['test_suite']['edit_link'] : '') . '
			</h3>
		</div>
		<div class="information">', $context['test_suite']['suite']['description'], '</div>';

	TS_show_buttons();

	// Show info of the test cases created so far
	if (isset($context['test_suite']['suite']['cases']))
	{
		echo '
		<div class="suite_frame">';
		if (empty($context['test_suite']['suite']['cases']))
		{
			
			if($context['test_suite']['suite']['groups_can_create']) {
				echo '
				<div class="error">' . $txt['ts_no_loaded_cases_create'] . '</div>';
			} else {
				echo '
				<div class="error">' . $txt['ts_no_loaded_cases'] . '</div>';
			}
		}
		echo '
				<ul>';
		foreach ($context['test_suite']['suite']['cases'] as $key => $value)
		{
			echo '
					<li class="header">', $value['link'], '
						<div class="floatright">';

						if($value['groups_can_edit']) {
						echo '
							<a class="active" href="', $context['test_suite']['url'], ';editcase='. $value['id'] . '">
								<img src="' . $settings['images_url'] . '/buttons/sm_edit.png" alt="', $txt['ts_edit'], ' ', $txt['ts_case'], '" title="', $txt['ts_edit'], ' ', $txt['ts_case'], '" />
							</a>';
						}

						if($value['groups_can_manage']) {
						echo '
							<a class="active" href="', $context['test_suite']['url'], ';copycase=', $value['id'], '">
								<img src="' . $settings['images_url'] . '/buttons/sm_copy.png" alt="', $txt['ts_copy_case'], '" title="', $txt['ts_copy_case'], '" />
							</a>';
						}

						if($value['groups_can_manage']) {
						echo '
							<a class="active" href="', $context['test_suite']['url'], ';admin=per_level;level_name=case;id_level='. $value['id'] . '">
								<img src="' . $settings['images_url'] . '/buttons/sm_manage.png" alt="', $txt['ts_manage_case'], '" title="', $txt['ts_manage_case'], '" />
							</a>';
						}

						if($value['groups_can_delete']) {
						echo '
							<a class="active" href="', $context['test_suite']['url'], ';removecase='. $value['id'] . '" onclick="return confirm(\'', $txt['ts_remove_case'], '?\');">
								<img src="' . $settings['images_url'] . '/buttons/sm_delete.png" alt="', $txt['ts_remove_case'], '" title="', $txt['ts_remove_case'], '" />
							</a>';
						}

						echo '
						</div>
					</li>';
			echo '
					<li class="suite_frame_right">', $value['description'], '</li>';
			echo '
					<li class="text_style"><strong>', $txt['ts_total_run_count'], '</strong>', ': ', $value['count'], '</li>';
			echo '
					<li class="text_style"><strong>', $txt['ts_pass_count'], '</strong>', ': ', $value['pass_count'], '</li>';
			echo '
					<li class="text_style"><strong>', $txt['ts_fail_count'], '</strong>', ': ', $value['fail_count'], '</li>';
			if (!empty($value['modified_by']))
			{
				echo '
					<li class="modified">', 'Last edited by', ' ', $value['modified_by'], ' ', $txt['ts_at'], ' ', $value['modified_time'], '</li>';
			}
			echo '
					<li class="bottom_margin"><hr class="clear" /></li>';
			}

		echo '
				</ul>
		</div>';
	}
}

function template_sm_testsuite_separate_case_view()
{
	global $txt, $scripturl, $context, $run, $settings;

	// Start our template...
	TS_show_header();

	// Time to get boxes in places :P, div power. Show all infomation regarding to this specific test case
	echo '
	<div class="cat_bar">
		<h3 class="catbg">
		', $txt['ts_case_action'], ' <em>', $context['test_suite']['case']['name'], '</em>
		' . (isset($context['test_suite']['edit_link']) ? $context['test_suite']['edit_link'] : '') . '
		</h3>
	</div>';
	echo '
	<div class="suite_frame">';

	// If a run was just deleted.
	if (isset($context['test_suite']['alerts']['deleted']))
	{
		echo '
		<div id="delete-notify">', $txt['ts_delete_run'], '</div>';
	}

	echo '
		<ul>';
	echo '
		<li class="details">
				<strong>', $txt['ts_project_id'], '</strong>: ', $context['test_suite']['case']['id_project'], '
		</li>';

	echo '
		<li class="details">
				<strong>', $txt['ts_suite_id'], '</strong>: ', $context['test_suite']['case']['id_suite'], '
		</li>';

	echo '
		<li class="details">
				<strong>', $txt['ts_case_id'], '</strong>: ', $context['test_suite']['case']['id'], '
		</li>';

	if (!empty($context['test_suite']['case']['id_assigned']))
	{
		echo '
		<li class="details">
			<strong>', $txt['ts_assigned_to'], ': ' , '</strong>';
			foreach ($context['test_suite']['case']['id_assigned'] as $assigned => $member)
			{
				echo $member['link'], ' ';
			}
		echo '
		</li>';
	}

	echo '
		<li class="bottom_margin"><hr class="clear" /></li>';

	echo '
		<li class="details">
			<strong>', $txt['ts_case_name'], '</strong>
		</li><li><hr class="clear" /></li>';

	echo '
		<li class="suite_frame_right">', $context['test_suite']['case']['name'], '</li>
		<li class="bottom_margin"><hr class="clear" /></li>';

	echo '
		<li class="details">
			<strong>', $txt['ts_description'], '</strong>
		</li><li><hr class="clear" /></li>';

	echo '
			<li class="suite_frame_right">',  $context['test_suite']['case']['description'], '</li>
			<li class="bottom_margin"><hr class="clear" /></li>';

	echo '
		<li class="details">
					<strong>', $txt['ts_steps'], '</strong>
		</li><li><hr class="clear" /></li>';

	echo '
		<li class="suite_frame_right">',  $context['test_suite']['case']['steps'], '</li>
		<li class="bottom_margin"><hr class="clear" /></li>';

	echo '
		<li class="details">
				<strong>', $txt['ts_expected_result'], '</strong>
		</li><li><hr class="clear" /></li>';

	echo '
		<li class="suite_frame_right">',  $context['test_suite']['case']['expected_result'], '</li>
		<li><hr class="clear" /></li>';

	echo '
		</ul>
		</div>';

		TS_show_buttons();
		
		// Show info of the run feedback regarding to this test case
		if (!empty($context['test_suite']['case']['runs']))
		{
			echo '
			<div class="cat_bar">
				<h3 class="catbg">', $txt['ts_run_list'], '</h3>
			</div>';
			echo '
			<div class="suite_frame">';

			echo '
				<ul>';

			foreach ($context['test_suite']['case']['runs'] as $key => $value)
			{
				echo '
				<li class="header ts_smaller" id="run'. $key . '">
					', $txt['ts_ran_by'], '', ' - ', $value['member']['link'], '
					<div class="floatright">
						<a class="active" href="', $context['test_suite']['url'], ';editrun='. $value['id'] . '">
							<img src="' . $settings['images_url'] . '/buttons/sm_edit.png" alt="', $txt['ts_edit_run'], '" title="', $txt['ts_edit_run'], '" />
						</a>
						<a class="active" href="', $context['test_suite']['url'], ';removerun='. $value['id'] . '" onclick="return confirm(\'', $txt['ts_delete_run'], '?\');">
							<img src="' . $settings['images_url'] . '/buttons/sm_delete.png" alt="', $txt['ts_delete_run'], '" title="', $txt['ts_delete_run'], '" />
						</a>
					</div>
				</li>';

				echo '
				<li><a id="run', $value['id'], '"></a></li>';

				// Temp hack?
				global $smcFunc;
				$value['result_achieved'] = $smcFunc['ucfirst']($value['result_achieved']);

				echo '
				<li class="', $value['result_achieved'] == 'fail' ? 'text_style red' : 'text_style','">
					<strong>', $txt['ts_run_id'], '</strong>', ' - ', '
					<a style="', $value['result_achieved'] == 'fail' ? 'color:#ff0000;' : '','" href="', $value['href'], '">', $value['id'], '</a>
				</li>
				<li class="text_style">
						<strong>', $txt['ts_time_submitted'], '</strong> - ', $value['time'], '</strong>
				</li>
				<li class="', $value['result_achieved'] == 'fail' ? 'text_style red' : 'text_style','">
						<strong>', $txt['ts_run_result'], '</strong>', ' - ', $value['result_achieved'], '
				</li>';

				echo '
				<li class="text_style">
						<strong>', $txt['ts_feedback'], ':</strong>
				</li>
				<li class="suite_frame_right">', $value['feedback'], '</li>';

				if ($value['id_bug'] != 0)
				{
					echo '
				<li class="text_style">
						<strong>', $txt['ts_id_bug'], '</strong>', ' - ', $value['id_bug'], '
				</li>';
				}
				if (!empty($value['modified_by']))
				{
					echo '
				<li class="modified">', $txt['ts_last_edit_by'], ' ', $value['modified_by'], ' ', $txt['ts_at'], ' ', $value['modified_time'], '</li>';
				}

				echo '
				<li class="bottom_margin"><hr class="clear" /></li>';
			}
		echo '
		</ul>
		</div>';
		}

	// Shhh...maybe if nobody notices this, it can just stay here!
	// Joker has noticed this and going to make a neat write up for this
	echo '
	<script type="text/javascript">
		run = location.href.substr(location.href.indexOf("#") + 1);
		runli = document.getElementById("" + run);
		if (runli != null)
			runli.className = runli.className + " active_run";
	</script>';
}

function template_sm_testsuite_count_link()
{
	global $txt, $scripturl, $context;

	// Start our template...

	echo '
	<div class="cat_bar">   
		<h3 class="catbg">', $context['test_suite']['suite']['link'], '</h3>
	</div>
	<div class="information">', $context['test_suite']['suite']['description'], '</div>';

	echo '
	<div class="buttons">
		<ul>
		</ul>
	</div>';

	// Show info of the test cases created so far
	echo'
	<div class="suite_frame">
		<ul>';

		foreach ($context['test_suite']['run_links'] as $key => $value)
		{
			echo '
			<li class="text_style"><strong>', $txt['ts_run_id'], '</strong>: ', $value['id_run'], ' ', $value['run_link'], '</li>
			<li class="text_style"><strong>', $txt['ts_case_name'], '</strong>: ', $value['case_link'], '</li>
			<li><hr class="clear" /></li>';
		}

		echo '
		</ul>
	</div>';
}

// Let make some creative(ohh creation functions :P).
function template_create_project()
{
	global $context, $txt, $scripturl;

	TS_show_header();

	echo '
	<form action="', $context['test_suite']['url'], ';createproject2" method="post">';

	// Start the main table.
	echo '
		<div id="ts_post" class="cat_bar">
				<h3 class="catbg">', $context['page_title'], '</h3>
		</div>
		<div>
			<span class="upperframe"><span></span></span>
			<div class="roundframe">';
		
	// If an error occurred, explain what happened.
	echo '
				<div class="errorbox"', empty($context['post_error']['description']) ? ' style="display: none"' : '', ' id="errors">
					<dl>
						<dt>
							<strong style="', empty($context['error_type']) || $context['error_type'] != 'serious' ? 'display: none;' : '', '" id="error_serious">', (empty($context['test_suite']['current_project']) ? $txt['ts_error_while_creating_project'] : $txt['ts_error_while_creating_suite']), '</strong>
						</dt>
						<dt class="error" id="error_list">
							', empty($context['post_error']['description']) ? '' : implode('<br />', $context['post_error']['description']), '
						</dt>
					</dl>
				</div>';

	// The post header... important stuff
	echo '
				<dl id="post_header">';

	// Show a box so that user can write the Project/Suite name
	echo '
				<dt>
					<label for="name">
						<span', isset($context['post_error']['name']) ? ' class="error"' : '', ' id="caption_subject">', $txt['ts_name'], ':</span>
					</label>
				</dt>
				<dd>
					<input type="text" name="name" id="name"', isset($context['name']) ? ' value="' . $context['name'] . '"' : '', ' tabindex="', $context['tabindex']++, '" size="80" maxlength="80" class="input_text" />
				</dd>';
								
	echo'
				</dl><hr class="clear" />';

	// Write description related to Project/Suite
	echo '
				<div class="textbox">
					<div class="text">
						<p>
							<label for="description">', $txt['ts_description'], ':</label>
						</p>
					</div>
						<textarea class="editor" name="description" id="description" rows="20" cols="600" tabindex="', $context['tabindex']++, '" style="width: 70%; height: 150px; ', isset($context['post_error']['no_description']) || isset($context['post_error']['long_description']) ? 'border: 1px solid red;' : '', '">', isset($context['description']) ? $context['description'] : '', '</textarea>
				</div>';
	
	// Ohh take the Project ID if we're using this function for creation of suites
	echo'
				<input type="hidden" name="proj" value="', $context['test_suite']['current_project'] , '" />';

	// Finally, the submit button.
	echo '
				<br />
				<p class="righttext">
					<input type="submit" value="', $txt['ts_create'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />
				</p>
			</div>
			<span class="lowerframe"><span></span></span>
		</div>
		<br class="clear" />
	</form>';
}

// Just to make things less complicated one more function for level 3 & 4
function template_create_case()
{
	global $context, $txt, $scripturl, $settings;

	TS_show_header();

	echo '
		<form action="', $context['test_suite']['url'], ';createcase2" method="post">';

	// Start the main table.
	echo '
			<div id="ts_post" class="cat_bar">
				<h3 class="catbg">', $context['page_title'], '</h3>
			</div>
			<div>
				<span class="upperframe"><span></span></span>
				<div class="roundframe">';

	// If an error occurred, explain what happened.
	echo '
				<div class="errorbox"', empty($context['post_error']['description']) ? ' style="display: none"' : '', ' id="errors">
					<dl>
						<dt>
							<strong style="', empty($context['error_type']) || $context['error_type'] != 'serious' ? 'display: none;' : '', '" id="error_serious">', $txt['ts_error_while_creating_case'], '</strong>
						</dt>
						<dt class="error" id="error_list">
							', empty($context['post_error']['description']) ? '' : implode('<br />', $context['post_error']['description']), '
						</dt>
					</dl>
				</div>';

	// The post header... important stuff
	echo '
				<dl id="post_header">';

	// Show a box so that user can write the Test Cases name
	echo '
					<dt>
						<label for="name">
							<span', isset($context['post_error']['name']) ? ' class="error"' : '', ' id="caption_name">', $txt['ts_name'], ':</span>
						</label>
					</dt>
					<dd>
						<input type="text" name="name" id="name"', isset($context['name']) ? ' value="' . $context['name'] . '"' : '', ' tabindex="', $context['tabindex']++, '" size="80" maxlength="80" class="input_text" />
					</dd>';

	echo'
				</dl><hr class="clear" />';

	// Write DESCRIPTION related to Test Cases
	echo'
			<div>
				<div class="description_text">', $txt['ts_description'] ,':</div>
				<div class="description_box">';
		
		// Show the actual posting area (woohh BBC codes usage, never going to forget this part :()
		if ($context['show_bbc'])
		{
			echo '
					<div id="bbcBox_message"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
				echo '
						<div id="smileyBox_message"></div>';

		// Atlast show the box to type description
		echo '
				', template_control_richedit('message', 'smileyBox_message', 'bbcBox_message'); 
								
		echo '
					</div>
				</div><hr class="clear" />';

		// We need a box to type STEPS also
		echo'
				<div>
					<div class="description_text">', $txt['ts_steps'] ,':</div>
					<div class="description_box">';

		// Show the actual posting area (woohh BBC codes usage, never going to forget this part :()
		if ($context['show_bbc'])
		{
			echo '
					<div id="bbcBox_steps"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
				echo '
					<div id="smileyBox_steps"></div>';

		// Atlast show the box to type description
		echo '
				', template_control_richedit('steps', 'smileyBox_steps', 'bbcBox_steps'); 
			
		echo '
					</div>
				</div><hr class="clear" />';

		// We need a box to type Expected Result also
		echo'
				<div>
					<div class="description_text">', $txt['ts_expected_result'] ,':</div>
					<div class="description_box">';

		// Show the actual posting area (woohh BBC codes usage, never going to forget this part :()
		if ($context['show_bbc'])
		{
			echo '
					<div id="bbcBox_expected_result"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
				echo '
						<div id="smileyBox_expected_result"></div>';

		// Atlast show the box to type description
		echo '
				', template_control_richedit('expected_result', 'smileyBox_expected_result', 'bbcBox_expected_result'); 
		
		echo '
					</div>
				</div><hr class="clear" />';

		// So who's the champ to which this Case is going to be allotted
		echo '
			<dl id="post_header">
				<dt>
					<label for="id_assigned">
							<span', isset($context['post_error']['id_assigned']) ? ' class="error"' : '', ' id="caption_subject">', $txt['ts_id_assigned'], ':</span>
					</label>
				</dt>
				<dd>
					<input type="text" name="id_assigned" id="id_assigned"', isset($context['id_assigned']) ? ' value="' . $context['id_assigned'] . '"' : '', ' tabindex="', $context['tabindex']++, '" size="10" maxlength="10" class="input_text" />
					<div id="id_assigned_list_container"></div>
				</dd>
			</dl><hr class="clear" />';

		//Request the id's of Project, Suite and Test Case on our way to exit
				echo'
						<input type="hidden" name="s" value="', $context['test_suite']['current_suite'], '" />';

		// Finally, the submit buttons.
		echo '
				<br />
				<p class="righttext">
						<input type="submit" value="', $txt['ts_create'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />
				</p>
						</div>
						<span class="lowerframe"><span></span></span>
				</div>
				<br class="clear" />
		</form>';

	echo '
		<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/suggest.js?fin20"></script>
		<script type="text/javascript"><!-- // --><![CDATA[
			var oAssignSuggest = new smc_AutoSuggest({
				sSelf: \'oAssignSuggest\',
				sSessionId: \'', $context['session_id'], '\',
				sSessionVar: \'', $context['session_var'], '\',
				sSuggestId: \'id_assigned\',
				sControlId: \'id_assigned\',
				sSearchType: \'member\',
				bItemList: true,
				sPostName: \'id_assigned_list\',
				sURLMask: \'action=profile;u=%item_id%\',
				sTextDeleteItem: \'', $txt['autosuggest_delete_item'], '\',
				sItemListContainerId: \'id_assigned_list_container\',
				aListItems: [';
				if (isset($context['id_assigned']))
				{
					foreach ($context['id_assigned'] as $assigned => $member)
						echo '
						{
							sItemId: ', JavaScriptEscape($member['id']), ',
							sItemName: ', JavaScriptEscape($member['name']), '
						}', $assigned == count($context['id_assigned']) - 1 ? '' : ',';
				}
			echo '
				]
			});
		// ]]></script>';
}

function template_create_run()
{
	global $context, $txt, $scripturl;

	TS_show_header();

	echo '
		<form action="', $context['test_suite']['url'], ';createrun2" method="post" enctype="multipart/form-data">';

		// Start the main table.
		echo '
				<div id="ts_post" class="cat_bar">
						<h3 class="catbg">', $context['page_title'], '</h3>
				</div>
				<div>
						<span class="upperframe"><span></span></span>
						<div class="roundframe">';

		// If an error occurred, explain what happened.
		echo '
				<div class="errorbox"', empty($context['post_error']['description']) ? ' style="display: none"' : '', ' id="errors">
					<dl>
						<dt>
							<strong style="', empty($context['error_type']) || $context['error_type'] != 'serious' ? 'display: none;' : '', '" id="error_serious">', (empty($context['test_suite']['current_run']) ? $txt['ts_error_while_creating_project'] : $txt['ts_error_while_creating_run']), '</strong>
						</dt>
						<dt class="error" id="error_list">
							', empty($context['post_error']['description']) ? '' : implode('<br />', $context['post_error']['description']), '
						</dt>
					</dl>
				</div>';

		// The post header... important stuff
		echo '
				<dl id="post_header">';

		// So what's the result of the run?
		echo '
				<dt>
					<label for="result">
							<span', isset($context['post_error']['result']) ? ' class="error"' : '', ' id="caption_subject">', $txt['ts_result'], ':</span>
					</label>
				</dt>
				<dd>
					<select name="result" tabindex="', $context['tabindex']++, '">
						<option value="select" selected="selected">', '(', $txt['ts_select'], ')', '</option>
						<option value="pass"', (isset($context['result_achieved']) && ($context['result_achieved'] == 'pass')) ? ' selected="selected"' : '', '>', $txt['ts_pass'], '</option>
						<option value="fail"', (isset($context['result_achieved']) && ($context['result_achieved'] == 'fail')) ? ' selected="selected"' : '', '>', $txt['ts_fail'], '</option>
					</select>
				</dd>';

		echo'
				</dl><hr class="clear" />';

		// Ask user aout the feedback of the run he/she has made
		echo '
				<div>
					<div class="textbox">
						<div class="text">
							<p>
								<label for="feedback">', $txt['ts_feedback'], ':</label>
							</p>
						</div>
						<textarea class="editor" name="feedback" id="feedback" rows="20" cols="600" tabindex="', $context['tabindex']++, '" style="width: 70%; height: 150px; ', isset($context['post_error']['no_feedback']) || isset($context['post_error']['long_feedback']) ? 'border: 1px solid red;' : '', '">', isset($context['feedback']) ? $context['feedback'] : '', '</textarea>
					</div>
				</div>';

		// Ask the bug ID, pitty we are asking users to submit this manually at this stage :(
		echo '
				<hr class="clear" />
				<dl id="post_header">
					<dt>
						<label for="id_bug">
							<span', isset($context['post_error']['id_bug']) ? ' class="error"' : '', ' id="caption_subject">', $txt['ts_id_bug'], ':</span>
						</label>
					</dt>
					<dd>
						<input type="text" name="id_bug" id="id_bug"', isset($context['id_bug']) ? ' value="' . $context['id_bug'] . '"' : '', ' tabindex="', $context['tabindex']++, '" size="10" maxlength="10" class="input_text" />
					</dd>
				</dl>';

		//Request the id's of Project, Suite and Test Case on our way to exit
		echo'
			<input type="hidden" name="c" value="', $context['test_suite']['current_case'], '" />';

		// Finally, the submit buttons.
		echo '
				<br />
				<p class="righttext">
					<input type="submit" value="', $txt['ts_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />
				</p>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>
			<br class="clear" />
		</form>';
}

function template_edit_project()
{
	global $context, $txt, $scripturl;

	TS_show_header();

	echo '
	<form action="', $context['test_suite']['url'], ';editproject2" method="post" enctype="multipart/form-data">';

	// Start the main table.
	echo '
		<div id="ts_post" class="cat_bar">
				<h3 class="catbg">', $context['page_title'], '</h3>
		</div>
		<div>
			<span class="upperframe"><span></span></span>
			<div class="roundframe">';
								
	// If an error occurred, explain what happened.
	echo '
			<div class="errorbox"', empty($context['post_error']['description']) ? ' style="display: none"' : '', ' id="errors">
				<dl>
					<dt>
						<strong style="', empty($context['error_type']) || $context['error_type'] != 'serious' ? 'display: none;' : '', '" id="error_serious">', (empty($context['test_suite']['current_project']) ? $txt['ts_error_while_creating_project'] : $txt['ts_error_while_creating_suite']), '</strong>
					</dt>
					<dt class="error" id="error_list">
						', empty($context['post_error']['description']) ? '' : implode('<br />', $context['post_error']['description']), '
					</dt>
				</dl>
			</div>';

	// The post header... important stuff
	echo '
			<dl id="post_header">';

	// Show a box so that user can write the Project/Suite name
	echo '
				<dt>
					<label for="name">
						<span', isset($context['post_error']['name']) ? ' class="error"' : '', ' id="caption_subject">', $txt['ts_name'], ':</span>
					</label>
				</dt>
				<dd>
					<input type="text" name="name" id="name"', isset($context['name']) ? ' value="' . $context['name'] . '"' : '', ' tabindex="', $context['tabindex']++, '" size="80" maxlength="80" class="input_text" />
				</dd>';

		echo'
				</dl>
				<hr class="clear" />';

		// Write description related to Project/Suite
		echo '
				<div>
					<div class="textbox">
						<div class="text">
							<p>
								<label for="description">', $txt['ts_description'], ':</label>
							</p>
						</div>
						<textarea class="editor" name="description" id="description" rows="20" cols="600" tabindex="', $context['tabindex']++, '" style="width: 70%; height: 150px; ', isset($context['post_error']['no_description']) || isset($context['post_error']['long_description']) ? 'border: 1px solid red;' : '', '">', isset($context['description']) ? $context['description'] : '', '</textarea>
					</div>
				</div>';

	// Ohh take the Project ID if we're using this function for creation of suites
	echo'
			<input type="hidden" name="proj" value="', $context['test_suite']['current_project'], '" />
			<input type="hidden" name="s" value="', $context['test_suite']['current_suite'], '" />';

	// Finally, the submit button.
	echo '
			<br />
			<p class="righttext">
				<input type="submit" value="', $txt['ts_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />
			</p>
			</div>
			<span class="lowerframe"><span></span></span>
		</div>
		<br class="clear" />
	</form>';
}

// Just to make things less complicated one more function for level 3 & 4
function template_edit_case()
{
	global $context, $txt, $scripturl, $settings;

	TS_show_header();

	echo '
	<form action="', $context['test_suite']['url'], ';editcase2" method="post">';

	// Start the main table.
	echo '
		<div id="ts_post" class="cat_bar">
			<h3 class="catbg">', $context['page_title'], '</h3>
		</div>
		<div>
			<span class="upperframe"><span></span></span>
			<div class="roundframe">';

	// If an error occurred, explain what happened.
	echo '
			<div class="errorbox"', empty($context['post_error']['description']) ? ' style="display: none"' : '', ' id="errors">
				<dl>
					<dt>
						<strong style="', empty($context['error_type']) || $context['error_type'] != 'serious' ? 'display: none;' : '', '" id="error_serious">', $txt['ts_error_while_creating_case'], '</strong>
					</dt>
					<dt class="error" id="error_list">
						', empty($context['post_error']['description']) ? '' : implode('<br />', $context['post_error']['description']), '
					</dt>
				</dl>
			</div>';

	// The post header... important stuff
	echo '
			<dl id="post_header">';

	// Show a box so that user can write the Test Cases name
	echo '
				<dt>
					<label for="name">
						<span', isset($context['post_error']['name']) ? ' class="error"' : '', ' id="caption_name">', $txt['ts_name'], ':</span>
					</label>
				</dt>
				<dd>
					<input type="text"', isset($context['post_error']['no_name']) ? ' class="error"' : '', ' name="name" id="name"', isset($context['name']) ? ' value="' . $context['name'] . '"' : '', ' tabindex="', $context['tabindex']++, '" size="80" maxlength="80" class="input_text" />
				</dd>';

	echo'
			</dl><hr class="clear" />';

	// Write DESCRIPTION related to Test Cases
	echo'
			<div>
				<div class="description_text">', $txt['ts_description'] ,':</div>
				<div class="description_box">';

		// Show the actual posting area (woohh BBC codes usage, never going to forget this part :()
		if ($context['show_bbc'])
		{
			echo '
				<div id="bbcBox_message"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
			echo '
					<div id="smileyBox_message"></div>';

		// Atlast show the box to type description
		echo '
				', template_control_richedit('message', 'smileyBox_message', 'bbcBox_message'); 
				
		echo '
					</div>
				</div><hr class="clear" />';

		// We need a box to type STEPS also
		echo'
			<div>
				<div class="description_text">', $txt['ts_steps'] ,':</div>
				<div class="description_box">';

		// Show the actual posting area (woohh BBC codes usage, never going to forget this part :()
		if ($context['show_bbc'])
		{
			echo '
				<div id="bbcBox_steps"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
			echo '
				<div id="smileyBox_steps"></div>';

		// Atlast show the box to type description
		echo '
				', template_control_richedit('steps', 'smileyBox_steps', 'bbcBox_steps'); 
			
		echo '
				</div>
			</div><hr class="clear" />';

		// We need a box to type Expected Result also
		echo'
			<div>
				<div class="description_text">', $txt['ts_expected_result'] ,':</div>
				<div class="description_box">';

		// Show the actual posting area (woohh BBC codes usage, never going to forget this part :()
		if ($context['show_bbc'])
		{
			echo '
					<div id="bbcBox_expected_result"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
			echo '
					<div id="smileyBox_expected_result"></div>';

		// Atlast show the box to type description
		echo '
				', template_control_richedit('expected_result', 'smileyBox_expected_result', 'bbcBox_expected_result'); 
		
		echo '
				</div>
			</div><hr class="clear" />';

		// So who's the champ to which this Case is going to be allotted
		echo '
			<dl id="post_header">
				<dt>
					<label for="id_assigned">
						<span', isset($context['post_error']['id_assigned']) ? ' class="error"' : '', ' id="caption_subject">', $txt['ts_id_assigned'], ':</span>
					</label>
				</dt>
				<dd>
					<input type="text" name="id_assigned" id="id_assigned" value="' . $context['id_assigned_list'] . '" tabindex="', $context['tabindex']++, '" size="10" maxlength="10" class="input_text" />
					<div id="id_assigned_list_container"></div>
				</dd>
			</dl><hr class="clear" />';

		//Request the id's of Project, Suite and Test Case on our way to exit
		echo'
			<input type="hidden" name="c" value="', $context['test_suite']['current_case'], '" />';

		// Finally, the submit buttons.
		echo '
			<br />
			<p class="righttext">
				<input type="submit" value="', $txt['ts_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />
			</p>
			</div>
			<span class="lowerframe"><span></span></span>
		</div>
		<br class="clear" />
	</form>';

	echo '
		<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/suggest.js?fin20"></script>
		<script type="text/javascript"><!-- // --><![CDATA[
			var oAssignSuggest = new smc_AutoSuggest({
				sSelf: \'oAssignSuggest\',
				sSessionId: \'', $context['session_id'], '\',
				sSessionVar: \'', $context['session_var'], '\',
				sSuggestId: \'id_assigned\',
				sControlId: \'id_assigned\',
				sSearchType: \'member\',
				bItemList: true,
				sPostName: \'id_assigned_list\',
				sURLMask: \'action=profile;u=%item_id%\',
				sTextDeleteItem: \'', $txt['autosuggest_delete_item'], '\',
				sItemListContainerId: \'id_assigned_list_container\',
				aListItems: [';
					foreach ($context['id_assigned'] as $assigned => $member)
						echo '
						{
							sItemId: ', JavaScriptEscape($member['id']), ',
							sItemName: ', JavaScriptEscape($member['name']), '
						}', $assigned == count($context['id_assigned']) - 1 ? '' : ',';
			echo '
				]
			});
		// ]]></script>';
}

function template_edit_run()
{
	global $context, $txt, $scripturl;

	TS_show_header();

	echo '
		<form action="', $context['test_suite']['url'], ';editrun2" method="post" enctype="multipart/form-data">';

	// Start the main table.
	echo '
			<div id="ts_post" class="cat_bar">
				<h3 class="catbg">', $context['page_title'], '</h3>
			</div>
			<div>
				<span class="upperframe"><span></span></span>
				<div class="roundframe">';

		// If an error occurred, explain what happened.
		echo '
				<div class="errorbox"', empty($context['post_error']['feedback']) ? ' style="display: none"' : '', ' id="errors">
					<dl>
						<dt>
							<strong style="', empty($context['error_type']) || $context['error_type'] != 'serious' ? 'display: none;' : '', '" id="error_serious">', (empty($context['test_suite']['current_run']) ? $txt['ts_error_while_creating_project'] : $txt['ts_error_while_creating_run']), '</strong>
						</dt>
						<dt class="error" id="error_list">
							', empty($context['post_error']['feedback']) ? '' : implode('<br />', $context['post_error']['feedback']), '
						</dt>
					</dl>
				</div>';

		// The post header... important stuff
		echo '
				<dl id="post_header">';

		// So what's the result of the run?
		echo '
				<dt>
					<label for="result">
						<span', isset($context['post_error']['result']) ? ' class="error"' : '', ' id="caption_subject">', $txt['ts_result'], ':</span>
					</label>
				</dt>
				<dd>
					<select name="result" tabindex="', $context['tabindex']++, '">
						<option value="select" selected="selected">', '(', $txt['ts_select'], ')', '</option>
						<option value="pass"', 'pass' == $context['result_achieved'] ? ' selected="selected"' : '', '>', $txt['ts_pass'], '</option>
						<option value="fail"', 'fail' == $context['result_achieved'] ? ' selected="selected"' : '', '>', $txt['ts_fail'], '</option>
					</select>
				</dd>';

		echo'
				</dl><hr class="clear" />';

		// Ask user aout the feedback of the run he/she has made
		echo '
				<div>
					<div class="textbox">
						<div class="text">
							<p>
								<label for="feedback">', $txt['ts_feedback'], ':</label>
							</p>
						</div>
						<textarea class="editor" name="feedback" id="feedback" rows="20" cols="600" tabindex="', $context['tabindex']++, '" style="width: 70%; height: 150px; ', isset($context['post_error']['no_feedback']) || isset($context['post_error']['long_feedback']) ? 'border: 1px solid red;' : '', '">', isset($context['feedback']) ? $context['feedback'] : '', '</textarea>
					</div>
				</div>';

		// Ask the bug ID, pitty we are asking users to submit this manually at this stage :(
		echo '
				<hr class="clear" />
				<dl id="post_header">
					<dt>
						<label for="id_bug">
							<span', isset($context['post_error']['id_bug']) ? ' class="error"' : '', ' id="caption_subject">', $txt['ts_id_bug'], ':</span>
						</label>
					</dt>
					<dd>
						<input type="text" name="id_bug" id="id_bug"', isset($context['id_bug']) ? ' value="' . $context['id_bug'] . '"' : '', ' tabindex="', $context['tabindex']++, '" size="10" maxlength="10" class="input_text" />
					</dd>
				</dl>';

		//Request the id's of Project, Suite and Test Case on our way to exit
		echo'
			<input type="hidden" name="r" value="', $context['test_suite']['current_run'], '" />';
						
	// Finally, the submit buttons.
	echo '
			<br />
			<p class="righttext">
				<input type="submit" value="', $txt['ts_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />
			</p>
			</div>
			<span class="lowerframe"><span></span></span>
		</div>
		<br class="clear" />
	</form>';
}

function template_testsuite_admin()
{
	global $txt, $context;

	TS_show_header();

	echo '
	<div class="cat_bar">
		<h3 class="catbg">Test Suite Permissions</h2>
	</div>
	<div class="suite_frame">';
	
	echo '
	<form action="', $context['test_suite']['url'], ';admin=main" method="post">';

		foreach ($context['test_suite']['global_perms'] as $perm)
		{
				$perm['member_groups'] = $perm['member_groups'] == '' ? '' : explode(',', $perm['member_groups']);
			echo ' <fieldset>';
			echo '<legend>' . $txt['ts_global_perm_' . $perm['permission']] . '</legend>';

			foreach ($context['test_suite']['groups'] as $group)
			{
				echo '
					<input' . (is_array($perm['member_groups']) && in_array($group['id_group'], $perm['member_groups']) ? ' checked="checked"' : '') . ' id="' . $group['id_group'] . '" type="checkbox" name="' . $perm['permission'] . '[]" value="' . $group['id_group'] . '" /> <label for="' . $group['id_group'] . '">' . $group['group_name'] . '</label><br />';
			}
			echo ' </fieldset>';
		}

		echo '
		<input type="submit" name="submit" value="', $txt['ts_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />';

	echo '
	</form>';

	echo'
	</div>';
}

function template_testsuite_admin_per_level()
{
	global $txt, $context;

	TS_show_header();

	echo ' 
	<div class="cat_bar">
		<h3 class="catbg">', $txt['ts_perm'], ' - ', $context['test_suite']['permission']['level_name'], '</h2>
	</div>
	<div class="suite_frame">';

	echo '
	<form action="', $context['test_suite']['url'], ';admin=per_level;level_name='. $context['test_suite']['permission']['level_name'] . '" method="post">';

		foreach ($context['test_suite']['perms'] as $key => $perm)
		{
				$perm = $perm == '' ? '' : explode(',', $perm);
			echo ' <fieldset>';
			echo '<legend>' . $txt['ts_perm_' . $key] . '</legend>';

			foreach ($context['test_suite']['groups'] as $group)
			{
				echo '
					<input' . (is_array($perm) && in_array($group['id_group'], $perm) ? ' checked="checked"' : '') . ' id="' . $group['id_group'] . '" type="checkbox" name="' . $key . '[]" value="' . $group['id_group'] . '" /> <label for="' . $group['id_group'] . '">' . $group['group_name'] . '</label><br />';
			}
			echo ' </fieldset>';
		}

		echo '
		<input type="hidden" name="level_name" value="', $context['test_suite']['permission']['level_name'], '" />';

		if (!empty($context['test_suite']['permission']['id_level']))
		echo ' <input type="hidden" name="id_level" value="', $context['test_suite']['permission']['id_level'] , '" />';

		echo '
		<input type="submit" name="submit" value="', $txt['ts_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />';

	echo '
	</form>';

	echo'
	</div>';

	echo '<p></p>
	<div class="cat_bar">
		<h3 class="catbg">Something Else</h2>
	</div>
	<div class="suite_frame">I like me, am you.</div>';
}

function TS_show_buttons()
{
	global $context;

	$first = true;

	echo '
	<div class="buttons">
		<ul>';
	foreach ($context['test_suite']['buttons'] as $button)
	{
		echo ' <li' . (!$first ? ' class="more' : '') . '><a class="active" href="' . $button['href'] . '"><span>' . $button['name'] . '</span></a></li>';
		$first = false;
	}

	echo '
		</ul>
	</div>';
}

function template_copy()
{
	global $context, $txt, $scripturl;

	TS_show_header();

	// Start the main table.
	echo '
		<div id="ts_post" class="cat_bar">
			<h3 class="catbg">', $context['page_title'], '</h3>
		</div>
		<div class="suite_frame">';

		echo '
		<form action="', $context['test_suite']['url'], ';copyitem2" method="post">
			<fieldset>
				<input type="checkbox" class="input_check" onclick="invertAll(this, this.form);" /><label>' . $txt['ts_select_all'] . '</label><br />';

			if ($context['type'] == 'Project')
			echo '
				<input type="checkbox" class="input_check" name="level[]" value="1" /><label>' . $txt['ts_suite'] . '</label><br />';

			if (($context['type'] == 'Project') || ($context['type'] == 'Suite'))
			echo '
				<input type="checkbox" class="input_check" name="level[]" value="1" /><label>' . $txt['ts_case'] . '</label><br />';

			if (($context['type'] == 'Project') || ($context['type'] == 'Suite') || ($context['type'] == 'Case'))
			echo '
				<input type="checkbox" class="input_check" name="level[]" value="1" /><label>' . $txt['ts_run'] . '</label><br />
			</fieldset>';

		//Request the id's of Project, Suite and Test Case on our way to exit
			echo'
			<input type="hidden" name="id" value="', $context['id'], '" />
			<input type="hidden" name="type" value="', $context['type'], '" />';

		// Finally, the submit buttons.
			echo '
			<p>
				<input type="submit" value="', $txt['ts_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />
			</p>
		</form>
		</div>
		<br class="clear" />';
}

?>
