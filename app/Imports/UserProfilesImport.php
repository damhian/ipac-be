<?php

namespace App\Imports;

use App\Models\UserProfiles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class UserProfilesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {   
        // Prepare the data for insertion
        foreach ($rows as $row) {
            // dd($rows);

            // Validate that email and username are not empty
            if (empty($row['email']) || empty($row['username'])) {
                // Skip this row and log a message (you can log to Laravel's log system)
                Log::error('Skipped a row due to missing email or username: ' . json_encode($row));
                continue; // Skip this row and move to the next one
            }
            // Determine the role, status, currentStatus, and other variables with default values
            $role = $row['role'] ? $row['role'] : 'alumni';
            $status = 'approved';
            $currentStatus = $row['current_status'] ? $row['current_status'] : 'HIDUP';
            $licenseNumber = is_numeric($row['license_number']) ? $row['license_number'] : null;
            $batch = $row['batch'] ? $row['batch'] : null;
            $nationality = $row['nationality'] ? $row['nationality'] : 'INDONESIA';
            $currentJob = $row['current_job'] ? $row['current_job'] : null;
            $currentWorkplace = $row['current_workplace'] ? $row['current_workplace'] : null;
            $address = $row['address'] ? $row['address'] : null;

            // Generate nomorAnggota
            $lastThreeDigit = UserProfiles::where('tahun_lulus', $row['tahun_lulus'])->count() + 1;
            $lastThreeDigit = str_pad($lastThreeDigit, 3, '0', STR_PAD_LEFT);
            $nomorAnggota = $row['tahun_lulus'] . $lastThreeDigit;

            // Check for duplicate usernames
            $username = $row['username'];
            if (UserProfiles::where('alumni_id', '<>', null)->where('nomor_anggota', $nomorAnggota)->exists()) {
                // If duplicates found, update username with nomorAnggota
                $username = $nomorAnggota;
            }

            // Create user data array
            $userData = [
                'email' => $row['email'],
                'username' => $username,
                'password'  => bcrypt('ipac2023'),
                'role' => $role,
                'status' => $status,
                'current_status' => $currentStatus,
            ];

            // Insert user data into the users table
            $userId = DB::table('users')->insertGetId($userData);

            // Create user profile data array
            $userProfileData = [
                'alumni_id' => $userId,
                'nomor_anggota' => $nomorAnggota,
                'license_number' => $licenseNumber,
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'tahun_masuk' => $row['tahun_masuk'],
                'tahun_lulus' => $row['tahun_lulus'],
                'training_program' => $row['training_program'],
                'batch' => $batch,
                'current_job' => $currentJob,
                'current_workplace' => $currentWorkplace,
                'birth_place' => $row['birth_place'],
                'date_of_birth' => $row['date_of_birth'],
                'nationality' => $nationality,
                'address' => $address,
                'phone_number_code' => $row['phone_number_code'],
                'phone_number' => $row['phone_number'],
                'gender' => $row['gender'],
            ];

            // Insert user profile data into the user_profiles table
            DB::table('user_profiles')->insert($userProfileData);
        }
    }
}
