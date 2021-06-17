<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\Category;
use App\Models\DeliveryPincode;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Products;
use App\Models\ProductVariables;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use Validator;
use Auth;

class TicketController extends BaseController{
    public function addTicket(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'subject' => 'required|string',
                'category'=>'required|string',
                'message'=>'required|string',
                'order_id'=>'numeric',

            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());

            }
            else {
                $ticket=new Ticket;
                if($request->has('order_id')){
                    $ticket->order_id=$request->order_id;
                }
                $ticket->customer_id=Auth::user()->id;
                $ticket->subject=$request->subject;
                $ticket->category=$request->category;
                $ticket->ticket_status_id=1;
                $ticketSaveStatus=$ticket->save();
                if($ticketSaveStatus){
                    $ticketMessage = new TicketMessage();
                    $ticketMessage->ticket_id=$ticket->id;
                    $ticketMessage->message=$request->message;
                    $ticketMessage->message_by='customer';
                    $ticketMessageSaveStatus=$ticketMessage->save();
                    if($ticketMessageSaveStatus){
                        return $this->sendResponse($ticket,'Ticket Stored Successfully', true);

                    }else{
                        return $this->sendResponse([],'Error in saving ticket message', false);

                    }
                }else{
                    return $this->sendResponse([],'Error in saving ticket', false);
                }

            }
//
        }catch (\Exception $e) {
            return response()->json(['success' => false,
                'data'=>$e->getMessage()], 413);
        }
    }

    public function getAllTickets(Request $request){
        try {
            $validator = Validator::make($request->all(), [

            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            else {
                $customer=Auth::user();

                if ($customer==null) {
                    return response()->json(['success' => false,
                        'msg'=>'No User Found'], 200);
                } else {
                    $tickets = Ticket::with(['customer','admin','ticketStatus','message.supportAgent'])->where('customer_id',$customer->id)->orderBy('id','DESC')->get();
                    if($tickets->isEmpty()){
                        return $this->sendResponse([],'No Tickets Found', false);

                    }
                    return $this->sendResponse($tickets,'Tickets Fetched Successfully', true);
                }
            }
//
        }catch (\Exception $e) {
            return response()->json(['success' => false,
                'data'=>$e->getMessage()], 413);
        }
    }

    public function addMessage(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'ticket_id' => 'required',
                'message' => 'nullable|string',
                'image'=>'nullable|mimes:jpeg,bmp,png,jpg|file|max:3072'
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            else {
                $ticketMessage = new TicketMessage;
                if($request->has('message')){
                    $ticketMessage->message = $request->message;
                }
                if($request->hasFile('image')){
//                    return "shubham";
                    $ticketMessage->photo_url=$this->saveImage($request->file('image'));
                }

                $ticketMessage->ticket_id=$request->ticket_id;
                $ticketMessage->message_by = 'customer';
                $ticketMessageSaveStatus = $ticketMessage->save();
                $ticket = Ticket::with(['customer', 'admin', 'ticketStatus','message','message.supportAgent'])->where('id', $request->ticket_id)->first();

                if($ticketMessageSaveStatus){
                    return $this->sendResponse($ticket,'Message Sent Successfully', true);

                }else{
                    return $this->sendResponse([],'Error in sending message', false);

                }



            }
//
        }catch (\Exception $e) {
            return response()->json(['success' => false,
                'data'=>$e->getMessage()], 413);
        }
    }

    function saveImage($image){
        $image_name = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('images/tickets');
        $image->move($destinationPath, $image_name);
        $imageURL=env('APP_URL').'/images/tickets/'.$image_name;
        return $imageURL;
    }

    function getAllTicketMessage(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'ticket_id' => 'required',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            else {

                $ticket = Ticket::with(['customer', 'admin', 'ticketStatus','message','message.supportAgent'])->where('id', $request->ticket_id)->first();

                if($ticket->exists){
                    return $this->sendResponse($ticket,'Message Fetched Successfully', true);
                }else{
                    return $this->sendResponse([],'No data Found', false);
                }
            }
//
        }catch (\Exception $e) {
            return response()->json(['success' => false,
                'data'=>$e->getMessage()], 413);
        }
    }

    function createCustomTicket(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'subject' => 'required|string',
                'category'=>'required|string',
                'message'=>'required|string',
                'order_id'=>'numeric',

            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            else {
                $ticket=new Ticket;
                if($request->has('order_id')){
                    $ticket->order_id=$request->order_id;
                }
                $ticket->customer_id=Auth::user()->id;
                $ticket->subject=$request->subject;
                $ticket->category=$request->category;
                $ticket->ticket_status_id=1;
                $ticketSaveStatus=$ticket->save();
                if($ticketSaveStatus){
                    $ticketMessage = new TicketMessage();
                    $ticketMessage->ticket_id=$ticket->id;
                    $ticketMessage->message=$request->message;
                    $ticketMessage->message_by='customer';
                    $ticketMessageSaveStatus=$ticketMessage->save();
                    if($ticketMessageSaveStatus){
                        $customer=User::find($request->customer_id);

                        if ($customer==null) {
                            return $this->sendResponse([],'No User Found', false);

                        } else {
                            $tickets = Ticket::with(['customer','admin','ticketStatus','message','message.supportAgent'])->where('customer_id',$customer->id)->orderBy('id','DESC')->get();
                            if($tickets->isEmpty()){
                                return $this->sendResponse([],'No Tickets Found', false);

                            }
                            return $this->sendResponse($tickets,'Message Added Successfully', true);

                        }

                    }else{
                        return $this->sendResponse([],'Error in saving ticket Message', false);
                    }
                }else{
                    return $this->sendResponse([],'Error in saving ticket', false);

                }

            }
//
        }catch (\Exception $e) {
            return response()->json(['success' => false,
                'data'=>$e->getMessage()], 413);
        }
    }
}
