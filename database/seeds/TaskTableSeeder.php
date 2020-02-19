<?php

use Illuminate\Database\Seeder;
use App\Tasks;

class TaskTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(Tasks::class, 100)->create();
    }
}
