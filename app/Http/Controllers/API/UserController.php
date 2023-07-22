<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         // Fetch all users with their related data
         $users = User::with('userExperience', 'userProfiles', 'userGallery')->get();

         return response()->json([
             'users' => $users
         ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with('userExperience', 'userProfiles', 'userGallery')->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'user' => $user
        ], 200);
    }

    public function showbytoken()
    {
        $user = Auth::user();

        $user->load('userExperience', 'userProfiles', 'userGallery');

        return response()->json([
            'user' => $user
        ], 200);
    }

    public function showUserbyTahunLulus(Request $request)
    {
        $tahunLulus = $request->input('tahun_lulus', null);

        $usersWithProfilesCount = $this->countUsersWithTahunLulus($tahunLulus);

        return response()->json([
            'User' => $usersWithProfilesCount
        ]);

    }

    private function countUsersWithTahunLulus($tahunLulus = null)
    {
        $query = User::query();

        // If 'tahun_lulus' is provided, filter users based on 'tahun_lulus'
        if ($tahunLulus !== null) {
            $query->whereHas('userProfiles', function ($query) use ($tahunLulus) {
                $query->where('tahun_lulus', $tahunLulus);
            });
        }

        // Exclude users with null 'tahun_lulus'
        $query->whereHas('userProfiles', function ($query) {
            $query->whereNotNull('tahun_lulus');
        });

        return $query->count();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
