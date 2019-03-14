<?php

namespace App\Http\Controllers\Test;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp;

class IndexController extends Controller
{
    protected $hash_token = 'str:h:token:';
    public function login(Request $request){
      $uid=$request->input('uid');
        $tokena=md5(time()+$uid+rand(1000,9999));
        $token=substr($tokena,10,20);
       if(1){
           $key=$this->hash_token.$uid;
           Redis::hSet($key,'token',$token);
           Redis::expire($key,60*60*24*7);
           $response=[
               'errno'=>0,
               'token'=>$token
           ];
       }else{
           //TODO
       }
        return $response;
    }
    public function uCenter(Request $request){
        $uid=$request->input('uid');
        if(!empty($_SERVER['HTTP_TOKEN'])){
            $http_token=$_SERVER['HTTP_TOKEN'];
//            echo $http_token;die;
            $key=$this->hash_token.$uid;
            $token=Redis::hGet($key,'token');
            if($token==$http_token){
                $response=[
                    'errno'=>0,
                    'msg'=>'ok'
                ];
            }else{
                $response=[
                    'errno'=>50001,
                    'msg'=>'Invalid token'
                ];
            }
        }else{
            $response=[
                'errno'=>50000,
                'msg'=>'Not Found'
            ];
        }
        return $response;
    }
}
