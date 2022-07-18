<?php
function round_to_5($n){
	$x = 5 * ceil($n / 5);
	return $x;
}

function jira_api_call($project_key,$start_date="", $end_date=''){
	$api_key = get_option( 'jira_example_plugin_settings' )['api_key'];
	$api_url = get_option( 'jira_example_plugin_settings' )['site_url'];
	if(empty($end_date)){
		$end_date = date('Y-m-d');
	}


	$auth_array = array(
		"Authorization:",
		"Bearer",
		// $new_token
	);
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => $api_url."/rest/api/1/worklog/list?projectKey=".$project_key."&start=".$start_date."&end=".$end_date."&auth_token=".$api_key,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_POSTFIELDS => "",
		CURLOPT_HTTPHEADER => array(
			"Content-Type: application/json",
			"cache-control: no-cache"
		),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	$data2 = json_decode($response, true);
	$time_arr = [];
	$time_arr_nonbill = [];
	$time_arr_rounded = [];
	$time_arr_nonbill_rounded = [];
	$issues = [];
	$x = -1;
	foreach($data2 as $spent_time){
		$x += 1;
		$issues[$x]['date'] = $spent_time['date'];
		$issues[$x]['issueId'] = $spent_time['issueId'];
		$issues[$x]['issueKey'] = $spent_time['issueKey'];
		$issues[$x]['timeSpent'] = $spent_time['timeSpent'] / 60;
		$issues[$x]['timeSpentRounded'] = round_to_5($spent_time['timeSpent'] / 60);
		$issues[$x]['comment'] = $spent_time['comment'];
		$issues[$x]['worklogId'] = $spent_time['worklogId'];
		$issues[$x]['category'] = $spent_time['category'];
		if($spent_time['category'] == 0){
			array_push($time_arr, $spent_time['timeSpent']);
			array_push($time_arr_rounded, round_to_5($spent_time['timeSpent'] / 60));
		}else{
			array_push($time_arr_nonbill, $spent_time['timeSpent']);
			array_push($time_arr_nonbill_rounded, round_to_5($spent_time['timeSpent'] / 60));
		}

	}
	if($start_date != '1970-01-01'){
		$time_in_minutes_jira = array_sum($time_arr) / 60;
		$time_in_minutes_jira_rounded = array_sum($time_arr_rounded);
		$time_in_minutes_jira_nonbill = array_sum($time_arr_nonbill) / 60;
		$time_in_minutes_jira_nonbill_rounded = array_sum($time_arr_nonbill_rounded);
	}else{
		$time_in_minutes_jira = 0;
		$time_in_minutes_jira_nonbill = 0;
		$time_in_minutes_jira_rounded = 0;
		$time_in_minutes_jira_nonbill_rounded = 0;
	}

	return ['billable' => $time_in_minutes_jira, 'nonbillable' => $time_in_minutes_jira_nonbill,
	'issues' => $issues, 'billable_rounded' => $time_in_minutes_jira_rounded, 'nonbillable_rounded' =>$time_in_minutes_jira_nonbill_rounded ];
}

function get_filter_projects($filter_id, $start_date = '', $end_date = ''){
	/* Start to develop here. Best regards https://php-download.com/ */
	$api_key = get_option( 'jira_example_plugin_settings' )['jira_api_key'];
	$api_url = get_option( 'jira_example_plugin_settings' )['jira_site_url'];
	$api_email = get_option( 'jira_example_plugin_settings' )['jira_api_email'];
	Unirest\Request::auth($api_email, $api_key);

	$headers = array(
		'Accept' => 'application/json'
	);

	$response = Unirest\Request::get(
		$api_url.$filter_id.'/',
		$headers
	);


	$empty_arr = [];
	foreach((array)$response as $item){
		if($item->sharePermissions){
			array_push($empty_arr,(array)$item->sharePermissions);
		}
	}

	$second_arr = [];
	foreach($empty_arr as $item){
		foreach($item as $item2){
			array_push($second_arr, (array)$item2->project);
		}
	}
	$projects_arr = [];
	$x = -1;
	foreach($second_arr as $item){
		$x += 1;
		$projects_arr[$x]['project_key'] = $item['key'];
		$projects_arr[$x]['project_id'] = $item['id'];
	}
	$filter_vals = [];
	$billable_arr = [];
	$nonbillable_arr = [];
	$billable_arr_rounded = [];
	$nonbillable_arr_rounded = [];
	$issues = [];
	$y = -1;
	foreach($projects_arr as $item){
		$y += 1;
		$project_key = $item['project_key'];
		$project_id = $item['project_id'];
		$filter_vals[$y]['billable'] = jira_api_call($project_key, $start_date)['billable'];
		$filter_vals[$y]['nonbillable'] = jira_api_call($project_key, $start_date)['nonbillable'];
		$filter_vals[$y]['billable_rounded'] = jira_api_call($project_key, $start_date)['billable_rounded'];
		$filter_vals[$y]['nonbillable_rounded'] = jira_api_call($project_key, $start_date)['nonbillable_rounded'];
		$filter_vals[$y]['issues'] = jira_api_call($project_key, $start_date)['issues'];
	}
	foreach($filter_vals as $item){
		array_push($billable_arr, $item['billable']);
		array_push($nonbillable_arr, $item['nonbillable']);
		array_push($billable_arr_rounded, $item['billable_rounded']);
		array_push($nonbillable_arr_rounded, $item['nonbillable_rounded']);
		array_push($issues, $item['issues']);
	}
	$billable = array_sum($billable_arr);
	$nonbillable = array_sum($nonbillable_arr);
	$billable_rounded = array_sum($billable_arr_rounded);
	$nonbillable_rounded = array_sum($nonbillable_arr_rounded);
	return ['billable' => $billable, 'nonbillable' => $nonbillable, 'issues' => $issues,
	'billable_rounded' => $billable_rounded, 'nonbillable_rounded' => $nonbillable_rounded];
}
function get_filter_sum($filter_arr, $start_date = '', $end_date = ''){
	$filter_id = explode (",", $filter_arr);
	$billable_arr = [];
	$nonbillable_arr = [];
	$billable_arr_rounded = [];
	$nonbillable_arr_rounded = [];
	$issues = [];
	foreach($filter_id as $item){
		array_push($billable_arr, get_filter_projects($item,$start_date)['billable']);
		array_push($nonbillable_arr, get_filter_projects($item,$start_date)['nonbillable']);
		array_push($billable_arr_rounded, get_filter_projects($item,$start_date)['billable_rounded']);
		array_push($nonbillable_arr_rounded, get_filter_projects($item,$start_date)['nonbillable_rounded']);
		array_push($issues, get_filter_projects($item,$start_date)['issues']);
	}
	$billable = array_sum($billable_arr);
	$nonbillable = array_sum($nonbillable_arr);
	$billable_rounded = array_sum($billable_arr_rounded);
	$nonbillable_rounded = array_sum($nonbillable_arr_rounded);
	return ['billable' => $billable, 'nonbillable' => $nonbillable, 'issues' => $issues,
		'billable_rounded' => $billable_rounded, 'nonbillable_rounded' => $nonbillable_rounded];
}
