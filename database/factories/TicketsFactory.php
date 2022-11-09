<?php

namespace Database\Factories;

use App\Models\User as User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TicketsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();

        if (!empty($user)) {
            return [
                'subject'   => 'SeededTicket#',
                'content'   => $this->faker->sentence(rand(10,25)),
                'user_name' => $user->name,
                'user_email'=> $user->email,
                'status'    => 0,
            ];
        }
    }
}
