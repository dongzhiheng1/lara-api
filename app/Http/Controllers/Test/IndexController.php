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
    public function order(){
//      var_dump($_SERVER);
        $uri=$_SERVER['REQUEST_URI'];
        $url=substr(md5($uri),0,10);
        $ip=$_SERVER['SERVER_ADDR'];
        $redis_key='str:'.$url.":".$ip;
        $num=Redis::incr($redis_key);
        Redis::expire($redis_key,5);
//        echo 'count:'.$num;echo '</br>';die;
        if($num>5){
            $response=[
                'errno'=>50002,
                'msg'=>'Invaild token'
            ];
            Redis::sAdd('ip',$ip);
            Redis::expire($redis_key,20);
        }else{
            $response=[
                'errno'=>0,
                'msg'=>'ok',
                'data'=>[
                    'aaa'=>'bbb'
                ]
            ];
        }
        return $response;

    }
}
