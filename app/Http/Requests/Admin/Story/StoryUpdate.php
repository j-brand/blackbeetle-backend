<?php

namespace App\Http\Requests\Admin\Story;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Story;
use Illuminate\Validation\Rule;

class StoryUpdate extends FormRequest
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
            'active'              => 'boolean',
            'title'               => ['string', 'max:255', Rule::unique('stories')->ignore(Story::find($this->id)->id)],
            'slug'                => ['string', 'max:255', Rule::unique('stories')->ignore(Story::find($this->id)->id)],
            'description'         => 'max:1000',
            'image_upload'        => 'image|mimes:jpeg,bmp,png,jpg,JPG',
        ];
    }
}
