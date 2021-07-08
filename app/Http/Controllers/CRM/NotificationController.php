<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;

class NotificationController extends Controller{

    public function index(Request $request){

        $notifications = Notification::all();
        return view('admin.notification.index',compact(['notifications']));

    }

    public function show(Request $request, $id){

        $notification = Notification::find($id);
        return view('admin.notification.show',compact(['notification']));

    }

    public function edit(Request $request, $id){
        $notification = Notification::find($id);
        return view('admin.notification.edit',compact(['notification']));
    }

    public function create(Request $request){
        return view('admin.notification.create');
    }

    public function store(Request $request){
        //dd($request->all());
        try {
            $request->validate([
                'user_type' => 'required|string',
                'heading'=>'required|string',
                'is_mobile'=>'nullable',
                'is_mail'=>'nullable',
                'is_sms'=>'nullable',   
                'mobile_body' => 'required_if:is_mobile,==,1|string', 
                'mail_body' => 'nullable|string',       
                'sms_body' => 'nullable|string',
                'mobile_image'=>'nullable|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'registered_from'=>'nullable|date',
                'registered_till'=>'nullable|date|after:registered_from'
            ]);

            $newNotification = new Notification;
            $newNotification->user_type=$request->user_type;
            $newNotification->heading=$request->heading;
            $newNotification->is_mobile=$request->has('is_mobile')?$request->is_mobile:null;
            $newNotification->mobile_body=$request->has('mobile_body')?$request->mobile_body:null;
            $newNotification->is_mail=$request->has('is_mail')?$request->is_mail:null;
            $newNotification->mail_body=$request->has('mail_body')?$request->mail_body:null;
            $newNotification->is_sms=$request->has('is_sms')?$request->is_sms:null;
            $newNotification->sms_body=$request->has('sms_body')?$request->sms_body:null;
            if($request->hasFile('mobile_image')){
                $newNotification->mobile_image =$this->saveImage($request->mobile_image);
            }
            if ($request->user_type == "Specific") {
                $newNotification->registered_from=Carbon::parse($request->registered_from)->format('Y-m-d H:i:s');
                $newNotification->registered_till=Carbon::parse($request->registered_till)->format('Y-m-d H:i:s');
            }
            if($newNotification->save()){
                return redirect()->route('notification.index')
                        ->with('success','Notification created successfully.');
            }else{        
                return $this->sendError('Notification Creation Failed',[], 422);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function saveImage($image){
        
        $image_name = 'category'.time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('images/notification/');
        $image->move($destinationPath, $image_name);
        $imageURL='/images/notification/'.$image_name;
        return $imageURL;
    }

    public function update(Request $request, $id){
        try {
            $request->validate([
                'user_type' => 'nullable|string',
                'heading'=>'nullable|string',
                'is_mobile'=>'nullable',
                'is_mail'=>'nullable',
                'is_sms'=>'nullable',   
                'mobile_body' => 'nullable|string', 
                'mail_body' => 'nullable|string',       
                'sms_body' => 'nullable|string',
                'mobile_image'=>'nullable|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'registered_from'=>'nullable|date',
                'registered_till'=>'nullable|date|after:registered_from',
            ]);
            $notification=Notification::find($id);
            if(!is_null($notification)){
                $notification->user_type=$request->has('user_type')?$request->user_type:$$notification->user_type;
                $notification->heading=$request->has('heading')?$request->heading:$$notification->heading;
                $notification->is_mobile=$request->has('is_mobile')?$request->is_mobile:$$notification->is_mobile;
                $notification->is_mail=$request->has('is_mail')?$request->is_mail:$$notification->is_mail;
                $notification->is_sms=$request->has('is_sms')?$request->is_sms:$$notification->is_sms;
                $notification->mobile_body=$request->has('mobile_body')?$request->mobile_body:$$notification->mobile_body;
                $notification->mail_body=$request->has('mail_body')?$request->mail_body:$$notification->mail_body;
                $notification->sms_body=$request->has('sms_body')?$request->sms_body:$$notification->sms_body;
                
                if($notification->save()){
                    return redirect()->route('notification.index')
                        ->with('success','Notification created successfully.');
                }else{
                    return $this->sendError('Notification Updation Failed',[], 422);
                }

            }else{
                return $this->sendError('Notification Does Not Exist.',[], 422);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function destroy(Request $request, $id)
    {
        Notification::find($id)->delete();
        return redirect()->route('notification.index')
                        ->with('success','Notification deleted successfully');
    }
}
