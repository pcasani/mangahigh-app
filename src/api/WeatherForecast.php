<?php

namespace App\api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

require __DIR__ . '/../../vendor/autoload.php';

class WeatherForecast
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $base_uri;
    private $full_uri;
    private $requestMethod = 'GET';
    private $params = [];

    function __construct(Client $client, string $base_uri, array $params)
    {
        $this->client = $client;
        $this->base_uri = $base_uri;
        $this->params = $params;
    }

    /**
     * @param $data
     * @return array
     */
    static public function decodeData($data): array
    {
        return json_decode($data, true);
    }

    /**
     * Builds the request URI
     *
     * e.g.: api.openweathermap.org/data/2.5/weather?q={city name},{state code}&appid={your api key}
     *
     * @param $requestType
     */
    public function buildUri($requestType)
    {
        switch ($requestType):
            case ('city_country_temperature'):
                //
                $this->full_uri = $this->base_uri . '/?';
                $this->full_uri .= 'q=' . $this->params['city_name'] . ',' . $this->params['country_code'];
                $this->full_uri .= '&appid=' . $this->params['api_key'];
                break;
        endswitch;
    }

    /**
     * Gets temperature from API
     *
     * @param $requestType
     * @return string
     */
    public function requestCurrentTemperature($requestType): string
    {
        $this->buildUri($requestType);

        $response = '';
        try {
            $response = $this->client->request(
                $this->requestMethod,
                $this->full_uri
            );
        } catch (GuzzleException $exception) {
            echo($exception->getMessage());
        }

        $base_temperature = $this->parseResponse($response->getBody(), $requestType);
        $this->getTemperatureFormats($base_temperature);

        return json_encode($this->getTemperatureFormats($base_temperature));

    }

    /**
     * @param $response_body
     * @param $requestType
     * @return mixed
     */
    public function parseResponse($response_body, $requestType): float
    {
        $response_body_arr = $this->decodeData($response_body);

        switch ($requestType):
            case ('city_country_temperature'):
                return $response_body_arr['main']['temp'];
                break;
        endswitch;
    }

    /**
     * @param $base_temperature
     * @return array
     */
    public function getTemperatureFormats($base_temperature): array
    {
        $temperature_formats_arr = [$this->params['city_name'] => ['Celsius' => '', 'Kelvin' => '', 'Fahrenheit' => '']];
        $temperature_formats_arr[$this->params['city_name']]['Kelvin'] = round($base_temperature, 2);
        $temperature_formats_arr[$this->params['city_name']]['Celsius'] = round($base_temperature - 273.15, 2);
        $temperature_formats_arr[$this->params['city_name']]['Fahrenheit'] = round($base_temperature * 9 / 5 - 459.67, 2);

        return $temperature_formats_arr;
    }

}