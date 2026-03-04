<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'unit_group_code' => ['required'],
            'unit_group_name' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
