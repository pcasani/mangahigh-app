<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use \App\api\WeatherForecast;
use GuzzleHttp\Client;

/**
 * Example of use of the Weather Forecast
 */

$base_uri = 'api.openweathermap.org/data/2.5/weather';
$params = ['city_name' => 'london', 'country_code' => 'UK', 'api_key'=> 'ed322bdef8bfd0a592207136697ca9fa'];
$requestType = 'city_country_temperature';

$client = new Client();
$make_request = new WeatherForecast($client, $base_uri, $params);

// Outputs
echo $make_request->requestCurrentTemperature($requestType);




