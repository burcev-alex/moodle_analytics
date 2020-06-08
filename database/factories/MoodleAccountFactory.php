<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\MoodleAccount;
use Faker\Generator as Faker;
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

$factory->define(MoodleAccount::class, function (Faker $faker) {
    return [
        'full_name' => $faker->name,
        'domain' => 'www.dfn.mdpu.org.ua',
        'endpoint' => 'http://www.dfn.mdpu.org.ua/enpoint',
        'api_key' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    ];
});
