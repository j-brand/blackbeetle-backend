<?php

namespace App\Http\Requests\Album;

use Illuminate\Foundation\Http\FormRequest;

class AlbumStore extends FormRequest
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
            'title'               => 'required|unique:albums|string|max:255',
            'slug'                => 'required|unique:albums|string|max:255',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date',
            'description'         => 'string|max:1000',
            'title_image_text'    => 'max:255',
            'image_upload'        => 'required|mimes:jpeg,bmp,png,jpg,JPG|max:2500',
        ];
    }
}
