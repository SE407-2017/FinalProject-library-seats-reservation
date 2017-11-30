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
use App\WXBizDataCrypt;
use Carbon\Carbon;


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

    private function CheckMessage($message)
    {
        if ($message->MsgType == 'event') {
            if ($message->Event == 'subscribe')
                return 'subscribe';

            return 'unsubscribe';
        }
        else {
            return 'valid';
        }
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

    public function apiGetOpenid(Request $request) {
        $appid = env("WECHAT_APPID");
        $appsecret = env("WECHAT_APPSECRET");
        $userinfo = file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$appsecret}&js_code={$request->code}&grant_type=authorization_code");
        $userinfo = json_decode($userinfo);
        return Response::json($userinfo);
    }

    private function Reply($old_message, $reply_content)
    {
        $wechat_reply = [
            'ToUserName'   => $old_message->FromUserName,
            'FromUserName' => $old_message->ToUserName,
            'CreateTime'   => time(),
            'MsgType'      => 'text',
            'Content'      => $reply_content
        ];
        //return view('wechat/reply', $wechat_reply);
        echo "<xml>
            <ToUserName><![CDATA[{$old_message->FromUserName}]]></ToUserName>
            <FromUserName><![CDATA[{$old_message->ToUserName}]]></FromUserName>
            <CreateTime>".time()."</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[{$reply_content}]]></Content>
        </xml>
        ";
    }

    public function wechatBindByOpenid(Request $request)
    {
        $openid = $request->openid;
        $record = Wechat::where('wxid', $openid);
        $wechat = null;
        if ($record->count() == 0) {
            $wechat = new Wechat(array(
                "wxid" => $openid,
                "jaccount" => "",
                "token" => "BIND_BY_WECHAT_APP",
            ));
        } else {
            $wechat = $record->first();
        }
        if (Session::get('jaccount') != "") {
            $wechat->jaccount = Session::get('jaccount');
            $wechat->save();
            return view('wechat/success')->with(array(
                "JaccountID" => Session::get('jaccount'),
                "JaccountUserName" => Session::get('true_name'),
            ));
        } else {
            app('App\Http\Controllers\Auth\JaccountController')->wechat_login($openid, BY_WECHAT_APP);
        }
    }

    public function getBindURL($wxid)
    {
        $record = Wechat::where("wxid", $wxid)->get();
        if ($record->count() == 0) {
            $token = str_random(8);
            $wechat = new Wechat(array(
                'wxid' => $wxid,
                'token' => $token,
                'jaccount' => '',
            ));
            $wechat->save();
            return array(
                'url' => url('/wechat/bind/' . $token),
                'msg' => '进入以下链接完成JAccount绑定: '
            );
        } elseif ($record->first()->jaccount == "") {
            return array(
                'url' => url('/wechat/bind/' . $record->first()->token),
                'msg' => '进入以下链接完成JAccount绑定: '
            );
        } elseif ($record->first()->jaccount != "") {
            return array(
                'url' => url('/wechat/bind/' . $record->first()->token),
                'msg' => "您目前已绑定JAccount账号: {$record->first()->jaccount}。要更换绑定账号，进入以下链接完成JAccount绑定: "
            );
        } else {
            return array(
                'url' => '',
                'msg' => '未知错误'
            );
        }
    }

    public function wechatBind(Request $request)
    {
        $record = Wechat::where("token", $request->token)->get();
        if ($record->count() == 0) {
            die('Invalid token');
        } else {
            if (Session::get('jaccount') != "") {
                $wechat = $record->first();
                $wechat->jaccount = Session::get('jaccount');
                $wechat->save();
                return view('wechat/success')->with(array(
                    "JaccountID" => Session::get('jaccount'),
                    "JaccountUserName" => Session::get('true_name'),
                ));
            } else {
                app('App\Http\Controllers\Auth\JaccountController')->wechat_login($request->token);
            }
        }
    }

    public function apiDecryptData(Request $request)
    {
        $data = "";
        $pc = new WXBizDataCrypt(env("WECHAT_APPID"), $request->sessionKey);
        $errCode = $pc->decryptData($request->encryptedData, $request->iv, $data );

        if ($errCode == 0) {
            echo $data;
        } else {
            return Response::json(array(
                "success" => false,
                "errcode" => $errCode,
            ));
        }
    }

    public function MsgHandler()
    {
        if (!$this->CheckSignature())
            return 'Invalid Request';

        $raw_message = file_get_contents('php://input');
        $message = simplexml_load_string($raw_message, 'SimpleXMLElement', LIBXML_NOCDATA);
        $message->Content = trim($message->Content);

        $check_result = $this->CheckMessage($message);
        if ($check_result == 'valid') {
            if ($message->Content == "绑定") {
                $msg = $this->getBindURL($message->FromUserName);
                $this->Reply($message, $msg['msg'] . $msg['url']);
            } else {
                //$this->Reply($message, $message->Content);
                $this->Reply($message, "Shinko最可爱w");
            }
        }
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

    public function getReservationInfoUsingWechatID(Request $request)
    {
        $wxid = $request->wxid;
        $jaccount = Wechat::where("wxid",$wxid)->first()->jaccount;
        $all_reservations = Reservations::where('jaccount', $jaccount)->orderBy('created_at','desc')->get();
        foreach ($all_reservations as $reservation) {
            $reservation->floor;
            $reservation->status = $this->getReservationStatus($reservation);
        }
        return Response::json(array(
            "success" => true,
            "count" => $all_reservations->count(),
            "data" => $all_reservations,
        ));
    }

    public function ifSeatFree($floor, $table, $seat)
    {
        $reservation = Reservations::where("jaccount", Session::get("jaccount"))->where("floor_id", $floor)->where("seat_id", $seat)->where("table_id", $table)->where("is_left", 0);
        if ($reservation->count() == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkResvValid($reservation, $floor, $table, $seat)
    {
        if ($reservation->floor_id == $floor && $reservation->table_id == $table && $reservation->seat_id == $seat) {
            return true;
        } else {
            return false;
        }
    }

    public function getHitokoto()
    {
        $hitokoto = json_decode(file_get_contents("https://sslapi.hitokoto.cn/?c=d"));
        return $hitokoto;
    }

    public function wechatInSeat(Request $request)
    {
        $seat_json = json_decode(base64_decode($request->seat_id));
        $floor = $seat_json->floor;
        $table = $seat_json->table;
        $seat = $seat_json->seat;

        $hitokoto = $this->getHitokoto();

        $reservation = Reservations::where("jaccount", Session::get("jaccount"))->where("is_arrived", 1)->where("is_left", 0)->get();
        if ($reservation->count() != 0) {
            $resv = $reservation->first();
            if ($this->checkResvValid($resv, $floor, $table, $seat)) {
                return view("wechat/in_seat", array("data" => $resv, "hitokoto" => $hitokoto,"msg" => ""));
            } else {
                return view("wechat/fail", array("msg" => "该座位不是您预约的座位", "hitokoto" => $hitokoto));
            }
        } else {
            $reservation = Reservations::where("jaccount", Session::get("jaccount"))->where("is_arrived", 0)->where("is_left", 0)->get();
            if ($reservation->count() == 0) {
                if ($this->ifSeatFree($floor, $table, $seat)) {
                    $new_resv = new Reservations(array(
                        "name" => Session::get("true_name"),
                        "jaccount" => Session::get("jaccount"),
                        "floor_id" => $floor,
                        "table_id" => $table,
                        "seat_id" => $seat,
                        "arrive_at" => Carbon::now(),
                        "is_arrived" => true,
                        "is_left" => false,
                    ));
                    $new_resv->save();
                    return view("wechat/in_seat", array("data" => $new_resv, "hitokoto" => $hitokoto,"msg" => ""));
                } else {
                    return view("wechat/fail", array("msg" => "该座位已被占用", "hitokoto" => $hitokoto));
                }
            } else {
                if ($this->checkResvValid($reservation->first(), $floor, $table, $seat)) {
                    $current_resv = $reservation->first();
                    $current_resv->is_arrived = true;
                    $current_resv->save();
                    return view("wechat/in_seat", array("data" => $current_resv, "hitokoto" => $hitokoto, "msg" => "您已扫码入座，离座时请点击离座按钮"));
                } else {
                    return view("wechat/fail", array("msg" => "该座位不是您预约的座位", "hitokoto" => $hitokoto));
                }
            }
        }
    }

    public function wechatLeaveSeat()
    {
        $hitokoto = $this->getHitokoto();
        $reservation = Reservations::where("jaccount", Session::get("jaccount"))->where("is_arrived", 1)->where("is_left", 0)->get();
        if ($reservation->count() == 0) {
            return view("wechat/leave_seat", array("title" => "离座失败", "msg" => "您当前没有进行中预约", "hitokoto" => $hitokoto));
        } else {
            $resv = $reservation->first();
            $resv->is_left = true;
            $resv->save();
            return view("wechat/leave_seat", array("title" => "离座成功！", "msg" => "期待下一次为您服务", "hitokoto" => $hitokoto));
        }
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
