<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\UniqueSuperadmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $currentUserId = Auth::id();

        // Fetch all users with their related data
        $users = User::where('id', '!=', $currentUserId)
        ->with('userExperience', 'userProfiles', 'userGallery')
        ->get();
        
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
        try {
            $request->validate([
                'username' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role' => 'in:superadmin,admin,alumni', 
                new UniqueSuperadmin, // Apply the custom rule for UniqueSuperadmin
            ]);
    
            // Create the new user
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password), // Hash the password
                'role' => $request->role,
                // 'status' => $request->status,
            ]);
    
            // Optionally, you can also create related models like user profiles, experiences, etc., if needed.
    
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
    
        } catch (\Throwable $th) {
            // Return error response in case of an exception
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $th->getMessage()
            ], 500);
        }
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
        
        $user->userProfiles;
        $user->userGallery;
        $user->userExperience;

        // Old method
        // $user->load('userExperience', 'userProfiles', 'userGallery');

        return response()->json([
            'user' => $user,
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

    public function countUserbyTahunLulus(Request $request)
    {
        $startYear = $request->input('tahun_lulus', date('Y'));
        $userCounts = [];

        for ($i = $startYear; $i >= $startYear - 3; $i--) {
            $userCount = $this->countUsersWithTahunLulus($i, $i - 1);
            $userCounts[$i] = $userCount;
        }

        return response()->json([
            'UserCounts' => $userCounts
        ]);
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
        try {
            $request->validate([
                'username' => [
                    'required',
                    Rule::unique('users')->ignore($id),
                ],
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($id),
                ],
                'password' => 'sometimes|min:6',
                'role' => 'in:admin,alumni',
            ]);
    
            // Find the user by ID
            $user = User::find($id);
    
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            // Get the ID of the currently authenticated user
            $currentUserId = Auth::id();

            // Check if the user being updated is the same as the currently authenticated user
            if (($user->id === $currentUserId)) {
                // If 'role' field is being updated, return an error message
                if ($request->has('role')) {
                    return response()->json([
                        'message' => 'You are not allowed to update your own role!'
                    ], 403);
                }
            }

            // Update the user data
            $user->username = $request->username;
            $user->email = $request->email;
            // $user->status = $request->status;
            
            // Update the role if provided
            if ($request->has('role')) {
                $user->role = $request->role;
            }
    
            // Update the password if provided
            if ($request->has('password')) {
                $user->password = bcrypt($request->password);
            }
    
            // Save the changes
            $user->save();
    
            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);
    
        } catch (\Throwable $th) {
            // Return error response in case of an exception
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
