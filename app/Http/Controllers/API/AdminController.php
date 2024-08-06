<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Banner;
use App\Models\Events;
use App\Models\Jobfair;
use App\Models\Store;
use App\Notifications\UserApprovedNotification;
use App\Notifications\UserRejectedNotification;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\returnSelf;

class AdminController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {   
         // Check if the authenticated user is an administrator
        if (!Auth::user()->isAdmin()) {
            // abort(403, 'Only administrators can change the status.');
            return response()->json([
                'message' => 'Only administrators can change the status.'
            ], 403);
        }
        
         // Determine the table based on the URL segment or request data
         $table = $request->segment(3); // Assuming the table name is the second URL segment

         // Update the status based on the table name
         switch ($table) {
             case 'banner':
                $banner = Banner::find($id);
                $status = $request->input('status');

                // Check banner
                if(!$banner)
                    return response()->json([
                        'message' => 'Banner not found!'
                    ], 400);
                
                // Check the input
                if (!in_array($status, ['active', 'deleted'])) 
                        return response()->json([
                            'message' => 'Invalid status value.'
                        ], 400);

                Banner::where('id', $id)->update(['status' => $request->input('status')]);
                break;
             case 'events':
                $events = Events::find($id);
                $status = $request->input('status');

                if(!$events)
                    return response()->json([
                        'message' => 'Event not found!'
                    ], 400);

                if (!in_array($status, ['pending', 'approved', 'denied', 'deleted'])) 
                    return response()->json([
                        'message' => 'Invalid status value.'
                    ], 400);
                
                 
                Events::where('id', $id)->update(['status' => $status]);
                break;
             case 'jobfair':
                $jobfair = Jobfair::find($id);
                $status = $request->input('status');

                if(!$jobfair)
                    return response()->json([
                        'message' => 'Jobfair not found!'
                    ], 400);

                if (!in_array($status, ['pending', 'approved', 'denied', 'deleted'])) 
                    return response()->json([
                        'message' => 'Invalid status value.'
                    ], 400);

                Jobfair::where('id', $id)->update(['status' => $status]);
                break;
             case 'store':
                $store = Store::find($id);
                $status = $request->input('status');

                if(!$store)
                    return response()->json([
                        'message' => 'Store not found!'
                    ]);

                if (!in_array($status, ['pending', 'approved', 'denied', 'deleted'])) 
                    return response()->json([
                        'message' => 'Invalid status value.'
                    ], 400);
                
                Store::where('id', $id)->update(['status' => $status]);
                break;
             case 'user':
                $user = User::find($id);
                $status = $request->input('status');

                if(!$user)
                    return response()->json([
                        'message' => 'User not found!'
                    ], 400);

                    if (!in_array($status, ['pending', 'approved', 'rejected', 'deleted'])) {
                        return response()->json([
                            'message' => 'Invalid status value.'
                        ], 400);
                    }

                    User::where('id', $id)->update(['status' => $status]);
                    
                    // Send email notification
                    if ($status === 'approved') {
                        $user->notify(new UserApprovedNotification($user));
                    } elseif ($status === 'rejected') {
                        $customMessage = $request->input('message');
                        $user->notify(new UserRejectedNotification($user, $customMessage));
                    }
    
                break;
            default:
                abort(404);
         }
 
        // Return a response indicating the status change was successful
        return response()->json([
            'message' => 'Status updated successfully.'
        ]);
    }
}
