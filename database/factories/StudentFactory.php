<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        $pl = \Faker\Factory::create('pl_PL');

        $classNumber = $this->faker->randomElement(array_keys(\App\Support\StudentClasses::CLASSES));
        $range = \App\Support\StudentClasses::CLASSES[$classNumber];
        $age = $this->faker->numberBetween($range['min'], $range['max']);

        if (str_ends_with($classNumber, 'LO')) {
            $profile = $this->faker->randomElement([
                'humanistyczna',
                'językowa',
                'matematyczno-fizyczna',
                'biologiczno-chemiczna',
                'ogólna',
            ]);
        } else {
            $profile = null;
        }

        $topics = [
            'Funkcje kwadratowe',
            'Równania wykładnicze',
            'Ciągi arytmetyczne i geometryczne',
            'Geometria analityczna',
            'Trygonometria',
            'Rachunek prawdopodobieństwa',
            'Statystyka opisowa',
            'Planimetria',
            'Stereometria',
            'Logarytmy',
        ];

        return [
            'name' => $pl->firstName() . ' ' . $pl->lastName(),
            'age' => $age,
            'class_number' => $classNumber,
            'profile' => $profile,
            'current_topic' => $this->faker->randomElement($topics),
            'description' => $this->faker->randomElement([
                'Uczeń zdolny, ale wymagający systematyczności.',
                'Bardzo dobrze radzi sobie z zadaniami rachunkowymi, słabiej z dowodami.',
                'Przygotowuje się do egzaminu ósmoklasisty.',
                'Przygotowuje się do matury rozszerzonej.',
                'Sumienny uczeń, regularnie odrabia zadane prace.',
                'Wymaga wielu powtórek - słabo utrwala materiał między zajęciami.',
            ]),
            'notes' => $this->faker->randomElement([
                'Rodzice proszą o SMS przed zajęciami.',
                'Preferuje zadania z rozwiązaniem krok po kroku.',
                'Ma problem z zadaniami otwartymi - rozpisuj schematy do śledzenia przed wrzucaniem na głęboką wodę.',
                'Chce porozmawiać trochę przed lekcją - nie zaczynaj od razu z zadaniami.',
                null,
                null,
            ]),
            'next_exam_date' => $this->faker->dateTimeBetween('now', '+14 days')->format('Y-m-d'),
            'schedule' => $this->generateSchedule(),
            'price_per_lesson' => 70,
        ];
    }

    private function generateSchedule(): array
    {
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $times = ['15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00'];

        $count = $this->faker->numberBetween(1, 2);
        $selectedDays = $this->faker->randomElements($weekdays, $count);

        $schedule = [];
        foreach ($selectedDays as $day) {
            $schedule[$day] = $this->faker->randomElement($times);
        }

        return $schedule;
    }
}
