<?php

class BadersWeather
{
    const API_HOST = 'https://api.openweathermap.org/data/2.5/weather?';
    const API_KEY = '4d3a9c30a8cf9536374594aca2874811';
    const UNITS = 'metric';
    const TRANSIENT_KEY = '_baders_weather_transient';
    const POLL = 45 * 60;
    //init object properties
    private function __construct($weather, $main, $desc, $location, $icon)
    {
        $this->weather = $weather;
        $this->main = $main;
        $this->desc = $desc;
        $this->location = $location;
        $this->icon = $icon;
    }
    //decode json data and return BadersWeather object
    private static function weatherData($res)
    {
        $jsondata = json_decode($res);
        if ($jsondata->cod === 200) {
            return new self(round($jsondata->main->temp), $jsondata->weather[0]->main, $jsondata->weather[0]->description, $jsondata->name, $jsondata->weather[0]->icon);
        } else return 'fail';
    }
    //get weather data
    public static function getWeather($lat, $long)
    {
        //check if cached data exists else  retrieve data from api
        if ($data = get_transient(self::TRANSIENT_KEY)) {
            return self::weatherData($data);
        } else {
            $data = array('lat' => $lat, 'lon' => $long, 'units' => self::UNITS, 'appid' => self::API_KEY);
            $get_data = http_build_query($data);
            $url = self::API_HOST . $get_data;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error: ' . curl_error($ch);
            }
            curl_close($ch);
            set_transient(self::TRANSIENT_KEY, $result, self::POLL);
            update_option('_cached_timestamp', time());
            return self::weatherData($result);
        }
    }
    //delete transient function for when lat and long updated
    public static function clearTransient(){
        delete_transient(self::TRANSIENT_KEY);
    }
}
