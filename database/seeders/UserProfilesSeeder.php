<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Imports\UserProfilesImport;
use Maatwebsite\Excel\Facades\Excel;

class UserProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Excel::import(new UserProfilesImport, 'migrasi_data_ipac.csv');
    }
}
