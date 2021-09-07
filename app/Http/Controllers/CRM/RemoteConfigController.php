<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Kreait\Firebase\Exception\RemoteConfigException;
use Validator;
use Kreait\Firebase\RemoteConfig;


class RemoteConfigController extends Controller{

    public function index(Request $request){
        $remoteConfig = app('firebase.remote_config');
//        $template = RemoteConfig\Template::new();
//        $welcomeMessageParameter = RemoteConfig\Parameter::named('laravel_test_parameter')
//            ->withDefaultValue('Welcome!'); // optional;
//        $template = $template
//            ->withParameter($welcomeMessageParameter);
//        try {
//            $remoteConfig->publish($template);
//        } catch (RemoteConfigException $e) {
//            echo $e->getMessage();
//        }


        $template = $remoteConfig->get();
//        foreach ($template->parameters() as $key){
//            dd($key->defaultValue()->value());
//        }
        return view('admin.remoteConfig.index',compact('template'));

    }

    public function edit(){

    }

}
