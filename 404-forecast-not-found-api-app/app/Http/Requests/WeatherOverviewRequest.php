<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeatherOverviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lat'     => ['sometimes', 'numeric', 'between:-90,90'],
            'lon'     => ['sometimes', 'numeric', 'between:-180,180'],
            'city'    => ['sometimes', 'string', 'max:100'],
            'country' => ['sometimes', 'string', 'size:2'],
        ];
    }
}
