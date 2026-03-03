<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'unit_code' => ['required'],
            'unit_name' => ['required'],
            'unit_abbr' => ['required'],
            'unit_group_code' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
