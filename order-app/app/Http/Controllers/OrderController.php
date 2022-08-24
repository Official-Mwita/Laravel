<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //check how class local variables are dealed with
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     //Class constants
     //Standard prices
    const CWriting = 11.01;
    const CRwriting = 6.98;
    const CEditing = 5.2;

    const UWriting = 11.76;
    const URwriting = 7.4;
    const UEditing = 5.5;

    const PWriting = 12.5;
    const PRwriting = 8.0;
    const PEditing = 6.0;

    //Other constants
    const College = "College";
    const University = "University";
    const Phd = "PhD";
    const Writing = "Writing";
    const Editing = "Editing";
    const Re_Writing = "Re-Writing";

    //Spacing
    const Double = "Double";
    const Single = "Single";

    public function index()
    {

      
        $orders = auth()->user()->orders->sortByDesc('created_at');

        $modOrders= array();
        $modOrder = array();
        $description="";
        $price=0.0;
        $datedue = "";
        $academic_level = "";
        $subject_name = "";
        $service = "";
        $type_of_paper = "";
        $reference_style = "";
        $pages = 0;
        $hours = 0;
        $spacing = "";
        $trackId = 0;
        $cancelled = "";
        $progress_status = "";
        $progress_class = "";




        if($orders->count()){
            foreach($orders as $order){
              //Process time due
              $hours =$order->hours;
              //$time = $order->created_at->addHours($hours);
              $datedue = $order->created_at->addHours($hours)->toDayDateTimeString();
              $time_remaining =  $order->created_at->addHours($hours)->diffForHumans();

              //cancellation
              if($order->cancelled == 0) $cancelled = "Due";
              else $cancelled = "Cancelled";
              $description = $order->description;
              $price = $order->price;
              $academic_level = $order->academic_level;
              $subject_name = $order->subject_name;
              $service = $order->service;
              $type_of_paper = $order->type_of_paper;
              $reference_style = $order->reference_style;
              $pages = $order->pages;
              $spacing = $order->spacing;
              $trackId = $order->trackId;
              $progress_status = $order->progress_status;
              $progress_class = $order->progress_class;

              $modOrder = [
                "datedue" => $datedue,
                "remaining_time" => $time_remaining,
                "description" => $description,
                "subject" => $subject_name,
                "price" => $price,
                "academic_level" => $academic_level,
                "service" => $service,
                "type_of_paper" => $type_of_paper,
                "reference_style" => $reference_style,
                "pages" => $pages,
                "spacing" => $spacing,
                "trackId" => $trackId,
                "progress_status" => $progress_status,
                "progress_class" => $progress_class,
                "cancelled" => $cancelled
              ];

              array_push($modOrders, $modOrder);

            }
            return $modOrders;
        }
        
        return  $modOrders;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Make sure you validate user inputs
        $fields = $request->validate([

            //For those with selection
            //Make sure the user select from the list
            //Check also validation for every data type

            'cost' => 'required',
            'academicLevel' => 'required|string',
            'writingService' => 'required|string',
            'typeOfPaper' => 'required|string',
            'writingStyle' => 'required|string',
            'noOfPages' => 'required',
            'hours' => 'required',
            'subject_name' => '',
            'spacing' => 'required|string',
            'orderDescription' => 'required|string',
            'subject' => 'required|string'

        ]);

        $price = OrderController::calculatePrice($fields);
        $trackId = OrderController::generateId();

        $order = $request->user()->orders()->create(
                [
                    'price' => $price,
                    'trackId' => $trackId,
                    'cancelled' => false,
                    'academic_level' => $fields['academicLevel'],
                    'subject_name' => $fields['subject'],
                    'service' => $fields['writingService'],
                    'type_of_paper' => $fields['typeOfPaper'],
                    'description' => $fields['orderDescription'],
                    'spacing' => $fields['spacing'],
                    'hours' => $fields['hours'],
                    'pages' => $fields['noOfPages'],
                    'reference_style' => $fields['writingStyle'],
                    'progress_status' => 'Queued',
                    'progress_class' => 'bg-secondary',
                ]
            );

            //$remotehost = isset($_SERVER['REMOTE_HOST']) ? 'unkwown' : $_SERVER['REMOTE_HOST'];

          //create allow download files
          $allowdownload = $request->user()->allowDownload()->create(
            [
              'ip_address' => $_SERVER['REMOTE_ADDR'],
              'user_host' => "remotehost",
              'order_id' => $order->id,
              'user_agent' => $_SERVER['HTTP_USER_AGENT']
            ]
            );

        $sf = new SupportFileController();
        if($sf->storeFile($order)){
            return response([
                'message' => 'Order placed Successfully',
                'success' => true
            ], 201);
        }

        else  {
          $order->delete();
          $allowdownload->delete();
            return response([
                'message' => 'File upload error. Upload only supported files types and less than 5Mbs',
                'success' => false
            ], 501);
        }

       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

      /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $trackId)
    {
        //Validate the fields
        $fields = $request->validate(
            [
                'reason' => 'required|string',
                'message' => '',
            ]);

        //Get the order from the database
        $order = Order::where('trackId', $trackId)->first();

        //Order found
        if($order){
            
            //Check if order belongs to the user
            if($order->ownedBy(auth()->user()))
            {
                //Restrict order cancellation to 5
                if($order->cancellation->count() > 5)
                {
                    return response([
                        'message' => 'Operation failed'
                    ], 403);

                }
                 //create cancellation order
                 if(!($order->isCancelled()))
                 {
                     $order->cancellation()->create(
                         [
                             'reason' => $fields['reason'],
                             'message' => $fields['message'],
                         ]);

                $order->update(
                    [
                        'cancelled'=> 1,
                        'progress_status' => 'Cancelled',
                        'progress_class' => 'table-danger',
                    ]
                );

               
                }
                

                return response([
                    'message' => 'Success'
                ], 201);

            }

            //This user doesnt own the order
            else
            {
                return response([
                    'message' => 'Unauthorized'
                ], 409);

            }
        }

        //Order doesn't exist
        else {
            return response([
                'message' => 'Order doesn\'t exist'
            ], 409);
        }

    }

      /**
     * Update the order cancellation resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order->trackId  $trackId
     * @return \Illuminate\Http\Response
     */
    public function re_place(Request $request, $trackId)
    {
         //Get the order from the database
         $order = Order::where('trackId', $trackId)->first();

          //Order found
        if($order){
                        //Check if order belongs to the user
            if($order->ownedBy(auth()->user()))
            {
                 //create cancellation order
                 if(($order->isCancelled()))
                    {
                    
                        $order->update(
                        [
                            'cancelled'=> 0
                        ]
                    );
               
                }
                

                return response([
                    'message' => 'Success'
                ], 201);

            }

            //This user doesnt own the order
            else
            {
                return response([
                    'message' => 'Unauthorized'
                ], 409);

            }
        }

        //Order doesn't exist
        else {
            return response([
                'message' => 'Order doesn\'t exist'
            ], 409);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    private function calculatePrice($fields)
    {
        $cost = 0.00;

        $aLevel = $fields['academicLevel'];
        $service = $fields['writingService'];

        switch ($aLevel) {
            case self::College:
              switch ($service) {
                case self::Writing:
                  $cost = self::CWriting;
                  break;
                case self::Re_Writing:
                  $cost = self::CRwriting;
                  break;
                case self::Editing:
                  $cost = self::CEditing;
                  break;
                default:
                  break;
              }
              break;
      
            case self::University:
              switch ($service) {
                case self::Writing:
                  $cost = self::UWriting;
                  break;
                case self::Re_Writing:
                  $cost = self::URwriting;
                  break;
                case self::Editing:
                  $cost = self::UEditing;
                  break;
                default:
                  break;
              }
              break;
      
            case self::Phd:
              switch ($service) {
                case self::Writing:
                  $cost = self::PWriting;
                  break;
                case self::Re_Writing:
                  $cost = self::PRwriting;
                  break;
                case self::Editing:
                  $cost = self::PEditing;
                  break;
                default:
                  break;
              }
              break;
      
            default:
            $cost = 13.5;
              break;
          }

          //hours
          $hours = $service = $fields['hours'];
          switch ($hours) {
            case 6:
              $cost = $cost * 1.7;
              break;
            case 12:
              $cost = $cost * 1.6;
              break;
            case 24:
              $cost = $cost * 1.4;
              break;
            case 48:
              $cost = $cost * 1.15;
              break;
            case 72:
                $cost = $cost * 1.0;
                break;
            case 120:
              $cost = $cost * 0.9;
              break;
            case 168:
              $cost = $cost * 0.88;
              break;
            case 336:
              $cost = $cost * 0.8;
              break;
      
            default:
              if ($hours > 336) $cost = $cost * 0.75;
              else $cost = $cost * 1.7;
              break;
          }

        //update cost on spacing
        $spacing = $fields['spacing'];
        $cost = $spacing === self::Double ? $cost : $cost * 1.9;
        
        //Update cost by pages
        $pages = $fields['noOfPages'];
        $dis = 0.0;
        if ($pages > 80) {
            // use standard
            $cost = $cost * $pages;
            $dis = (80 / 300) * $cost;
            $cost = $cost - $dis;
        } else if ($pages < 80 && $pages > 0) {
            //go by pages
            $cost = $cost * $pages;
            $dis = ($pages / 300) * $cost;
            $cost = $cost - $dis;
        } else $cost = -($pages*$cost);


        $cost = round($cost, 2);

    
        return $cost;
    }

    private function generateId()
    {
        $trackId;

        do {
            
            $trackId = mt_rand(10000, 20000);           
           
            //Get the order from the data
            //Return false if an order with track id not found
            $order = Order::find($trackId, 'trackId');

        } while ($order);


        //Return the auto id because is unique
        return $trackId;
    }

}
