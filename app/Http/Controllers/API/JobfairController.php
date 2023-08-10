<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobfairRequest;
use App\Models\Jobfair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobfairController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function __construct()
    {
        $this->middleware('admin')->only('updateStatus');
    }

    public function index()
    {
        $jobfairs = Jobfair::with(['user.userProfiles'])
        ->where('status', '!=', 'deleted')
        ->get();

        return response()->json([
            'jobfairs' => $jobfairs
        ]);
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
    public function store(JobfairRequest $request)
    {
        try {
            // Create Jobfair
            Jobfair::create([
                'title' => $request->title,
                'content' => $request->content,
                'short_description' => $request->short_description,
                'region' => $request->region,
                'company' => $request->company,
                'jobtype' => $request->jobtype,
                'jobtitle' => $request->jobtitle,
                'jobtype' => $request->jobtype,
                'location_name' => $request->location_name,
                'location_lon' => $request->location_lon,
                'location_lat' => $request->location_lat,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Jobfair succesfully created'
            ], 200);

        } catch (\Throwable $th) {
            // return json response
            return response()->json([
                'message' => 'something went wrong!',
                'error message' => $th
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Get Jobfair data by id
        $jobfair = Jobfair::find($id);

        if(!$jobfair)
            return response()->json([
                'message' => 'Jobfair not found!'
            ], 404);

        // Return response success
        return response()->json([
            'Jobfair' => $jobfair
        ], 200);
    }

    public function showByToken()
    {
        // Get the authenticated user's token
        $user = Auth::user();

        // Find the store associated with the token
        $jobfair = Jobfair::with(['user.userProfiles'])
        ->where('created_by', $user->id)
        ->get();

        if (!$jobfair) {
            return response()->json([
                'message' => 'Store not found!'
            ], 404);
        }

        // Return response success
        return response()->json([
            'jobfair' => $jobfair
        ], 200);
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
    public function update(JobfairRequest $request, string $id)
    {
        try {
            // Find Jobfair
            $jobfair = Jobfair::find($id);

            if(!$jobfair)
                return response()->json([
                    'message' => 'Jobfair not found!'
                ]);

            $jobfair->title = $request->title;
            $jobfair->content = $request->content;
            $jobfair->short_description = $request->short_description;
            $jobfair->region = $request->region;
            $jobfair->company = $request->company;
            $jobfair->jobtype = $request->jobtype;
            $jobfair->jobtitle = $request->jobtitle;
            $jobfair->jobtype = $request->jobtype;
            $jobfair->location_name = $request->location_name;
            $jobfair->location_lon = $request->location_lon;
            $jobfair->location_lat = $request->location_lat;
            $jobfair->start_at = $request->start_at;
            $jobfair->end_at = $request->end_at;

            $jobfair->save();

            return response()->json([
                'message' => 'Jobfair successfully updated'
            ]);

        } catch (\Throwable $th) {
             // return json response
             return response()->json([
                'message' => 'something went wrong!',
                'error message' => $th
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        try {
            // Find event to delete
            $jobfair = Jobfair::find($id);

            if(!$jobfair)
                return response()->json([
                    'message' => 'Jobfair not found!'
                ]);
            
            $jobfair->status = 'deleted';

            $jobfair->save();
            
            return response()->json([
                'message' => 'Jobfair successfully deleted'
            ]);
            
        } catch (\Throwable $th) {
             // return json response
             return response()->json([
                'message' => 'something went wrong!',
                'error message' => $th
            ], 500);
        }
    }

    public function search(Request $request)
    {
        // dd($request);
        try {
            // Get the search query from the request parameters
            $searchQuery = $request->search;

            // Search for jobfairs based on the username or jobfair title
            $jobfairs = Jobfair::with('user')
            ->where('title', 'like', '%' . $searchQuery . '%')
            ->orWhereHas('user', function ($query) use ($searchQuery) {
                $query->where('username', 'like', '%' . $searchQuery . '%');
            })
            ->get();

            return response()->json([
                'jobfairs' => $jobfairs
            ], 200);

        } catch (\Throwable $th) {
            // return json response
            return response()->json([
                'message' => 'Something went wrong!',
                'error message' => $th
            ], 500);
        }
    }

}
