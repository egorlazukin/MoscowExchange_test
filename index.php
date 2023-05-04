<?
	include 'function.php';
	$request = new ApiRequest('https://www.moex.com');
	$response = $request->sendRequest('/s868');
	$only_tag = $request->SearsUrl($response);
	echo $only_tag;
	/*
	include 'function.php';
	$request = new ApiRequest('https://www.moex.com');
	$response = $request->sendRequest('/n39381');
	echo '<pre>';
	echo $response;
	echo '</pre>';
	*/
?>