<?php

use App\Student;
use Illuminate\Database\Seeder;

/**
 * Class StudentsTableSeeder
 */
class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        factory(Student::class, 10)->create();
    }
}
