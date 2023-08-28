<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserProfilesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $username = $row['username'];
            $email = $username.'@yopmail.com'; // Assuming username can be used as email

            $userData = [
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($row['password']),
            ];

            // Insert user data into the users table
            $userId = DB::table('users')->insertGetId($userData);

            // Check if the date format is valid before conversion
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $row['date_of_birth'])) {
                $dobParts = explode('/', $row['date_of_birth']);
                $date_of_birth = $dobParts[2] . '-' . $dobParts[1] . '-' . $dobParts[0];
            } else {
                // Handle invalid date format
                $date_of_birth = null; // or set a default value
            }

            // Validate and handle license_number
            $license_number = is_numeric($row['license_number']) ? $row['license_number'] : null;

            $batch = $row['batch'] ? $row['batch'] : null; 

            $userProfileData = [
                'alumni_id' => $userId,
                'nomor_anggota' => $row['nomor_anggota'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'batch' =>  $batch,
                'nationality' => $row['nationality'],
                'gender' => $row['gender'],
                'birth_place' => $row['birth_place'],
                'date_of_birth' => $date_of_birth,
                'phone_number_code' => $row['phone_number_code'],
                'phone_number' => $row['phone_number'],
                'tahun_masuk' => $row['tahun_masuk'],
                'tahun_lulus' => $row['tahun_lulus'],
                'training_program' => $row['training_program'],
                'license_number' => $license_number,
                'current_job' => $row['current_job'],
                'current_workplace' => $row['current_workplace'],
                'status' => $row['status'],
            ];

            // Insert user profile data into the user_profiles table
            DB::table('user_profiles')->insert($userProfileData);
        }
    }
}
