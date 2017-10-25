<?php
/**
 * Created by PhpStorm.
 * User: hebin
 * Date: 2017-09-15
 * Time: 13:11
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Reservations;
use App\Floors;
use App\Tables;
use App\Wechat;

use Response;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Helper\Table;

class WechatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

    }

    private function GenerateVerifyCode()
    {
        $letters = explode(',', $this->config->verifycode_letters);
        $length = intval($this->config->verifycode_length);
        $result = '';
        for ($i = 0; $i < $length; ++$i)
            $result .= $letters[array_rand($letters)];
        return $result;
    }

    private function CheckSignature()
    {
        $signature = Input::get('signature');
        $timestamp = Input::get('timestamp');
        $nonce = Input::get('nonce');
        $token = env('WECHAT_TOKEN', 'no token found');
        $tmp_arr = array($token, $timestamp, $nonce);
        sort($tmp_arr, SORT_STRING);
        $result = sha1(implode($tmp_arr));
        return $result == $signature;
    }

    public function apiLeaveSeat(Request $request) {
        $success = true;
        $err_msg = "";

        $wxid = $request->wxid;
        if (Wechat::where("wxid", $wxid)->count() == 0) {
            $success = false;
            $err_msg = "微信号未绑定Jaccount";
        } else {
            $user = Wechat::where("wxid", $wxid)->first();
            $going_on_resv = Reservations::where("jaccount", $user->jaccount)->where("is_left", 0);
            if ($going_on_resv->count() == 0) {
                $success = false;
                $err_msg = "当前没有进行中预约";
            } else {
                $going_on_resv = $going_on_resv->first();
                $going_on_resv->is_left = 1;
                $going_on_resv->save();
            }
        }

        return Response::json(array(
            "success" => $success,
            "msg" => $err_msg,
        ));

    }

    public function MsgHandler()
    {
        return 0;
    }

    public function FirstVerify()
    {
        if (!$this->CheckSignature())
            return 'Invalid Request';

        $echostr = Input::get('echostr');
        return $echostr;
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
