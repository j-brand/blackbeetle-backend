<?php

namespace App\Http\Requests\Admin\Story;

use Illuminate\Foundation\Http\FormRequest;

class StoryStore extends FormRequest
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
            'active'              => 'integer',
            'slug'                => 'required|unique:stories|string|max:255',
            'title'               => 'required|unique:stories|string|max:255',
            'description'         => 'string|max:1000',
            'image_upload'        => 'required|mimes:jpeg,bmp,png,jpg,JPG|max:2500',
        ];
    }
}
