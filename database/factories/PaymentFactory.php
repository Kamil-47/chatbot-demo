<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $lessonCount = $this->faker->numberBetween(4, 12);

        return [
            'student_id' => Student::factory(),
            'amount' => $lessonCount * 70,
            'month' => now()->format('Y-m'),
            'status' => 'waiting',
            'lesson_count' => $lessonCount,
            'payment_date' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => 'paid',
            'payment_date' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
        ]);
    }
}
