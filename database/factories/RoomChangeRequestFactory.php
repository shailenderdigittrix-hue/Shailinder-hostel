<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RoomChangeRequest;
use App\Models\Room;
use App\Models\Student;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomChangeRequest>
 */
class RoomChangeRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'current_room_id' => Room::factory(),
            'desired_room_id' => Room::factory(),
            'reason' => $this->faker->sentence,
            'status' => 'pending',
        ];
    }
}
