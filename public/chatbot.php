<?php

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        header('Access-Control-Max-Age: 1728000');
        header('Content-Length: 0');
        header('Content-Type: text/plain');
        die();
    }

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    
    $url = 'https://api.api.ai/v1/query';
    
    $method = $_SERVER['REQUEST_METHOD'];

    if ($_GET && isset($_GET['v'])) {
    	$headers = getallheaders();
    	$headers_str = [];
    	$version = $_GET['v'];
    	$url = $url . '?v=' . $version;
    
    	foreach ( $headers as $key => $value){
    		if($key == 'Host' || $key == 'Origin' || $key == 'Accept-Encoding')
    			continue;
    		$headers_str[]=$key.":".$value;
    	}
    	
    	$ch = curl_init($url);
    
    	curl_setopt($ch,CURLOPT_URL, $url);
    	if( $method !== 'GET') {
    		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    	}
    
    	if($method == "PUT" || $method == "PATCH" || ($method == "POST" && empty($_FILES))) {
    		$data_str = file_get_contents('php://input');
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
    	}
    	elseif($method == "POST") {
    		$data_str = array();
    		if(!empty($_FILES)) {
    			foreach ($_FILES as $key => $value) {
    				$full_path = realpath( $_FILES[$key]['tmp_name']);
    				$data_str[$key] = '@'.$full_path;
    			}
    		}
    
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str+$_POST);
    	}
    
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers_str );
    
    	$result = curl_exec($ch);
    	curl_close($ch);
    	
    	header('Content-Type: application/json');
    	echo $result;
    }
    else {
    	echo $method;
    	var_dump($_POST);
    	var_dump($_GET);
    	$data_str = file_get_contents('php://input');
    	echo $data_str;
    
    }