<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = new Banner();
        $result = $banners->getBanner();

        return response()->json([
            'banners' => $result
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
    public function store(BannerRequest $request)
    {
        try {
            $imagename = Str::random(32).".".$request->image->getClientOriginalExtension();
            
            // Create Banner
            Banner::create([
                "title" => $request->title,
                "content" => $request->content,
                "short_description" => $request->short_description,
                "image" => $imagename,
                "created_by" => Auth::id(),
            ]);

            // Save Image in Storage folder
            Storage::disk('public')->put($imagename, file_get_contents($request->image));

            return response()->json([
                'message' => 'Company Successfully created'
            ], 200);

        } catch (\Exception $e) {
            // return json response
            return response()->json([
                'message' => 'Something went wrong!'
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
    public function update(BannerRequest $request, string $id)
    {
        try {
            // Find banner
            $banner = Banner::find($id);

            if(!$banner)
                return response()->json([
                    'message' => 'Banner not found!'
                ], 404);
            
            $banner->title = $request->title;
            $banner->content = $request->content;
            $banner->short_description = $request->short_description;
            $banner->status = $request->status;

            if($request->image) {
                // Public storage
                $storage = Storage::disk('public');

                // Old image delete
                if($storage->exists($banner->image))
                    $storage->delete($banner->image);
                
                // Image name
                $imagename = Str::random(32).".".$request->image->getClientOriginalExtension();
                $banner->image = $imagename;

                // Save image in public folder
                $storage->put($imagename, file_get_contents($request->image));
            }

            $banner->save();

            return response()->json([
                'message' => 'Banner successfully updated'
            ]);

        } catch (\Exception $e) {
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
            if($storage->exists($banner->image))
                $storage->delete($banner->image);
        
            $banner->status = 'deleted';

            $banner->save();

            return response()->json([
                'message' => 'Banner successfully deleted'
            ]);

        } catch (\Throwable $th) {
            // Return Json response
            return response()->json([
                'message' => 'Something went wrong!'
            ]);
        }
    }
}
