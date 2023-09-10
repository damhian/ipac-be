<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Userprofiles;
use App\Rules\UniqueSuperadmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        $currentUserId = Auth::id();

        // Set the number of items per page, you can adjust this as needed
        // this feature are still not needed
        // $perPage = $request->input('per_page', 10);

        // Fetch all users with their related data
        $query = User::query()
        ->with('userExperience', 'userProfiles', 'userGallery')
        ->select('id', 'email', 'username', 'role', 'status', 'current_status')
        ->addSelect(['first_name' => Userprofiles::select('first_name')
            ->whereColumn('alumni_id', 'users.id')
            ->limit(1)
        ])
        ->addSelect(['last_name' => Userprofiles::select('last_name')
            ->whereColumn('alumni_id', 'users.id')
            ->limit(1)
        ]);
        
        // Apply filters
        if ($request->has('first_name')) {
            $query->whereHas('userProfiles', function ($subquery) use ($request) {
                $subquery->where('first_name', 'like', '%' . $request->input('first_name') . '%');
            });
        }

        if ($request->has('last_name')) {
            $query->whereHas('userProfiles', function ($subquery) use ($request) {
                $subquery->where('last_name', 'like', '%' . $request->input('last_name') . '%');
            });
        }

        if ($request->has('batch')) {
            $query->whereHas('userProfiles', function ($subquery) use ($request) {
                $subquery->where('batch', 'like', '%' . $request->input('batch') . '%');
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

         // Apply sorting
        if ($request->has('sortBy')) {

            $sortDirection = $request->input('sortDir', 'asc');
            $sortBy = $request->input('sortBy');

            // Validate the sort direction to prevent SQL injection
            $validSortDirections = ['asc', 'desc'];

            if (in_array($sortDirection, $validSortDirections) && in_array($sortBy, ['first_name', 'last_name', 'tahun_masuk', 'tahun_lulus', 'batch'])) {
                // Specify the table alias in orderBy
                $query->join('user_profiles', 'user_profiles.alumni_id', '=', 'users.id')
                    ->orderBy($sortBy, $sortDirection);
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        }

        // Paginate the results
        // $users = $query->paginate($perPage);
        
        $users = $query->get();
        
        return response()->json([
            'users' => $users
        ], 200);
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
        $user = User::with('userExperience.company', 'userProfiles', 'userGallery', 'userIdcards')->find($id);

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
                'current_status' => 'in:HIDUP,ALMARHUM,GUGUR DALAM TUGAS'
            ]);

                
            // if ($request->has('current_status')) {
            //     if (!in_array($currentStatus, ['HIDUP', 'ALMARHUM', 'GUGUR DALAM TUGAS'])) {
            //         return response()->json([
            //             'message' => 'Invalid current_status value.'
            //         ], 400);
            //     }

            //     User::where('id', $id)->update(['current_status' => $currentStatus]);
            // }
    
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
            $user->current_status = $request->current_status;
            
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
}
