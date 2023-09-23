<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Userprofiles;
use App\Rules\UniqueSuperadmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

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
        
        $searchTerm = $request->input('search');

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
        ])
        ->addSelect(['training_program' => Userprofiles::select('training_program')
            ->whereColumn('alumni_id', 'users.id')
            ->limit(1)
        ])
        ->addSelect(['batch' => Userprofiles::select('batch')
            ->whereColumn('alumni_id', 'users.id')
            ->limit(1)
        ])
        ->addSelect(['current_job' => Userprofiles::select('current_job')
            ->whereColumn('alumni_id', 'users.id')
            ->limit(1)
        ])
        ->addSelect(['current_workplace' => Userprofiles::select('current_workplace')
            ->whereColumn('alumni_id', 'users.id')
            ->limit(1)
        ])
        ->addSelect(['gender' => Userprofiles::select('gender')
            ->whereColumn('alumni_id', 'users.id')
            ->limit(1)
        ]);

        // If a search term is provided, apply the search filter
        if (!empty($searchTerm)) {
            $query->where(function ($subquery) use ($searchTerm) {
                $subquery->where('username', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('userProfiles', function ($subquery) use ($searchTerm) {
                        $subquery->where('first_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('userProfiles', function ($subquery) use ($searchTerm) {
                        $subquery->where('batch', 'like', '%' . $searchTerm . '%')
                            ->orWhere('training_program', 'like', '%' . $searchTerm . '%')
                            ->orWhere('current_job', 'like', '%' . $searchTerm . '%')
                            ->orWhere('current_workplace', 'like', '%' . $searchTerm . '%');
                    });
            });
        }
        
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

        if ($request->has('training_program')) {
            $query->whereHas('userProfiles', function ($subquery) use ($request) {
                $subquery->where('training_program', 'like', '%' . $request->input('training_program') . '%');
            });
        }

        if ($request->has('current_job')) {
            $query->whereHas('userProfiles', function ($subquery) use ($request) {
                $subquery->where('current_job', 'like', '%' . $request->input('current_job') . '%');
            });
        }

        if ($request->has('current_workplace')) {
            $query->whereHas('userProfiles', function ($subquery) use ($request) {
                $subquery->where('current_workplace', 'like', '%' . $request->input('current_workplace') . '%');
            });
        }

        if ($request->has('gender')) {
            $query->whereHas('userProfiles', function ($subquery) use ($request) {
                $subquery->where('gender', 'like', '%' . $request->input('gender') . '%');
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
        
        $users = $query->get();
        
        return response()->json([
            'users' => $users
        ], 200);
    }

    public function exportFilteredUsersWithProfiles(Request $request)
    {
        try {
            // Fetch all users with their related data and apply filters
            // $currentUserId = Auth::id();

            $query = User::query()
                ->with('userProfiles')
                ->select('id', 'email', 'username', 'role', 'status', 'current_status')
                ->addSelect([
                    'nomor_anggota' => Userprofiles::select('nomor_anggota')
                    ->whereColumn('alumni_id', 'users.id')
                ])
                ->addSelect([
                    'license_number' => Userprofiles::select('license_number')
                    ->whereColumn('alumni_id', 'users.id')
                ])
                ->addSelect([
                    'first_name' => Userprofiles::select('first_name')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'last_name' => Userprofiles::select('last_name')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'tahun_masuk' => Userprofiles::select('tahun_masuk')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'tahun_lulus' => Userprofiles::select('tahun_lulus')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'training_program' => Userprofiles::select('training_program')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'batch' => Userprofiles::select('batch')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'current_job' => Userprofiles::select('current_job')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'current_workplace' => Userprofiles::select('current_workplace')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'birth_place' => Userprofiles::select('birth_place')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'date_of_birth' => Userprofiles::select('date_of_birth')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'nationality' => Userprofiles::select('nationality')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'address' => Userprofiles::select('address')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'phone_number_code' => Userprofiles::select('phone_number_code')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'phone_number' => Userprofiles::select('phone_number')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ])
                ->addSelect([
                    'gender' => Userprofiles::select('gender')
                        ->whereColumn('alumni_id', 'users.id')
                        ->limit(1)
                ]);

            // If a search term is provided, apply the search filter
            if (!empty($searchTerm)) {
                $query->where(function ($subquery) use ($searchTerm) {
                    $subquery->where('username', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('userProfiles', function ($subquery) use ($searchTerm) {
                            $subquery->where('first_name', 'like', '%' . $searchTerm . '%')
                                ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
                        })
                        ->orWhereHas('userProfiles', function ($subquery) use ($searchTerm) {
                            $subquery->where('batch', 'like', '%' . $searchTerm . '%')
                                ->orWhere('training_program', 'like', '%' . $searchTerm . '%')
                                ->orWhere('current_job', 'like', '%' . $searchTerm . '%')
                                ->orWhere('current_workplace', 'like', '%' . $searchTerm . '%');
                        });
                });
            }

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

            if ($request->has('training_program')) {
                $query->whereHas('userProfiles', function ($subquery) use ($request) {
                    $subquery->where('training_program', 'like', '%' . $request->input('training_program') . '%');
                });
            }

            if ($request->has('current_job')) {
                $query->whereHas('userProfiles', function ($subquery) use ($request) {
                    $subquery->where('current_job', 'like', '%' . $request->input('current_job') . '%');
                });
            }

            if ($request->has('current_workplace')) {
                $query->whereHas('userProfiles', function ($subquery) use ($request) {
                    $subquery->where('current_workplace', 'like', '%' . $request->input('current_workplace') . '%');
                });
            }

            if ($request->has('gender')) {
                $query->whereHas('userProfiles', function ($subquery) use ($request) {
                    $subquery->where('gender', 'like', '%' . $request->input('gender') . '%');
                });
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $filteredUsers = $query->get();

            // Create an array to store the export data
            $exportData = [];

            // Add headers to the export data
            $exportData[] = [
                'Username',
                'Email',
                'Role',
                'Status',
                'Current Status',
                'Nomor Anggota',
                'License Number',
                'First Name',
                'Last Name',
                'Tahun Masuk',
                'Tahun Lulus',
                'Training Program',
                'Batch',
                'Current Job',
                'Current Workplace',
                'Birth Place',
                'Date of Birth',
                'Nationality',
                'Address',
                'Phone Number Code',
                'Phone Number',
                'Gender',
            ];

            // Iterate through filtered users and add them to the export data
            foreach ($filteredUsers as $user) {
                $exportData[] = [
                    $user->username,
                    $user->email,
                    $user->role,
                    $user->status,
                    $user->current_status,
                    $user->nomor_anggota,
                    $user->license_number,
                    $user->first_name,
                    $user->last_name,
                    $user->tahun_masuk,
                    $user->tahun_lulus,
                    $user->training_program,
                    $user->batch,
                    $user->current_job,
                    $user->current_workplace,
                    $user->birth_place,
                    $user->date_of_birth,
                    $user->nationality,
                    $user->address,
                    $user->phone_number_code,
                    $user->phone_number,
                    $user->gender,
                ];
            }

            // Define the default format (e.g., XLSX)
            $format = 'xlsx';

            // Check if a 'format' parameter is provided in the request
            if ($request->has('format')) {
                $requestedFormat = strtolower($request->input('format'));

                // Check if the requested format is supported
                if ($requestedFormat === 'csv') {
                    $format = 'csv';
                }
                // You can add more format checks here if needed
            }
            
            $format = $request->input('format') == 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;

            // dd($format);
            if ($format == 'Xlsx') {
                $fileName = 'user_export_' . date('Y-m-d_H-i-s') . '.xlsx';
            } else {
                $fileName = 'user_export_' . date('Y-m-d_H-i-s') . '.csv';
            }

            // Export the data using Maatwebsite Excel
            return Excel::download(new UsersExport($exportData), $fileName, $format);

        } catch (\Throwable $th) {
            // Handle any errors that occur during export
            return response()->json([
                'message' => 'Failed to export data',
                'error' => $th->getMessage()
            ], 500);
        }
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
