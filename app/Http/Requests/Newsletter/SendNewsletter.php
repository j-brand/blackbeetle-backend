<?php

namespace App\Http\Requests\Newsletter;

use Illuminate\Foundation\Http\FormRequest;

class SendNewsletter extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'option'            => 'required|string',
            'subject'            => 'required|string',
            'content'            => 'required|string',
            'image'            => 'nullable|string',
            'slug'            => 'required|string',
        ];
    }
}