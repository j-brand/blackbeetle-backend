<?php

namespace App\Http\Requests\PublicFrontend\Newsletter;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionUpdate extends FormRequest
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
            'subscriptions' => "nullable|string",
            'token' => 'required|uuid|exists:newsletter_subscriptions,token'
        ];
    }
}
