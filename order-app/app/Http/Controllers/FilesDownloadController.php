<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportFile;
use App\Models\CompletionFiles;

class FilesDownloadController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function adminSupportFileDownload($fileId=0)
    {
        if(auth()->user()->isadmin === 1){
            //Only processed by admin
            $file = SupportFile::where('id', $fileId)->first();

            if($file !== null){
                //Download files
                if(!FilesDownloadController::downloadFile($file->StoragePath, $file->FileName)) return 'file download error';

            }

            else return 'file does not exist';
        } 
        else {

            return redirect()->route('home'); 
        }

    }

    public function userDownload($fileId=0)
    {
        //get file
        $file = CompletionFiles::find($fileId);

        //return error if null
        if(is_null($file)){
            return response([
                'message' => 'Internal server error',
                'success' => false
            ], 501);
        }

        //Get order
        $order = $file->order;

        //Make sure user posseses this file
        if($order->user->id === auth()->user()->id){
            //Make sure is allowed to download
            if($file->allow_download !== 1) return $file->reason_denied;
            //Download file
            if(!FilesDownloadController::downloadFile($file->StoragePath, $file->FileName)) return 'file download error';
        }

        else return "unauthorized";
        //Make sure this file order belongs to this user

        //$order = 

    }

    /**
    * used to handle file downloads provided a file path
    */
    private function downloadFile(string $filepath, $file_name)
    {
        $range = isset($_SERVER['HTTP_RANGE']);
        // to avoid compression turn off.
        //apache_setenv('no-gzip', 1);
        ini_set('zlib.output_compression', 'Off');

        //Exit on null file
        if(!file_exists($filepath)) return false;

        $pathinfo = pathinfo($filepath);
        $file_ext = $pathinfo['extension'];
        //$file_name = $pathinfo['basename']; 
        $file_size = filesize($filepath);
        $file_start; $file_end;
        $file = @fopen($filepath, 'r');
        
        if($range)
        {
            // used to set the seek start and seek end of a file if a download is resumed.
            list($si_unit, $range_starts) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if($si_unit == 'bytes'){
                list($range1, $range2) = explode(',', $range_starts, 2);

            }

            else
            {
                $range1 = '';
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                exit;
            }
        
            list($file_start,$file_end) = explode('-', $range1, 2);

            $file_end   = (empty($file_end)) ? ($file_size - 1) : min(abs(intval($file_end)),($file_size - 1));
            $file_start = (empty($file_start) || $file_end < abs(intval($file_start))) ? 0 : max(abs(intval($file_start)),0);


            if ($file_start > 0 || $file_end < ($file_size - 1))
            {
                header('HTTP/1.1 206 Partial Content');
                header('Content-Range: bytes '.$file_start.'-'.$file_end.'/'.$file_size);
                $file_size = $file_end - $file_start + 1;
                fseek($file, $file_start);
                header("Content-Length: $file_size");
            }

        }

        else 
        {
            //For content type
            $content_default = "text/rtf";
            $cont_type = array (
			    "pdf"=>"application/pdf",
			    "ppt"=>"application/vnd.ms-powerpoint",
			    "zip"=>"application/zip",
			    "xls"=>"application/vnd.ms-excel",
                // "mp3" => "audio/mpeg",  
                // "mp4" => "video/mpeg",
                // "mpg" => "video/mpeg",
                // "avi" => "video/x-msvideo",
			);
		
		//content type sent to user
		$to_use = isset($cont_type["$file_ext"]) ? ($cont_type["$file_ext"]) : $content_default;
		
        // set the headers, prevent caching
		header("Pragma: public");
		header("Expires: -1");
		header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
		header("Content-Disposition: attachment; filename=\"$file_name\"");
		header("Content-Type: $to_use");
		header("Content-Length: $file_size");
		
    }
		//ob_flush();
		flush();
		
		readfile($filepath);
    
		if(!connection_status()){
            fclose($file);
		    exit;
		}

        fclose($file);
	
        return true;
        
    }

}
