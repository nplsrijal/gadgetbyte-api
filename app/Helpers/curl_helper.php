<?php

if (!function_exists('curl_request')) {
    function curl_request($url, $method = 'GET', $data = [], $headers = []) {

        if(isset($data['isjson']) && $data['isjson']===true)
        {
          unset($data['isjson']);
          $postdata=json_encode($data);
          $ctype="Content-type: application/json";

        }
        else
        {
          $postdata=http_build_query($data);
          $ctype='';
        }

        if(empty($data['org_id']))
        {
            $data['org_id']='614';
        }
        $ch = curl_init('http://api.mero.doctor/api/v3/'.$url);

        // Set cURL options based on the method
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        }


        $http_response_header=array(
            "Apikey: " . md5('org_'.$data['org_id']),
            "Orgid:". $data['org_id'],
            "Apiversion: v3",
            "Appversioncode: 57",
            "Machinetype: Web",
            "Type: Web",
            $ctype

        );


        if(count($headers) > 0)
        {
            $http_response_header=array_merge($headers,$http_response_header);
        }

        // Set common cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$http_response_header);

        $response = curl_exec($ch);

        

        curl_close($ch);
        $response=json_decode($response);

        return $response;
    }
}
