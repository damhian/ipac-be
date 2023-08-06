<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserexperienceRequest;
use App\Models\Userexperiences;
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
        $userexp = Userexperiences::with(['user.userProfiles'])->get();

        return response()->json([
            'User_experiences' => $userexp
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

    public function showByToken()
    {
        // Get the authenticated user's token
        $user = Auth::user();
        
        // Find the store associated with the token
        $userexperience = Userexperiences::with(['user.userProfiles'])->where('alumni_id', $user->id)->get();

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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
}
