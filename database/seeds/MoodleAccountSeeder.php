<?php

use Illuminate\Database\Seeder;
use App\MoodleAccount;

class MoodleAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\MoodleAccount::class, 1)->make();
    }
}
