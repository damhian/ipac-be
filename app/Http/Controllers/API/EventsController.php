<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = new Events();
        $result = $events->getEvents();

        return response()->json([
            'events' => $result
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
            // Create Event
            Events::create([
                "title" => $request->title,
                "content" => $request->content,
                "short_description" => $request->short_description,
                "location_name" => $request->location_name,
                "location_lon" => $request->location_lon,
                "location_lat" => $request->location_lat,
                "start_at" => $request->start_at,
                "end_at" => $request->end_at,
                "created_by" => Auth::id()
            ]);

            return response()->json([
                'message' => 'Event succesfully created'
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
        // Event detail data
        $event = Events::find($id);

        if (!$event)
            return response()->json([
                'message' => 'Event not found!'
            ], 404);

        // Return response success
        return response()->json([
            'Event' => $event
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
    public function update(Request $request, string $id)
    {
        try {
            // Find event
            $event = Events::find($id);

            if(!$event)
                return response()->json([
                    'message' => 'Event not found!'
                ]);
            
            $event->title = $request->title;
            $event->content = $request->content;
            $event->short_description = $request->short_description;
            $event->location_name = $request->location_name;
            $event->location_lon = $request->location_lon;
            $event->location_lat = $request->location_lat;
            $event->start_at = $request->start_at;
            $event->end_at = $request->end_at;
            $event->status = $request->status;

            $event->save();

            return response()->json([
                'message' => 'Event successfully updated'
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
            $event = Events::find($id);

            if(!$event)
                return response()->json([
                    'message' => 'Event not found!'
                ]);
            
            $event->status = 'deleted';

            $event->save();
            
            return response()->json([
                'message' => 'Event successfully deleted'
            ]);
            
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
