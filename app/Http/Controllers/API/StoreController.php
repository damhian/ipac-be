<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequest;
use App\Models\Store;
use App\Models\Storemedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = Store::with('storeMedia')
                        ->where('status', '!=', 'deleted')
                        ->get();

        return response()->json([
            'store' => $stores
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
    public function store(StoreRequest $request)
    {
        try {
            // Create Store
            $store = Store::create([
                'title'             => $request->title,
                'content'           => $request->content,
                'short_description' => $request->short_description,
                'price'             => $request->price,
                'created_by'        => Auth::id()
            ]);

            // Store Media
            if ($request->hasFile('images')) {
                $images = $request->file('images');
    
                $storeMedia = [];
    
                foreach ($images as $image) {
                    $filePath = $image->store('store_media', 'public');
    
                    $storeMedia[] = new Storemedia([
                        'filename' => $filePath,
                        'created_by' => Auth::id()
                    ]);
                }
    
                $store->storeMedia()->saveMany($storeMedia);
            }

            DB::commit();

            return response()->json([
                'message' => 'Store content successfully created'
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
    public function show(string $id)
    {
        // Get Store data by id
        $store = Store::with('storeMedia')->find($id);

        if(!$store)
            return response()->json([
                'message' => 'store not found!'
            ], 404);
        
        // Return response success
        return response()->json([
            'Store' => $store
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
    public function update(StoreRequest $request, string $id)
    {
        try {
            // Find store
            $store = Store::find($id);

            if(!$store)
                return response()->json([
                    'message' => 'Store not found!'
                ]);
            
            $store->title = $request->title;
            $store->content = $request->content;
            $store->short_description = $request->short_description;
            $store->price = $request->price;
            $store->status = $request->status;
            
            // Update Media
            if ($request->hasFile('images')) {
                $storeMedia = [];

                foreach ($request->file('images') as $image) {
                    $filePath = $image->store('store_media', 'public');

                    $storeMedia[] = new StoreMedia([
                        'filename' => $filePath,
                        'created_by' => Auth::id()
                    ]);
                }

                $store->storeMedia()->delete(); // Delete existing media
                $store->storeMedia()->saveMany($storeMedia); // Save new media
            }

            $store->save();

            return response()->json([
                'message' => 'Store successfully updated'
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
            $store = Store::find($id);

            if(!$store)
                return response()->json([
                    'message' => 'Store not found!'
                ]);

            // Delete Store Media
            $store->storeMedia()->delete();
            
            $store->status = 'deleted';
            $store->save();
            
            return response()->json([
                'message' => 'Store successfully deleted'
            ]);
            
        } catch (\Throwable $th) {
             // return json response
             return response()->json([
                'message' => 'something went wrong!',
                'error message' => $th
            ], 500);
        }
    }
}
