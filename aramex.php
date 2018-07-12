<?php 

//track number = 40795800592
$soapClient = new SoapClient('shipments-tracking-api-wsdl.wsdl');

	$params = array(
		'ClientInfo' => array(
									'AccountCountryCode'	=> getenv('am_accountcountycode'),
									'AccountEntity'		 	=> getenv('am_accountentity'),
									'AccountNumber'		 	=> getenv('am_accountnumber'),
									'AccountPin'		 	=> getenv('am_accountpin'),
									'UserName'			 	=> getenv('am_username'),
									'Password'			 	=> getenv('am_password'),
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

