<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserstoryRequest;
use App\Models\Userstory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserstoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userstory = Userstory::with(['user.userProfiles'])->get();

        return response()->json([
            'User story' => $userstory
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
    public function store(UserstoryRequest $request)
    {
        try {
            //Create User Story
            Userstory::create([
                'alumni_id'     => Auth::id(),
                'story'         => $request->story,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'User story successfully created'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'something went wrong!' 
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $userstory = Userstory::where('alumni_id', $id)->first();

            if(!$userstory)
                return response()->json([
                    'message' => 'User story not found!'
                ], 404);

        return response()->json([
            'message' => $userstory
        ], 200);
    }

    public function showByToken()
    {
        // Get the authenticated user's token
        $user = Auth::user();

        // Find the store associated with the token
        $userstory = Userstory::with(['user.userProfiles'])->where('alumni_id', $user->id)->get();

        if (!$userstory) {
            return response()->json([
                'message' => 'Userstory not found!'
            ], 404);
        }

        // Return response success
        return response()->json([
            'User story' => $userstory
        ], 200);
    }

    public function showByUserId(string $id)
    {
        // Get the authenticated user's
        // $user = Auth::user();
        
        // Find the user story associated with the user id from their login
        $userstory = Userstory::with(['user.userProfiles'])->where('alumni_id', $id)->get();

        if (!$userstory) {
            return response()->json([
                'message' => 'userstory not found!'
            ], 404);
        }

        // Return response success
        return response()->json([
            'User story' => $userstory
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
    public function update(UserstoryRequest $request, string $id)
    {
        try {
            //Find user story by alumni_id as user id
            $userstory = Userstory::where('alumni_id', $id)->first();

            if(!$userstory)
                return response()->json([
                    'message' => 'User story not found!'
                ], 404);
            
            if($userstory->alumni_id != Auth::id())
                return response()->json([
                    'message' => 'Unauthorized!'
                ], 401);

            $userstory->alumni_id   = Auth::id();
            $userstory->story       = $request->story;

            $userstory->save();

            return response()->json([
                'message' => 'User story successfully updated'
            ], 200);
            
        } catch (\Throwable $th) {
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
        try {
            $userstory = Userstory::where('alumni_id', $id)->first();

            if(!$userstory)
                return response()->json([
                    'message' => 'User story not found!'
                ], 404);
            
            $userstory->delete();
            
            return response()->json([
                'message' => 'User story successfully deleted'
            ]);
        
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'something went wrong!',
                'error message' => $th
            ], 500);
        }
    }
}
