<?php

class WeatherRepository
{
    public function store(array $data)
    {
        return WeatherRecord::create($data);
    }

    public function getByCity(string $city)
    {
        return WeatherRecord::where('city', $city)->latest()->get();
    }
}

