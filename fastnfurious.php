<?php 

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.inboundfnf.co.za/api/WaybillTrack/GetWaybillTracking/' . implode($entityBody->tracking_numbers,','),
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

$shipments = json_decode($response);

if (empty($shipments)) {
  http_response_code(404);
  print_r(json_encode([
    'status' => http_response_code(),
    'message'=> 'Tracking currently is not available for this order.',
    'payload' => null
  ]));
  return;
}

http_response_code(200);

print_r(json_encode([
	'status' => http_response_code(),
	'message'=> 'Success',
	'payload' => $shipments 
]));
