<?php

namespace Database\Factories;

use App\Models\Subscriber;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubscriberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscriber::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'verified_at' => now(),
            'token' => Str::random(10),
        ];
    }
    /**
     * Configure the model factory.
     *
     * @return $this
     */

    public function configure()
    {
        return $this->afterMaking(function (Subscriber $sub) {
            //
        })->afterCreating(function (Subscriber $sub) {

            $s = new Subscription();
            $s->option = $this->faker->randomElement(['S1', 'S2']);

            $sub->subscriptions()->save($s);
        });
    }


}