<?php

namespace App\Http\Requests;

use App\Models\Link;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLinkRequest extends FormRequest
{
    /**
     * Codes that would shadow an existing application route.
     *
     * @var array<int, string>
     */
    private const RESERVED_CODES = ['api', 'up', 'storage'];

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'destination' => ['required', 'string', 'max:2048', 'url:http,https'],
            'code' => [
                'nullable',
                'string',
                'min:3',
                'max:32',
                'alpha_dash',
                Rule::notIn(self::RESERVED_CODES),
                Rule::unique(Link::class, 'code'),
            ],
        ];
    }
}
