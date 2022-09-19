<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\SupportFile;
use App\Models\CompletionFiles;
use App\Models\AllowDownload;

class AdminHomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(){
        if(auth()->user()->isAdmin === 1){
            $orders = Order::paginate(30);//with(['id', 'subject_name', ''])->paginate(20);
            return view('admin', [
                'orders' => $orders
            ]);
        } 
        else {

            return redirect()->route('home'); 
        }
    }

    public function getOrder(Request $request, $id = 0){
        if(auth()->user()->isadmin === 1){
            //Only processed by admin
            $order = Order::where('trackId', $id)->first();

            //get order files
            $files = SupportFile::where('order_id', $order->id)->get();
            $allowd = AllowDownload::where('order_id', $order->id)->first();

            return view('singleorder', [
                'order' => $order,
                'files' => $files,
                'allowdownload' => $allowd
            ]);
        } 
        else {

            return redirect()->route('home'); 
        }
    }

    public function uploadCompletedFiles(Request $request)
    {
        if(auth()->user()->isadmin === 1){
            //Only processed by admin
            $order = Order::where('trackId', $request['orderid'])->first();

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
    
            $fileName = $_FILES['orderfile']['name'];

            //Make sure file is less than 10Mb
            if($_FILES['orderfile']['size']>5000000) return "File to large";
    
            //get order and use it to create completion files
            $fileId = count($order->completion_files);

            //$fileId =is_null($fileId) ? 0 : $fileId;
            $fileId += 1;

            //$storagePath = '/home/dh_5g5qea/laravel/storage/completefiles/'.$order->trackId.'_'.$fileId.'_'.$fileName;
            $storagePath = '../storage/completefiles/'.$order->trackId.'_'.$fileId.'_'.$fileName;

            $file_ext = pathinfo($storagePath, PATHINFO_EXTENSION);
    
            //don't save not allowed files
            if(!in_array($file_ext, $allowedMimes)) return "file upload not supported";

            move_uploaded_file( $_FILES['orderfile']['tmp_name'], $storagePath);

            //Default reason denied
            $reason_denied = is_null($request['reason']) ? 'Allowed' : $request['reason'];
            $allow_download = is_null($request['allowDownload']) ? 0 : $request['allowDownload'];

            $order->completion_files()->create(
                [
                    'id' => $order->trackId.'00'.$fileId,
                    'FileName' => $fileName,
                    'StoragePath' => $storagePath,
                    'information' => $request['message'],
                    'allow_download' => $allow_download,
                    'reason_denied' => $reason_denied
                ]
                );

                return 'success';
        } 
        else {

            return redirect()->route('home'); 
        }
        
    }

    public function changeOrderStatus(Request $request)
    {
        if(auth()->user()->isadmin === 1){
            //Only for admins
            $status = $request['changestatus'];
            $progress_status = '';
            $progress_class = '';
            $cancelled = 0;

            switch ($status) {
                case 'queued':
                    $progress_status = 'Queued';
                    $progress_class = 'bg-secondary';
                    break;

                case 'in_progress':
                    $progress_status = 'In Progress';
                    $progress_class = 'bg-warning';
                    break;
                
                case 'completed':
                    $progress_status = 'Completed';
                    $progress_class = 'bg-success';
                    break;
                
                case 'cancelled':
                    $progress_status = 'Cancelled';
                    $progress_class = 'bg-danger';
                    break;
                
                default:
                return back();
                    break;
            }

            Order::where('trackId', $request['orderid'])->update(
                [
                    'cancelled'=> $cancelled,
                    'progress_status' => $progress_status,
                    'progress_class' => $progress_class,
                ]
                );

                return back();

        } 
        else {

            return redirect()->route('home'); 
        }

    }
}
