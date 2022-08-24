<?php

namespace App\Http\Controllers;

use App\Models\CompletionFiles;
use Illuminate\Http\Request;

class CompletionFilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompletionFiles  $completionFiles
     * @return \Illuminate\Http\Response
     */
    public function show(CompletionFiles $completionFiles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompletionFiles  $completionFiles
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompletionFiles $completionFiles)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompletionFiles  $completionFiles
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompletionFiles $completionFiles)
    {
        //
    }

    
    public function getSingleFiles(Request $request, $trackId)
    {
        $order = auth()->user()->orders->where('trackId', $trackId)->first();
        if(is_null($order)) 
        {
            
            return [
            'message' =>'Files not found',
            'success' => false
        ] ;

        }

        $files = $order->completion_files;
        return $files;

    }
}
