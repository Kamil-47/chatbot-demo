<?php

namespace Database\Factories;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    public function definition(): array
    {
        $date = Carbon::parse($this->faker->dateTimeBetween('-1 month', '+1 month'));

        return [
            'student_id' => Student::factory(),
            'lesson_date' => $date->format('Y-m-d'),
            'time' => $this->faker->randomElement(['15:00', '16:00', '17:00', '18:00', '19:00']),
            'status' => 'planned',
            'month' => $date->format('Y-m'),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => 'completed']);
    }

    public function canceled(): static
    {
        return $this->state(fn () => ['status' => 'canceled']);
    }
}
