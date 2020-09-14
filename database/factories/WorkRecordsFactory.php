<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\WorkRecords;
use Faker\Generator as Faker;

$factory->define(WorkRecords::class, function (Faker $faker) {
    $date_time = $faker->date . ' ' . $faker->time;
    $date_time = $faker->dateTimeBetween('2020-08-12', 'now')->format('Y-m-d H:i:s');

    $array_sn = [200727172836,200727172835,200730231235,201705080609];
    return [
        //
        'id' => null,
        'machine_sn' => $array_sn[$faker->numberBetween(0, 3)],
        'project_id' => $faker->numberBetween(1, 24),
        'pile_name' => strval($faker->numberBetween(1, 500)),       //$faker->numerify('ABC###')    这将生成一个以 ABC 开头的代码，后跟三位数字
        'content' => Json_encode([
            "stop_at" => $date_time,
            "start_at" => $date_time,
            "use_time" => $faker->randomFloat(3, 1, 10), 
            "max_error" => $faker->randomFloat(3, 0, 10),
            "stop_error" => $faker->randomFloat(3, 0, 10),
            "is_complete" => "true", 
            "record_data" => '',
            "start_error" => $faker->randomFloat(3, 0, 10), 
            "target_pile_x" => $faker->randomFloat(3, 1000, 10000),
            "target_pile_y" => $faker->randomFloat(3, 1000, 10000),
        ]),
        'created_at' => $date_time,
        'updated_at' => $date_time
    ];
});
