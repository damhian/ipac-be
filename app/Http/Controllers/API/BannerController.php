<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('admin')->only('updateStatus');
    }

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
                "title" => $request->title,
                "content" => $request->content,
                "short_description" => $request->short_description,
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
