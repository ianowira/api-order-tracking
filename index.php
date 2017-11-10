<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(400);
	print_r(json_encode([
		"status"=> http_response_code(),
		"message" => "Invalid Request Method"
	]));
	exit;
}

if (! isset($_POST['tracking_number'])) {
  http_response_code(401);
  print_r(json_encode([
    "status"=> http_response_code(),
    "message"=> "Unauthorized"
  ]));
  exit;
}

require './vendor/autoload.php';

//track number = 40795800592

$soapClient = new SoapClient('shipments-tracking-api-wsdl.wsdl');

	$params = array(
		'ClientInfo' => array(
									'AccountCountryCode'	=> 'ZA',
									'AccountEntity'		 	=> 'DUR',
									'AccountNumber'		 	=> '200721',
									'AccountPin'		 	=> '543543',
									'UserName'			 	=> 'shahil@planet54.com',
									'Password'			 	=> '#E36m3bmw',
									'Version'			 	=> 'v1.0'
								),
		'Transaction' => array('Reference1' => '001'),
		'Shipments' => array($_POST['tracking_number'])
	);

	// calling the method and printing results
	try {
		$auth_call = $soapClient->TrackShipments($params);
		if(isset($auth_call->Notifications->Notification)) {
			if($auth_call->Notifications->Notification->Code === 'ERR01') {
				http_response_code(401);
				print_r(json_encode([
					"status"=> http_response_code(),
					"message"=> "Unauthorized",
					"payload" => $auth_call->Notifications->Notification->Message
				]));
				exit;
			}
		}

		http_response_code(200);
    print_r(json_encode([
			"status"=> http_response_code(),
			"message"=> "Success",
			"payload" => $auth_call->TrackingResults->KeyValueOfstringArrayOfTrackingResultmFAkxlpY->Value->TrackingResult
		]));
	} catch (SoapFault $fault) {
		die('Error : ' . $fault->faultstring);
	}

