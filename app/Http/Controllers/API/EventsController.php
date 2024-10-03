<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Events;
use Carbon\Carbon;
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
        ->where('status', '=', 'approved')
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

            // Format the event_time if needed
            // $eventTime = $this->formatEventTime($request->event_time);

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
            $query = Events::with(['user', 'user.userProfiles']);
        } else {
            // If the user is not an admin, fetch events based on their user ID
            $query->where('created_by', $user->id);
        }

        // Apply filters
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->has('content')) {
            $query->where('content', 'like', '%' . $request->content . '%');
        }

        if ($request->has('shortDesc')) {
            $query->where('short_description', 'like', '%' . $request->short_description . '%');
        }

        if ($request->has('locationName')) {
            $query->where('location_name', 'like', '%' . $request->location_name . '%');
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

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            // Only add the "status" filter if the "status" input is provided
            $query->where('status', $request->input('status'));
        } else {
            // If "status" input is not provided, exclude banners with status "deleted"   
            $query->where('status', '!=', 'deleted');
        }

        // Apply sorting
        if ($request->has('sortBy')) {
            $sortDirection = $request->input('sortDir', 'asc');
            $sortBy = $request->input('sortBy');

            // Validate the sort direction to prevent SQL injection
            $validSortDirections = ['asc', 'desc'];

            if (in_array($sortDirection, $validSortDirections) && in_array($sortBy, ['title', 'content', 'short_description', 'location_name', 'start_at', 'end_at', 'type'])) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                // Handle invalid sort parameters here (e.g., return an error response)
                return response()->json([
                    'message' => 'Invalid sort parameters provided.'
                ], 400);
            }
        }

        $events = $query->get();

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

            // If start_at or end_at is undefined, set them to null
            $startAt = $request->start_at === 'undefined' ? null : $request->start_at;
            $endAt = $request->end_at === 'undefined' ? null : $request->end_at;

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
            $event->start_at = $startAt;
            $event->end_at = $endAt;
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

    private function formatEventTime($eventTime)
    {
       // Split the input by colon
        $parts = explode(':', $eventTime);

        if (count($parts) === 2) {
            $hours = intval($parts[0]);
            $minutes = intval($parts[1]);

            // Check if hours are greater than or equal to 24
            if ($hours >= 24) {
                // Reset hours to 0 and adjust minutes
                $hours = 0;
            }

            // Ensure hours and minutes are formatted with leading zeros
            $formattedHours = str_pad($hours, 2, '0', STR_PAD_LEFT);
            $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

            // Reconstruct the time in HH:MM format
            $formattedTime = $formattedHours . ':' . $formattedMinutes;

            return $formattedTime;
        }

        // If the input doesn't match the expected format, return it as is
        return $eventTime;
    }

}
