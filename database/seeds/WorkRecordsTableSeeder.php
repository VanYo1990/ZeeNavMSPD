<?php

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use App\Models\WorkRecords;

class WorkRecordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // $csv_file_data = {
        //     [1,,10287489.108,-8973255.529],
        //     [2,,10293035.304,-8970964.163], 
        //     [3,,10300245.359,-8967985.388], 
        //     [4,,10307640.753,-8964930.580], 
        //     [5,,10315365.173,-8961630.521], 
        //     [6,,10322644.526,-8958742.460], 
        //     [7,,10323745.558,-8958265.252], 
        //     [8,,10330383.773,-8955545.053], 
        //     [9,,10331484.806,-8955067.845], 
        //     [10,,10338148.447,-8952337.141], 
        //     [11,,10339249.480,-8951859.932], 
        //     [12,,10345867.245,-8949149.112], 
        //     [13,,10346968.278,-8948671.904], 
        //     [14,,10352383.684,-8946455.968], 
        //     [15,,10353484.716,-8945978.760], 
        //     [16,,10360837.203,-8942951.416], 
        //     [17,,10355151.885,-8949448.119], 
        //     [18,,10355748.396,-8950824.410], 
        //     [19,,10354372.105,-8951420.920], 
        //     [20,,10353775.595,-8950044.630] 
        // }

        // DB::table('work_records')->insert([
            
        //     'machine_sn' => 201705080609,
        //     'project_id' => 24,
        //     'pile_name' => '12',
        //     'content' => {
        //         "stop_at": "2020-09-10 13:19:25:97",
        //         "start_at": "2020-09-10 13:19:25:97",
        //         "use_time": "1.882",
        //         "max_error": "0.589", 
        //         "stop_error": "0.432", 
        //         "is_complete": "true", 
        //         "record_data": "", 
        //         "start_error": "2.864", 
        //         "target_pile_x": "565.000",
        //         "target_pile_y": "567.000"
        //     },
        //     'created_at' => '',
        //     'updated_at' => ''
   
        // ]);

        $workrecords = factory(WorkRecords::class, 200)->make();

        foreach ($workrecords as $record) {

            $read_record_data = WorkRecords::where('project_id', '=',  $record->project_id)
                                        ->where('machine_sn', '=',  $record->machine_sn)
                                        ->where('pile_name', '=',  $record->pile_name)
                                        ->first();

            if(!$read_record_data){
                $record->save();
            }
            else{
                $record->id = $read_record_data->id;
                $record->update();

                //throw new Exception($read_record_data);
            }
            
        }
       
    }
}
