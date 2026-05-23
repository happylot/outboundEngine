<?php

namespace App\Http\Controllers;

use App\Models\TwitterPost;
use App\Http\Requests\StoreTwitterPostRequest;
use App\Http\Requests\UpdateTwitterPostRequest;

class TwitterPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreTwitterPostRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TwitterPost $TwitterPost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TwitterPost $TwitterPost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTwitterPostRequest $request, TwitterPost $TwitterPost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TwitterPost $TwitterPost)
    {
        //
    }
}