<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Post;
use Illuminate\Validation\Rule;

class BlogPostUpdate extends FormRequest
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
            'active'   => 'integer',
            'date'     => 'date',
            'title'    => ['string', 'max:255', Rule::unique('posts')->ignore(Post::find($this->id)->id)],
            'content'  => '',
            'position' => 'integer'
        ];
    }
}
