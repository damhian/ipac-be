<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\Undefined;

class BannerController extends Controller
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
        $query = Banner::where('status', '!=', 'deleted');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $banners = $query->get();

        return response()->json([
            'banners' => $banners
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BannerRequest $request)
    {
        try {
            
            // Check file and store image in storage folder under banner folder
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path  = $file->store('banner', 'public');
            }

            // check the api run on local or hosted on the web
            // $host = $request->getHost();

            // if ($host === 'localhost' || $host === '127.0.0.1') {
            //     // Running on localhost
            //     $fileUrl = asset('storage/' . $path);
            // } else {
            //     // Running on the web server
            //     $fileUrl = asset('public/storage/' . $path);
            // }
            
            
            // Create Banner
            Banner::create([
                "title" => $request->title == null || empty($request->title) ? '' : $request->title,
                "content" => $request->content == null || empty($request->content) ? '' : $request->content,
                "short_description" => $request->short_description == null || empty($request->short_description) ? '' : $request->short_description,
                "type" => $request->tipe,
                "file_url" => $path,
                "created_by" => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Banner Successfully created'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            // return json response
            return response()->json([
                'message' => 'Something went wrong!',
                'error' => $e
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Banner detail data
        $banner = Banner::find($id);

        if (!$banner) 
            return response()->json([
                'message' => 'Banner not found!'
            ], 404);
        

        // Return response success
        return response()->json([
            'Banner' => $banner
        ], 200);
    }

    public function showByToken(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $query = Banner::query();
        } else {
            $query = Banner::where('created_by', $user->id)->get();
        }

        // Apply filters
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        if ($request->has('content')) {
            $query->where('content', 'like', '%' . $request->input('content') . '%');
        }

        if ($request->has('shortDesc')) {
            $query->where('short_description', 'like', '%' . $request->input('short_description') . '%');
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
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

            if (in_array($sortDirection, $validSortDirections) && in_array($sortBy, ['title', 'content', 'short_description', 'type'])) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                // Handle invalid sort parameters here (e.g., return an error response)
                return response()->json([
                    'message' => 'Invalid sort parameters provided.'
                ], 400);
            }
        }

        $banner = $query->get();

        if($banner->isEmpty()){
            return response()->json([
                'message' => 'No banner found!'
            ]);
        }

        return response()->json([
            'banner' => $banner
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BannerRequest $request, string $id)
    {
        try {
            // Find banner
            $banner = Banner::find($id);

            if(!$banner)
                return response()->json([
                    'message' => 'Banner not found!'
                ], 404);
            
            $banner->title = $request->title == null || empty($request->title) ? '' : $request->title;
            $banner->content = $request->content == null || empty($request->content) ? '' : $request->content;
            $banner->short_description = $request->short_description == null || empty($request->short_description) ? '' : $request->short_description;
            $banner->type = $request->tipe;
            
            if($request->file) {
                // Delete old image if exist
                if($banner->file_url)
                    Storage::disk('public')->delete($banner->file_url);
                    
                
                $file               = $request->file;
                $path               = $file->store('banner', 'public');

                // // check the api run on local or hosted on the web
                // $host = $request->getHost();

                // if ($host === 'localhost' || $host === '127.0.0.1') {
                //     // Running on localhost
                //     $fileUrl = asset('storage/' . $path);
                // } else {
                //     // Running on the web server
                //     $fileUrl = asset('public/storage/' . $path);
                // }

                $banner->file_url   = $path;
            }

            $banner->save();

            DB::commit();

            return response()->json([
                'message' => 'Banner successfully updated'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Return Json response
            return response()->json([
                'message' => 'Something went wrong!'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        try {
            // Detail Info
            $banner = Banner::find($id);

            if(!$banner)
                return response()->json([
                    'message' => 'Banner not found!'
                ], 404);

            // Public storage
            $storage = Storage::disk('public');

            // Old image delete
            if($storage->exists($banner->file_url))
                $storage->delete($banner->file_url);
        
            $banner->status = 'deleted';

            $banner->save();

            DB::commit();

            return response()->json([
                'message' => 'Banner successfully deleted'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            // Return Json response
            return response()->json([
                'message' => 'Something went wrong!'
            ]);
        }
    }
}
