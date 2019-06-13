<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Score;
use GuzzleHttp;
use \GuzzleHttp\Exception\GuzzleException;
use App\Helpers\WeChat\MiniProgramHelper;

class ScoreController extends Controller
{
    const APP_ID =  'wx28e71979e302610d';
    const APP_SECRET= '22c79583f5e54dccee4ea3d0dd7146b7';

    function login($jsCode){

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".self::APP_ID."&secret=".self::APP_SECRET."&js_code=".$jsCode."&grant_type=authorization_code";
        $client = new GuzzleHttp\Client();
        try{
            $res = $client->request('GET', $url);
            $response = $res->getBody();
            $responseJson = GuzzleHttp\json_decode($response);

            if(isset($responseJson->openid)){
                $open_id = $responseJson->openid;
                $session_key = $responseJson->session_key;
                $score = Score::firstOrNew(['open_id'=>$open_id],['session_key'=>$session_key]);
                $score->session_key = $session_key;
                $api_token = sha1($session_key.'sudoku');
                $score->api_token = $api_token;
                $score->save();
                return $this->responseSuccess('success',200,['api_token'=>$api_token]);
            }
            $errmsg = $responseJson->errmsg;
            return $this->responseFailed('login failed :'.$errmsg);

        }catch (GuzzleException $e){
            $error = $e->getMessage();
            return $this->responseFailed('login failed');
        }

    }

    function storeScore(Request $request){
        $score_time = $request->input('score_time');
        $api_token = $request->header('Authorization');
        $userInfo = $request->input('userInfo');
        $degree = $request->input('degree');
        try{
            $score = Score::where('api_token',$api_token)->firstOrFail();
            /*提交的等级高于现在的等级则更新最高等级和成绩*/
            if($score->degree<$degree){
                $score->degree = $degree;
                $score->score_time = $score_time;
            }else{
                /*double型判断是否相等，判断提交的等级是否与所存在的相等，相等则更新成绩，否则不更新*/
                if(abs($score->degree-$degree)<0.00001) {
                    if ($score->score_time > $score_time) {
                        $score->score_time = $score_time;
                    }
                }
            }
            $score->avatar_url = $userInfo['avatar_url'];
            $score->nickname = $userInfo['nickname'];
            $score->save();
            return $this->responseSuccess();
        }catch (\Exception $e){
            return $this->responseFailed('unauthorized!'.$e->getMessage());
        }
    }

    function getRanks(){
        try{
            $scores = Score::where('nickname','!=','')->orderBy('score_time','desc')->take(100)->get();
            $scoresArray = $scores->toArray();
            array_multisort(array_column($scoresArray,'degree'),SORT_DESC,$scoresArray);
            return $this->responseSuccess('success',200,$scoresArray);

        }catch (\Exception $e){
            return $this->responseFailed();
        }

    }
    //
}
