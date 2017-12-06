<?php 
// Bex
$trackingNumbersArray = $entityBody->tracking_numbers;

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://api.bex.co.za/api/WaybillQuickTrackingV2CustomTreeview?searchItems=' . implode($entityBody->tracking_numbers,','),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Cache-Control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	http_response_code(401);
	print_r(json_encode([
		"status"=> http_response_code(),
		"message"=> "cURL Error",
		"payload" => $err
	]));
	exit;
}

$items = json_decode($response)->items;

$shipments = [];

foreach ($items as $key => $item) {
	if($item->id === 0)
		continue;
	if(in_array($item->col2, $trackingNumbersArray)) {
		$shipments[$item->col2] = [];
		array_push($shipments[$item->col2], [
			'date' => $item->col1,
			'reference_number' => $item->col3,
			'description' => $item->col4,
			'name' => $item->col5]);
	}
}

http_response_code(200);
print_r(json_encode([
	'status' => http_response_code(),
	'message'=> 'Success',
	'payload' => $shipments 
]));
