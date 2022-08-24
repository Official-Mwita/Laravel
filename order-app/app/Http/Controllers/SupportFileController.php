<?php

namespace App\Http\Controllers;

use App\Models\SupportFile;
use App\Models\Order;
use Illuminate\Http\Request;

class SupportFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $orders = auth()->user()->orders;

        $files = array();

        foreach($orders as $order)
        {
            array_push($files, $order->support_files);
            //$files = ($order->support_files);
        }

        return $files;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Order  $order
     * @return true/false
     */
    
    public function storeFile(Order $order)
    {
        $allowedMimes = array(
            'jpg', 'jpeg', 'jpe', 'zip',
            'gif',         
            'png' ,       
            'pdf',
			'ppt',
			'zip',
			'xls',
            'docx',
            'doc',
            'rtf',
            'ods' ,
            'odt', 
            'odp', 
            'txt',

        );
        
        $fileId = 1;
        $fileName = "";
        $filePath = "";

        //check if user supplied files
        if(!isset($_FILES['file'])) return true;

        $countfiles = count($_FILES['file']['name']);
        
        for($i=0;$i<$countfiles;$i++){
            
            $fileName = $_FILES['file']['name'][$i];
            $filePath =  '/home/dh_5g5qea/laravel/storage/supportfiles/'.$order->trackId.'_'.$fileId.'_'.$fileName;
            //$filePath =  '../storage/supportfiles/'.$order->trackId.'_'.$fileId.'_'.$fileName;

            $file_ext = pathinfo($filePath, PATHINFO_EXTENSION);
	        //$file_mime = mime_content_type($_FILES['file']['tmp_name'][$i]);

            //don't save not allowed files
            if(!in_array($file_ext, $allowedMimes)) return;
            //if(!key_exists($file_ext, $allowedMimes)) return;

            //Make sure file is less than 10Mb
            if($_FILES['file']['size'][$i]>5000000) return;

            //Everything okay write to the database
            $order->support_files()->create(
                [
                    'id' => $order->trackId.'00'.$fileId,
                    'FileName' => $fileName,
                    'StoragePath' => $filePath,
                ]
                );
               
            move_uploaded_file( $_FILES['file']['tmp_name'][$i], $filePath);
            $fileId++;
        }

        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupportFile  $supportFile
     * @return \Illuminate\Http\Response
     */
    public function show(SupportFile $supportFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SupportFile  $supportFile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupportFile $supportFile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupportFile  $supportFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupportFile $supportFile)
    {
        //
    }

}
