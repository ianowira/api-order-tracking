<?php 

//track number = 40795800592
$soapClient = new SoapClient('shipments-tracking-api-wsdl.wsdl');

	$params = array(
		'ClientInfo' => array(
									'AccountCountryCode'	=> $_ENV['am_accountcountycode'],
									'AccountEntity'		 	=> $_ENV['am_accountentity'],
									'AccountNumber'		 	=> $_ENV['am_accountnumber'],
									'AccountPin'		 	=> $_ENV['am_accountpin'],
									'UserName'			 	=> $_ENV['am_username'],
									'Password'			 	=> $_ENV['am_password'],
									'Version'			 	=> 'v1.0'
								),
		'Transaction' => array('Reference1' => '001'),
		'Shipments' => $entityBody->tracking_numbers
	);

	// calling the method and printing results
	try {
		$auth_call = $soapClient->TrackShipments($params);
		if(isset($auth_call->Notifications->Notification)) {

			if(
				$auth_call->Notifications->Notification[0]->Code === 'ERR01' || $auth_call->Notifications->Notification[0]->Code === 'REQ03' ||
				$auth_call->Notifications->Notification[1]->Code === 'REQ04' ) {
				http_response_code(401);
				print_r(json_encode([
					"status"=> http_response_code(),
					"message"=> "Unauthorized",
					"payload" => $auth_call->Notifications->Notification
				]));
				exit;
			}
    }

    $payload = $auth_call->TrackingResults->KeyValueOfstringArrayOfTrackingResultmFAkxlpY;

    if (!isset($payload)) {
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
			'payload' => $payload
    ]));
    
	} catch (SoapFault $fault) {
			http_response_code(500);
			print_r(json_encode([
				'status' => http_response_code(),
				'message'=> 'Error, error, error!',
				'payload' => $fault->faultstring
			]));
	}
