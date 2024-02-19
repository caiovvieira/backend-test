<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Closure;

class UpdateUrl extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'url' => [
                'nullable',
                'max:255',
                'url:https',
                function (string $attribute, mixed $value, Closure $fail) {
                    $localPathPositionString = strpos(url()->current(), "//");
                    $localPathFormatted = substr(url()->current(), $localPathPositionString + 2);
                    $localPathPositionString = strpos($localPathFormatted, "/");
                    $localPathUrl = substr($localPathFormatted, 0, $localPathPositionString);
                    
                    $stringFormatted = substr($value, 8);
                    $positionString = strpos($stringFormatted, "/");
                    $requestUrl = substr($stringFormatted, 0, $positionString);

                    if ($localPathUrl === $requestUrl) {
                        $fail("The {$attribute} is invalid.");
                    }
                },
            ],
            'status' => 'nullable|boolean'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response($validator->errors(), 400)
                ->header('Content-Type', 'application/json')
        );
    }
}
