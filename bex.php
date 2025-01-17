<?php

class Bex {
    public $entityBody;

    public function __construct($entityBody)
    {
        $this->entityBody = $entityBody;
    }

    function init()
    {

        $trackingNumbersArray = $this->entityBody->tracking_numbers;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.bex.co.za/api/WaybillQuickTrackingV2CustomTreeview?searchItems=' . implode(',', $this->entityBody->tracking_numbers),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
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

            if(empty($item->col2))
                $item->col2 = 'PL' . $item->col3;
            $date = new DateTime($item->col1);
            if(in_array($item->col2, $trackingNumbersArray)) {
                $shipments[$item->col2][] = [
                    'date' => $date->format('Y-m-d H:i:s'),
                    'reference_number' => $item->col3,
                    'description' => $item->col4,
                    'name' => $item->col5
                ];
            }
        }
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
    }
}
