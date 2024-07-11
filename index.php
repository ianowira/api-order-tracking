<?php

header('Content-Type: application/json');

/**
 *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
 *  origin.
 *
 *  In a production environment, you probably want to be more restrictive, but this gives you
 *  the general idea of what is involved.  For the nitty-gritty low-down, read:
 *
 *  - https://developer.mozilla.org/en/HTTP_access_control
 *  - http://www.w3.org/TR/cors/
 *
 */
function cors() {

	if (isset($_SERVER['HTTP_ORIGIN'])) {
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');
	}

	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

			exit(0);
	}
}

cors();


/**
 *  Allow to out a response status after code has been executed
 *
 * @param int $statuscode
 * @param string $message
 * @return void
 */
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

/**
 * Fetch parameters from requests
 */

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
require_once('./fastnfurious.php');
require_once('./bex.php');
require_once('./aramex.php');

if (file_exists(__DIR__ . '/.env')) {
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();
}

switch (strtolower($entityBody->tracking_company)) {
	case 'aramex':
        $aramex = new Aramex($entityBody);
        $aramex->init();
		break;

	case 'bex':
        $bex = new Bex($entityBody);
        $bex->init();
    break;

  case 'fast furious':
      $ff = new FF($entityBody);
      $ff->init();
    break;
	default:
		response(428,'Tracking Company not found.');
		break;
}
