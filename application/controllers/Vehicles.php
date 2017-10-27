<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicles extends CI_Controller {

	function _remap($method, $args)
    {
       if (method_exists($this, $method))
       {
           $this->$method($args);
       }
       else
       {
            $this->index($method, $args);
       }
    }

    function index($method = 0, $args = array())
    {
    	$model_year = NULL;
    	$manufacturer = NULL;
    	$model = NULL;
    	$withRating = false;

    	if(isset($_GET['withRating']) && $_GET['withRating'] == "true"){
    		$withRating = true;
    	}
    	if ($method){
    		$model_year = $method;
	    	$manufacturer = isset($args[0]) ? $args[0] : NULL;
	    	$model = isset($args[1]) ? $args[1] : NULL;
	    }
	    else
	    {
		    if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
			   throw new Exception('Request method must be POST!');
			}

			//Make sure that the content type of the POST request has been set to application/json
			$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
			if(strcasecmp($contentType, 'application/json') != 0){
			   throw new Exception('Content type must be: application/json');
			}

			//Receive the RAW post data.
			$content = trim(file_get_contents("php://input"));

			//Attempt to decode the incoming RAW post data from JSON.
			$decoded = json_decode($content, true);

			$model_year =  isset($decoded['modelYear']) ? $decoded['modelYear'] : NULL;
		    $manufacturer = isset($decoded['manufacturer']) ? $decoded['manufacturer'] : NULL;
		    $model = isset($decoded['model']) ? $decoded['model'] : NULL;	
		    $withRating = isset($decoded['withRating']) && $withRating == "false" ? $decoded['withRating'] : $withRating;
		    
	    }
    	
        $url = 'https://one.nhtsa.gov/webapi/api/SafetyRatings/modelyear/'. $model_year .'/make/' . $manufacturer . '/model/' . $model . '?format=json';
		$data = $this->curl($url);

		$json = json_decode($data, true);
		
		$obj = new stdClass();
		$obj->Count = $json != NULL ? $json['Count'] : 0;
		$obj->Results = array();

		if ($model_year != NULL && $manufacturer != NULL && $model_year != NULL){
			if (!empty($json['Results'])){
				foreach($json['Results'] as $item) {
					$new_obj = new stdClass();
				    if ($withRating){
						$overallRating = $this->get_vehicle_rating($item['VehicleId']);
						$new_obj->CrashRating = $overallRating;
					}
					$new_obj->Description = $item['VehicleDescription'];
				    $new_obj->VehicleId = $item['VehicleId'];
				    array_push($obj->Results,$new_obj);
				}
			}
		}
		else
		{
			$obj->Count = 0;
		}
		
		return $obj;
    }

    public function get_vehicle_rating($vehicleId = '')
    {
    	$url = 'https://one.nhtsa.gov/webapi/api/SafetyRatings/VehicleId/'. $vehicleId . '?format=json';
    	$data = $this->curl($url);
    	$json = json_decode($data, true);
    	$overallRating = $json['Results'][0]['OverallRating'];
    	return $overallRating;

    }

	public function curl($url,$data = '')
	{
		$headers = array(
	    'Accept: application/json',
	    'Content-Type: application/json',
	    );

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 40000);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec ($ch);
		return $response;
	}
}

