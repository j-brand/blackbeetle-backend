<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Posttype implements Rule
{

    protected $posttypes = array('image', 'html', 'map', 'video');

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, $this->posttypes);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Der angegebene post type ist nicht gültig';
    }
}
