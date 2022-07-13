<?php

namespace App\Http\Requests\Album;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use App\Models\Album;

class AlbumUpdate extends FormRequest
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
            'title'               => ['string', 'max:255', Rule::unique('albums')->ignore(Album::find($this->id)->id)],
            'slug'                => ['string', 'max:255', Rule::unique('albums')->ignore(Album::find($this->id)->id)],
            'start_date'          => 'date|date_format:Y-m-d',
            'end_date'            => 'date|date_format:Y-m-d',
            'description'         => 'max:1000',
            'title_image_text'    => 'max:255',
            'image_upload'        => 'mimes:jpeg,bmp,png,jpg,JPG',
        ];
    }
}
