<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClearUsernameDuplicate extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        // dd('masuk aaa');
        try {
            // Step 1: Identify Duplicates
            $duplicateUsernames = User::select('username')
                ->groupBy('username')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('username');

            // Step 2: Fetch Users with Duplicates
            $usersWithDuplicates = User::whereIn('username', $duplicateUsernames)
            ->with('userProfiles')
            ->get();

            if (!$usersWithDuplicates) 
                return response()->json([
                    'message' => 'User with duplicates username not found!'
                ], 404);
            
            return response()->json([
                'Duplicates User' => $usersWithDuplicates
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateDuplicateUsername()
    {
        try {
            // Step 1: Identify Duplicates
            $duplicateUsernames = User::select('username')
                ->groupBy('username')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('username');

            // Step 2: Fetch Users with Duplicates
            $usersWithDuplicates = User::whereIn('username', $duplicateUsernames)
            ->whereHas('userProfiles')
            ->get();

            // Step 3: Update Usernames
            foreach ($usersWithDuplicates as $user) {
                // Fetch corresponding nomor_anggota from user_profiles
                $nomorAnggota = UserProfiles::where('alumni_id', $user->id)->value('nomor_anggota');

                // Update username with nomor_anggota
                $user->update(['username' => $nomorAnggota]);
            }

            return response()->json([
                'message' => 'User with duplicates username and has user profiles has updated'
            ], 200);

            // Log success
            Log::info('Usernames updated successfully');
        } catch (\Exception $e) {
            // Log error
            Log::error('Error updating usernames: ' . $e->getMessage());

            // Re-throw the exception for Laravel to handle
            throw $e;
        }
    }

}
