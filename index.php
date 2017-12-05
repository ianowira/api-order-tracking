<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

function response($statuscode ,$message) {
	http_response_code($statuscode);
	print_r(json_encode([
		'status' => http_response_code(),
		'message' => $message
	]));
	exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
	response(400,'Invalid Request Method');
}

$entityBody = json_decode(file_get_contents('php://input'));

if (! isset($entityBody)) {
	response(422,'Invalid JSON');
}

if(! isset($entityBody->tracking_numbers)) {
  response(428,'\'tracking_numbers\' object missing');
}

if(! isset($entityBody->tracking_company)) {
  response(428,'\'tracking_company\' name missing');
}

require './vendor/autoload.php';

switch ($entityBody->tracking_company) {
	case 'Aramex':
		require_once('./aramex.php');
		break;
	case 'Bex':
		require_once('./bex.php');
		break;
	default:
		response(428,'Tracking Company not found.');
		break;
}