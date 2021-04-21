<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActorController extends Controller
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
        $actors = Actor::with('user')->get();
        return response()->json([
            'actors' => $actors
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
            'user_id' => 'required|int|unique:actors,user_id|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        Actor::create([
            'user_id' => $request->user_id
        ]);

        $actor = Actor::with('user')->find($request->user_id);

        return response()->json([
            'success' => true,
            'actor' => $actor
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
        $actor = Actor::findOrFail($id);

        return response()->json([
            'actor' => $actor->user,
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
        $actor = Actor::find($id);
        if($actor) {
            $actor->delete();
            return response()->json([
                'success' => 'true',
                'message' => 'Actor Deleted Successfully'
            ]);
        }
        return response()->json(['error'=>'id does not exist'], 404);
    }
}
