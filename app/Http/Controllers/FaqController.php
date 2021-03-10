<?php

namespace App\Http\Controllers;

use App\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $faq = Faq::all();
        return response()->json(array('data' => $faq), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $faq = Faq::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json(array('data' => $faq), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Faq $faq
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Faq $faq)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required'
        ]);
        $faq->update($request->only(['title', 'description']));
        return response()->json(array('ok' => 'ok'), 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Faq $faq
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faq $faq)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->role === "1") {
            $faq->delete();
            return response()->json(null, 204);
        } else {
            return response()->json(null, 404);
        }
    }
}
