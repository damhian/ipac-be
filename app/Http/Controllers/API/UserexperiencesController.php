<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserexperienceRequest;
use App\Models\Userexperiences;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserexperiencesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userexp = Userexperiences::with(['company', 'user.userProfiles'])
        ->where('status', '!=', 'deleted')
        ->get();

        return response()->json([
            'User_experiences' => $userexp
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserexperienceRequest $request)
    {
        try {
            // Create User Experience
            Userexperiences::create([
                'alumni_id' => Auth::id(),
                'company_id' => $request->company_id,
                'position' => $request->position,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'User experience successfully created'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'something went wrong!',
                'error' => $th
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Get experiences data by id
        $userexp = Userexperiences::find($id);

        if(!$userexp)
            return response()->json([
                'message' => 'User experience for current id not found!'
            ], 404);
        
        // Return response success
        return response()->json([
            'Store' => $userexp
        ], 200);
    }

    public function showByToken(Request $request)
    {
        // Get the authenticated user's token
        $user = Auth::user();
        
        // Find the store associated with the token
        $query = Userexperiences::with(['user.userProfiles'])
                        ->where('status', '!=', 'deleted')
                        ->where('alumni_id', $user->id);
                        
        if ($request->has('position')) {
            $query->where('position', 'like', '%' . $request->position . '%');
        }

        if ($request->has('startAt')) {
            // Convert the date string to a Carbon date object
            $startDate = Carbon::parse($request->input('startAt'));

            // Use the converted date in the query
            $query->where('start_at', '=', $startDate);
        }

        if ($request->has('endAt')) {
            $endDate = Carbon::parse($request->input('endAt'));
            $query->where('end_at', '=', $endDate);
        }

        $userexperience = $query->get();

        if (!$userexperience) {
            return response()->json([
                'message' => 'User experience not found!'
            ], 404);
        }

        // Return response success
        return response()->json([
            'user experience' => $userexperience
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserexperienceRequest $request, string $id)
    {
        try {
            //Find user experiences by id user
            $userexp = Userexperiences::find($id);
            
            if(!$userexp)
                return response()->json([
                    'message' => 'User experience related to this user not found!'
                ], 404);
            
            if($userexp->alumni_id != Auth::id())
                return response()->json([
                    'message' => 'Unauthorized!'
                ], 401);
            
            $userexp->position = $request->position;
            $userexp->company_id = $request->company_id;
            $userexp->start_at = $request->start_at;
            $userexp->end_at = $request->end_at;

            $userexp->save();
    
            return response()->json([
                'message' => 'User Experience updated successfully',
                'user_experience' => $userexp
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
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        try {
            
            $userexp = Userexperiences::find($id);

            if(!$userexp)
                return response()->json([
                    'message' => 'User experiences not found!'
                ], 404);

            $userexp->status = 'deleted';

            $userexp->save();

            DB::commit();

            return response()->json([
                'message' => 'User experiences successfully deleted'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Something went wrong!'
            ]);
        }
    }
}
