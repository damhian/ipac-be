<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('admin')->only('updateStatus');
    }

    public function index(Request $request)
    {
        // Get all events and sort them by start_at date
        $query = Events::with(['user', 'user.userProfiles'])
        ->where('status', '!=', 'deleted')
        ->orderBy('start_at', $request->sort_by ? $request->sort_by : 'asc');

        // Check if type filter is provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $events = $query->get();

        if(!$events)
            return response()->json([
                'message' => 'event not found!'
            ]);

        return response()->json([
            'events' => $events
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
    public function store(EventRequest $request)
    {
        try {

            $path = null;

             // Check file and store image in storage folder under banner folder
             if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path  = $image->store('event_images', 'public');
            }

            // Create Event
            Events::create([
                "title"             => $request->title,
                "content"           => $request->content,
                "image"             => $path,
                "short_description" => $request->short_description,
                "location_name"     => $request->location_name,
                "location_lon"      => $request->location_lon,
                "location_lat"      => $request->location_lat,
                "start_at"          => $request->start_at,
                "end_at"            => $request->end_at,
                "type"              => $request->type,
                "created_by"        => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Event succesfully created'
            ], 200);
            
        } catch (\Throwable $th) {
            DB::rollBack();
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
        // Get Event data by id
        $event = Events::with('user', 'user.userProfiles')->find($id);

        if (!$event)
            return response()->json([
                'message' => 'Event not found!'
            ], 404);

        // Return response success
        return response()->json([
            'Event' => $event
        ], 200);
    }

    public function showByToken(Request $request)
    {   
        // Get the currently authenticated user
        $user = Auth::user();
        
        // Define the base query
        $query = Events::with(['user', 'user.userProfiles'])
            ->where('status', '!=', 'deleted')
            ->orderBy('start_at', $request->sort_by ? $request->sort_by : 'asc');
        
        // If the user is an admin, fetch all events
        if ($user->isAdmin()) {
            $events = $query->get();
        } else {
            // If the user is not an admin, fetch events based on their user ID
            $events = $query->where('created_by', $user->id)->get();
        }

        if ($events->isEmpty()) {
            return response()->json([
                'message' => 'No events found!'
            ]);
        }

        return response()->json([
            'events' => $events
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
    public function update(EventRequest $request, string $id)
    {
        try {
            // Find event
            $event = Events::find($id);

            if(!$event)
                return response()->json([
                    'message' => 'Event not found!'
                ]);
            
            if($request->hasFile('image')){
                if($event->image)
                    Storage::disk('public')->delete($event->image);

                $image        = $request->image;
                $path         = $image->store('event_images', 'public');
                $event->image = $path;
            }
            
            $event->title = $request->title;
            $event->content = $request->content;
            $event->short_description = $request->short_description;
            $event->location_name = $request->location_name;
            $event->location_lon = $request->location_lon;
            $event->location_lat = $request->location_lat;
            $event->start_at = $request->start_at;
            $event->end_at = $request->end_at;
            $event->type = $request->type;

            $event->save();

            DB::commit();

            return response()->json([
                'message' => 'Event successfully updated'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
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
            $event = Events::find($id);

            if(!$event)
                return response()->json([
                    'message' => 'Event not found!'
                ]);

            // Check and delete image if exists
            if($event->image)
                Storage::disk('public')->delete($event->image);
            
            $event->status = 'deleted';

            $event->save();

            DB::commit();
            
            return response()->json([
                'message' => 'Event successfully deleted'
            ]);
            
        } catch (\Throwable $th) {
            DB::rollBack();
             // return json response
             return response()->json([
                'message' => 'something went wrong!',
                'error message' => $th
            ], 500);
        }
    }

}
