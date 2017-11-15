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
use App\Jaccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use stdClass;

use Response;
use Session;
use Illuminate\Support\Facades\Storage;

class ReserveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $user = array(
            'true_name' => Session::get('true_name'),
            'student_id' => Session::get('student_id'),
            'jaccount' => Session::get('jaccount'),
        );
        return view('reserve/home')->with(array(
            'user_info' => $user,
            )
        );
    }

    /**
     * 检查是否有超时的预约
     * **注意**！ 当提供true为参数时，将遍历整张表，并依次检查是否超时
     *
     * @param bool $all_user
     * @return array true when $all_user is false, false otherwise.
     *              always true when $all_user is true
     */
    public function checkLastReservationFailedOrNot($all_user = false)
    {
        if (!$all_user) {
            $current_user = Session::get('jaccount');
            $last_reservation = Reservations::where('jaccount', $current_user)->orderBy('created_at','desc')->first();
            if ($last_reservation != NULL) {
                $reservation_time = strtotime($last_reservation->arrive_at);
                $current_time = time();
                if ($current_time - $reservation_time > 900) {
                    return [$last_reservation, true];
                }
            }
            return [$last_reservation, false];
        } else {
            $reservations = Reservations::where('is_arrived', 0)->where('is_left', 0)->get();
            if($reservations != NULL) {
                foreach ($reservations as $reservation) {
                    $reservation_time = strtotime($reservation->arrive_at);
                    $current_time = time();
                    if ($current_time - $reservation_time > 900) {
                        $reservation->is_arrived = 0;
                        $reservation->is_left = 1;
                        $reservation->save();
                    }
                }
            }
            return NULL;
        }
    }


    /**
     * Set the specified column as "failed".
     *
     * @return Response
     */
    public function apiReservationCancel(Request $request)
    {
        $reservation = Reservations::where("id", $request->id)->get();
        if ($reservation->count() == 0) {
            return Response::json(array(
                "success" => false,
                "msg" => "预约不存在"
            ));
        } else {
            if ($reservation->first()->is_arrived == true) {
                return Response::json(array(
                    "success" => false,
                    "msg" => "预约行程已开始"
                ));
            } elseif ($reservation->first()->is_left == true) {
                return Response::json(array(
                    "success" => false,
                    "msg" => "预约已失效"
                ));
            } else {
                $r = $reservation->first();
                $r->is_left = true;
                $r->save();
                return Response::json(array(
                    "success" => true,
                    "msg" => "取消成功"
                ));
            }
        }
    }

    public function refreshReservationStatus()
    {
        $reservations = Reservations::where('is_left', 0)->get();
        $dt = Carbon::now();
        foreach ($reservations as $reservation) {
            $diff_minutes = Carbon::createFromTimestamp(strtotime($reservation->arrive_at))->diffInMinutes($dt);
            if ($diff_minutes >= 15) {
                $reservation->is_left = 1;
                $reservation->save();
            }
        }
    }

    public function checkReservation($request)
    {
        $this->refreshReservationStatus();
        $is_valid = true;
        $err_msg = "";
        if (!Floors::where('id', $request->floor_id)) {
            $is_valid = false;
            $err_msg = "楼层不存在";
        } elseif (!Tables::where('id', $request->table_id)) {
            $is_valid = false;
            $err_msg = "桌位不存在";
        } elseif ($request->seat_id < 0 || $request->seat_id > 3) {
            $is_valid = false;
            $err_msg = "座位不存在";
        } elseif (Carbon::instance(new \DateTime($request->arrive_at))->toDateString() != Carbon::now()->toDateString()) {
            $is_valid = false;
            $err_msg = "仅可提交今日预约";
        } elseif ($this->getUserFailedReservation(Carbon::now()->toDateString())->count() >= 3) {
            $is_valid = false;
            $err_msg = "今日预约失败次数已达上限";
        } elseif (Reservations::where('jaccount', Session::get('jaccount'))->where('is_left', 0)->count() != 0) {
            $is_valid = false;
            $err_msg = "存在尚未完成预约";
        } elseif (Reservations::where('table_id', $request->table_id)->where('seat_id', $request->seat_id)->where('is_left', 0)->count() != 0) {
            $is_valid = false;
            $err_msg = "当前座位已被预约";
        }
        return array(
            "success" => $is_valid,
            "msg" => $err_msg,
        );
    }
	
	public function apiTableDetail(Request $request)
    {
        $table = Tables::where('id', $request->table_id)->first();
        $table->floor;
        $avail_seats = array();
        for ($i = 0; $i < $table->seats_count; $i++) {
            if (Reservations::where('table_id', $request->table_id)->where('seat_id', $i)->where('is_left', 0)->count() == 0) {
                $avail_seats[] = $i;
            }
        }
        $table->avail_seats = $avail_seats;
        return $table;
    }

    public function getUserFailedReservation($date = null)
    {
        $all_failed_resv = Reservations::where('jaccount', Session::get('jaccount'))->where('is_arrived', 0)->where('is_left', 1);
        if ($date == null) {
            return $all_failed_resv->get();
        } else {
            return $all_failed_resv->whereDate('created_at', '=', $date)->get();
        }
    }

    function array2Object($array) {
        if (is_array($array)) {
            $obj = new StdClass();
            foreach ($array as $key => $val){
                $obj->$key = $val;
            }
        }
        else { $obj = $array; }
        return $obj;
    }

    public function apiReservationAdd(Request $request)
    {
        $request = $this->array2Object(Input::all());
        $check = $this->checkReservation($request);
        if ($check["success"] == true) {
            $reservation = new Reservations(array(
                'name' => Session::get('true_name'),
                'jaccount' => Session::get('jaccount'),
                'floor_id' => $request->floor_id,
                'table_id' => $request->table_id,
                'seat_id' => $request->seat_id,
                'arrive_at' => $request->arrive_at,
                'is_arrive' => false,
                'is_left' => false,
            ));
            $reservation->save();
            return Response::json(array(
                "success" => true,
            ));
        } else {
            return Response::json(array(
                "success" => false,
                "msg" => $check["msg"],
            ));
        }
    }

    public function getReservationStatus($reservation)
    {
        if ($reservation->is_arrived == 1) {
            if ($reservation->is_left == 1) {
                $status = [0, "已完成"];
            } else {
                $status = [2, "进行中"];
            }
        } else {
            if ($reservation->is_left == 1) {
                $status = [3, "已失效"];
            } else {
                $status = [1, "等待前往..."];
            }
        }
        return $status;
    }

    public function apiReservationRemove(Request $request)
    {
        $success = true;
        $err_msg = "";
        $rid = $request->reservation_id;
        $reservation = Reservations::where('id', $rid)->first();
        if ($reservation->count() == 0) {
            $success = false;
            $err_msg = "找不到该预约";
        } elseif ($reservation->jaccount != Session::get('jaccount')) {
            $success = false;
            $err_msg = "该预约非当前用户所有";
        } elseif ($reservation->is_left == 1) {
            $success = false;
            $err_msg = "该预约已结束";
        } elseif ($reservation->is_arrived == 1) {
            $success = false;
            $err_msg = "该预约正在进行";
        } else {
            $reservation->is_left = 1;
            $reservation->save();
        }
        return Response::json(array(
            'success' => $success,
            'msg' => $err_msg,
        ));
    }

    public function apiReservationAll()
    {
        $this->refreshReservationStatus();
        $all_reservations = Reservations::where('jaccount', Session::get('jaccount'))->orderBy('created_at','desc')->get();
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

    public function getReservationInProgress()
    {
        $in_progress_reservation = Reservations::where('jaccount', Session::get('jaccount'))->where('is_arrived', 0)->where('is_left', 0)->get() ;
        return array(
            "count" => $in_progress_reservation->count(),
            "data" => $in_progress_reservation->first(),
        );
    }

    public function apiUserInfo()
    {
        $user_info = Jaccount::where('account_name', Session::get('jaccount'))->first();
        $failed_reservation = $this->getUserFailedReservation(Carbon::now()->toDateString());
        $in_progress_reservation = $this->getReservationInProgress();
        return Response::json(array(
            "success" => true,
            "user_info" => $user_info,
            "reservation" => array(
                "failed" => array(
                    "count" => $failed_reservation->count(),
                    "data" => $failed_reservation,
                ),
                "in_progress" => $in_progress_reservation,
                "all_count" => Reservations::where('jaccount', Session::get('jaccount'))->count(),
            ),
        ));
    }

    public function apiFloorTableStatus(Request $request)
    {
        $tables = Tables::where("floor_id", $request->floor_id)->get();
        foreach ($tables as $table) {
            $table->seats_count -= Reservations::where('is_left', 0)->where('table_id', $table->id)->count();
        }
        return Response::json(array(
            "success" => true,
            "tables" => $tables,
        ));
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
