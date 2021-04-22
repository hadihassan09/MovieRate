<?php

namespace App\Http\Controllers;

use App\Models\Director;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DirectorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $directors = Director::with('user')->with('movies')->get();
        return response()->json([
            'directors' => $directors
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|int|unique:directors,user_id|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        Director::create([
            'user_id' => $request->user_id
        ]);

        $director = Director::with('user')->with('movies')->find($request->user_id);

        return response()->json([
            'success' => true,
            'director' => $director
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $director = Director::findOrFail($id);
        }catch (\Exception $e){
            return response()->json(['error'=>'Director Not Found'], 404);
        }

        $director['user'] = $director->user;
        $director['movies'] = $director->movies;
        return response()->json([
            'director' => $director->user,
            'success' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $director = Director::find($id);
        if($director) {
            $director->delete();
            return response()->json([
                'success' => 'true',
                'message' => 'Director Deleted Successfully'
            ]);
        }
        return response()->json(['error'=>'id does not exist'], 404);
    }
}
