<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
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
        $ratings = Rating::with('user')->with('movie')->get();
        return response()->json([
            'ratings' => $ratings
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
            'movie_id' => 'required|int|exists:movies,id',
            'rating' => 'required|int|max:10|min:0',
            'comment' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        try {
            $rating = Rating::create([
                'movie_id' => $request->movie_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'user_id' => Auth::user()->id
            ]);
        }catch (\Exception $e){
            return response()->json(['errors'=>'Rating Already Exists'], 422);
        }

        return response()->json([
            'success' => true,
            'rating' => $rating
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
        $rating = Rating::
            with('user')->
            with('movie')->
            where('movie_id', $id)->
            where('user_id', Auth::user()->id)
            ->get();
        return response()->json([
            'rating' => $rating
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $rating = Rating::
        where('movie_id', $id)->
        where('user_id', Auth::user()->id)
            ->delete();
        if($rating)
            return response()->json([
                'success' => 'true',
                'message' => 'Rating Deleted Successfully'
            ]);
        else
            return response()->json(['error'=>'Rating does not exist'], 404);
    }
}
