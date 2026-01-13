<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->input('section') === 'ai') {
            return [
                'openai_api_key' => [
                    'nullable',
                    'string',
                    function ($attr, $value, $fail) {
                        if (!$this->user()->ai_access) {
                            $fail('You are not allowed to update AI settings.');
                        }
                    },
                ],
                'ai_prompt' => [
                    'nullable',
                    'string',
                    function ($attr, $value, $fail) {
                        if (!$this->user()->ai_access) {
                            $fail('You are not allowed to update AI settings.');
                        }
                    },
                ],
                'section' => ['required'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',

                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'section' => ['nullable'],
        ];
    }
}
