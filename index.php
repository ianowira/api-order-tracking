<?php
var_dump($_SERVER);
die();
header('Content-Type: application/json');
header('Access-Control-Request-Method: POST');
header('Access-Control-Request-Headers: origin, x-requested-with');
header('Origin: '. $_SERVER['HTTP_HOST']);
header('Access-Control-Allow-Origin: '. $_SERVER['HTTP_HOST']);

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(400);
	print_r(json_encode([
		'status' => http_response_code(),
		'message' => 'Invalid Request Method'
	]));
	exit;
}
$entityBody = json_decode(file_get_contents('php://input'));

if (! isset($entityBody)) {
  http_response_code(422);
  print_r(json_encode([
    'status'=> http_response_code(),
    'message'=> 'Invalid JSON'
  ]));
  exit;
}

if(! isset($entityBody->tracking_numbers)){
  http_response_code(428 );
  print_r(json_encode([
    'status'=> http_response_code(),
    'message'=> '\'tracking_numbers\' object missing'
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
		'Shipments' => $entityBody->tracking_numbers
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
			'status' => http_response_code(),
			'message'=> 'Success',
			'payload' => $auth_call->TrackingResults->KeyValueOfstringArrayOfTrackingResultmFAkxlpY
		]));
	} catch (SoapFault $fault) {
			http_response_code(500);
			print_r(json_encode([
				'status' => http_response_code(),
				'message'=> 'Error, error, error!',
				'payload' => $fault->faultstring
			]));
	}

