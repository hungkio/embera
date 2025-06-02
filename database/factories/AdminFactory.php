<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domain\Admin\Models\Admin;
use Faker\Generator as Faker;

$factory->define(Admin::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});


$factory->define(\App\Domain\Taxonomy\Models\Taxonomy::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->define(\App\Domain\Taxonomy\Models\Taxon::class, function (Faker $faker) {
    $name = $faker->word;

    return [
        'name' => $name,
        'slug' => Str::slug($name),
        'description' => $faker->paragraph,
        'meta_title' => $faker->sentence,
        'meta_description' => $faker->sentence,
        'meta_keywords' => $faker->sentence,
    ];
});
