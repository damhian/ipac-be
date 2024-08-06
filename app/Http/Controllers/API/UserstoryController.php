<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserstoryRequest;
use App\Models\Userstory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

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
     * Store a newly created resource in storage.
     */
    public function store(UserstoryRequest $request)
    {
        try {

            $image = $request->image == null || empty($request->image) ? '' : $request->image;
            $path = $image->store('storyimage', 'public');

            //Create User Story
            Userstory::create([
                'alumni_id'     => Auth::id(),
                'title'         => $request->title == null || empty($request->title) ? '' : $request->title,
                'image'         => $path,
                'story'         => $request->story,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'User story successfully created'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'something went wrong!' ,
                'error' => $th
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $userstory = Userstory::where('id', $id)->first();

            if(!$userstory)
                return response()->json([
                    'message' => 'User story not found!'
                ], 404);

        return response()->json([
            'message' => $userstory
        ], 200);
    }

    public function showByToken(Request $request)
    {
        // Get the authenticated user's token
        $user = Auth::user();

        // Find the user story associated with the token
        $query = Userstory::with(['user.userProfiles'])->where('alumni_id', $user->id);

        // Apply filters
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Apply filters
        if ($request->has('story')) {
            $query->where('story', 'like', '%' . $request->story . '%');
        }

        // Apply sorting
        if ($request->has('sortBy')) {
            $sortDirection = $request->input('sortDir', 'asc');
            $sortBy = $request->input('sortBy');

            // Validate the sort direction to prevent SQL injection
            $validSortDirections = ['asc', 'desc'];

            if (in_array($sortDirection, $validSortDirections) && in_array($sortBy, ['title', 'story'])) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                // Handle invalid sort parameters here (e.g., return an error response)
                return response()->json([
                    'message' => 'Invalid sort parameters provided.'
                ], 400);
            }
        }

        $userstory = $query->get();

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
     * Update the specified resource in storage.
     */
    public function update(UserstoryRequest $request, string $id)
    {
        try {
            //Find user story by id
            $userstory = Userstory::find($id);

            if(!$userstory)
                return response()->json([
                    'message' => 'User story not found!'
                ], 404);
            
            if($userstory->alumni_id != Auth::id())
                return response()->json([
                    'message' => 'Unauthorized!'
                ], 401);

            

            $userstory->alumni_id   = Auth::id();
            $userstory->title       = $request->title == null || empty($request->title) ? '' : $request->title;
            $userstory->story       = $request->story;

            if($request->image){
                if($userstory->image)
                    Storage::disk('public')->delete('storyimage', 'public');

                $image = $request->image;
                $path = $image->store('storyimage', 'public');

                $userstory->image = $path;
            }

            $userstory->save();

            return response()->json([
                'message' => 'User story successfully updated'
            ], 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'something went wrong!',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $userstory = Userstory::find($id);

            if(!$userstory)
                return response()->json([
                    'message' => 'User story not found!'
                ], 404);

            // Public storage
            $storage = Storage::disk('public');

            // Old image delete
            if($storage->exists($userstory->image))
                $storage->delete($userstory->image);
            
            $userstory->delete();
            
            return response()->json([
                'message' => 'User story successfully deleted'
            ]);
        
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'something went wrong!',
            ], 500);
        }
    }
}
