<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;

class DatabaseSeeder extends Seeder
{

    public $tables = ['users','projects'];
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $faker = new Faker();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($this->tables as $table){
            DB::table($table)->truncate();
        }

         $this->call(UsersTableSeeder::class);
         $this->call(ProjectsTableSeeder::class);

        // pivot table
        foreach(range(1, 50) as $index)
        {
            DB::table('project_user')->insert([
                'project_id' => rand(1,100),
                'user_id' => rand(1, 10)
            ]);
        }

    }
}
