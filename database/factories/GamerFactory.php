<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Gamer;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Gamer::class, function (Faker $faker) {
	$name=$faker->name;
	$lastname=$faker->lastname;

	$count=Gamer::where('name', $name)->where('lastname', $lastname)->count();
	$slug=Str::slug($name." ".$lastname, '-');
	if ($count>0) {
		$slug=$slug.$count;
	}

    // Validación para que no se repita el slug
	$num=0;
	while (true) {
		$count2=Gamer::where('slug', $slug)->count();
		if ($count2>0) {
			$slug=$slug.$num;
			$num++;
		} else {
			break;
		}
	}

	return [
		'name' => $name,
		'lastname' => $lastname,
		'photo' => 'usuario.png',
		'slug' => $slug
	];
});
