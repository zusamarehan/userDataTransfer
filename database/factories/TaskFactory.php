<?php

/** @var Factory $factory */

use App\Tasks;
use App\ProjectUser;
use App\User;
use App\Project;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Tasks::class, function (Faker $faker) {

    $randomProject = ProjectUser::all()->random();

    return [
        'title' => $faker->name,
        'desc' => $faker->unique()->safeEmail,
        'due_date' => $faker->date(),
        'project_id' => $randomProject->project_id,
        'user_id' => $randomProject->user_id,
    ];
});
