<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequest;
use App\Models\Store;
use App\Models\Storemedia;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StoreController extends Controller
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
        $stores = Store::with(['storeMedia', 'user.userProfiles'])
                    ->where('status', '=', 'approved')
                    ->where('status', '!=', 'deleted')
                    ->get();

        $superadminPhoneNumber = null;

        $superadmin = User::where('role', 'superadmin')
            ->with('userProfiles:alumni_id,phone_number_code,phone_number')
            ->first();

        if ($superadmin) {
            $superadminPhoneNumber = $superadmin->userProfiles->phone_number_code .''. $superadmin->userProfiles->phone_number;
        }
                    
        foreach ($stores as $store) {
            $store->superadminPhone = $superadminPhoneNumber;
        }
                
        return response()->json([
            'stores' => $stores
        ], 200);
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
                'link'              => $request->link,
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
        $store = Store::with(['storeMedia', 'user.userProfiles'])->find($id);

        if(!$store)
            return response()->json([
                'message' => 'store not found!'
            ], 404);

            // Retrieve superadminPhoneNumber
        $superadminPhoneNumber = null;

        $superadmin = User::where('role', 'superadmin')
            ->with('userProfiles:alumni_id,phone_number_code,phone_number')
            ->first();

        if ($superadmin) {
            $superadminPhoneNumber = $superadmin->userProfiles->phone_number_code .''. $superadmin->userProfiles->phone_number;
        }

        // Add superadminPhone to the store entry
        $store->superadminPhone = $superadminPhoneNumber;
        
        // Return response success
        return response()->json([
            'store' => $store
        ], 200);
    }

    public function showByToken(Request $request)
    {
        // Get the authenticated user's token
        $user = Auth::user();

        // Find the store associated with the token
        if ($user->isAdmin()) {
            $query = Store::with(['storeMedia', 'user.userProfiles']);
        } else {
            $query = Store::with(['storeMedia', 'user.userProfiles'])
            ->where('created_by', $user->id);
        }

        // Apply filters
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->has('content')) {
            $query->where('content', 'like', '%' . $request->content . '%');
        }

        if ($request->has('shortDescription')) {
            $query->where('short_description', 'like', '%' . $request->short_description . '%');
        }

        if ($request->has('price')) {
            $query->where('price', '=', $request->price);
        }

        if ($request->has('status')) {
            // Only add the "status" filter if the "status" input is provided
            $query->where('status', $request->input('status'));
        } else {
            // If "status" input is not provided, exclude banners with status "deleted"   
            $query->where('status', '!=', 'deleted');
        }

        $store = $query->get();

        if (!$store) {
            return response()->json([
                'message' => 'User profile not found!'
            ], 404);
        }

        // Retrieve superadminPhoneNumber
        $superadminPhoneNumber = null;
        $superadmin = User::where('role', 'superadmin')
            ->with('userProfiles:alumni_id,phone_number_code,phone_number')
            ->first();

        if ($superadmin) {
            $superadminPhoneNumber = $superadmin->userProfiles->phone_number_code .''. $superadmin->userProfiles->phone_number;
        }

        // Add superadminPhone to each store entry in the response
        foreach ($store as $storeEntry) {
            $storeEntry->superadminPhone = $superadminPhoneNumber;
        }

        // Return response success
        return response()->json([
            'stores' => $store
        ], 200);
    }

    public function showByUserId(string $id)
    {
        // Get the authenticated user's
        // $user = Auth::user();
        
        // Find the store associated with the user id from their login
        $store = Store::with(['storeMedia', 'user.userProfiles'])->where('created_by', $id)->get();

        if (!$store) {
            return response()->json([
                'message' => 'Store not found!'
            ], 404);
        }

        // Retrieve superadminPhoneNumber
        $superadminPhoneNumber = null;
        $superadmin = User::where('role', 'superadmin')
            ->with('userProfiles:alumni_id,phone_number_code,phone_number')
            ->first();

        if ($superadmin) {
            $superadminPhoneNumber = $superadmin->userProfiles->phone_number_code .''. $superadmin->userProfiles->phone_number;
        }

        // Add superadminPhone to each store entry in the response
        foreach ($store as $storeEntry) {
            $storeEntry->superadminPhone = $superadminPhoneNumber;
        }

        // Return response success
        return response()->json([
            'stores' => $store
        ], 200);
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
            $store->link = $request->link;

            // Delete images that are not present in the new request
            $newImageFileNames = [];
            if ($request->hasFile('images')) {
                $newImageFileNames = [];
                foreach ($request->file('images') as $image) {
                    $filePath = $image->store('store_media', 'public');
                    $newImageFileNames[] = $filePath;
                }
            }

            // Delete existing media that are not present in the new images
            $store->storeMedia()
                ->whereNotIn('filename', $newImageFileNames)
                ->delete();

            // Save new images
            if ($request->hasFile('images')) {
                $storeMedia = [];
                foreach ($newImageFileNames as $fileName) {
                    $storeMedia[] = new StoreMedia([
                        'filename' => $fileName,
                        'created_by' => Auth::id()
                    ]);
                }
                $store->storeMedia()->saveMany($storeMedia);
            }

            $store->save();

            DB::commit();

            return response()->json([
                'message' => 'Store successfully updated'
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
            $store = Store::find($id);

            if(!$store)
                return response()->json([
                    'message' => 'Store not found!'
                ], 404);

            // Delete Store Media
            $store->storeMedia()->delete();
            
            $store->status = 'deleted';
            $store->save();

            DB::commit();
            
            return response()->json([
                'message' => 'Store successfully deleted'
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
}
