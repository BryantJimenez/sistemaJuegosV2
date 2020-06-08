<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Club;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Club::class, function (Faker $faker) {
	$name=$faker->name;

	$count=Club::where('name', $name)->count();
	$slug=Str::slug($name, '-');
	if ($count>0) {
		$slug=$slug.$count;
	}

    // Validación para que no se repita el slug
	$num=0;
	while (true) {
		$count2=Club::where('slug', $slug)->count();
		if ($count2>0) {
			$slug=$slug.$num;
			$num++;
		} else {
			break;
		}
	}
    return [
        'name' => $name,
        'slug' => $slug
    ];
});
