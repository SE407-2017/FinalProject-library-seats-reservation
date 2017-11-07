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
use Carbon\Carbon;


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
    public function apiReservationCancel()
    {
        $check_result = $this->checkLastReservationFailedOrNot();
        $expired_or_not = $check_result[1];
        $this_reservation = $check_result[0];

        if ($this_reservation == NULL) {
            //说明没有需要取消的预约
        } else {
            $this_reservation->is_arrived = 0;
            $this_reservation->is_left = 1;
            $this_reservation->save();
            $num = $this->getUserFailedReservation(Carbon::now()->toDateString())->count(); // 预约取消次数
            if ($expired_or_not) {
                //说明最近一次预约已经失效
            } else {
                //预约取消成功
            }
        }
        return redirect('/reserve/detail');
    }

    public function checkReservation($request)
    {
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
        }
        return array(
            "success" => $is_valid,
            "msg" => $err_msg,
        );
    }

    public function getUserFailedReservation($date = null)
    {
        $all_failed_resv = Reservations::where('jaccount', Session::get('jaccount'))->where('is_arrived', 0)->where('is_left', 1);
        if ($date == null) {
            return $all_failed_resv->get();
        } else {
            return $all_failed_resv->whereDate('created_at','=' ,$date)->get();
        }
    }

    public function apiReservationAdd(Request $request)
    {
        $check = $this->checkReservation($request);
        if ($check->success == true) {
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
                "msg" => $check->msg,
            ));
        }
    }

    public function showDetail()
    {
        $all_reservations = Reservations::where('jaccount',Session::get('jaccount'))->orderBy('arrive_at','desc')->get() ;
        $user_info = array(
            'true_name' => Session::get('true_name'),
            'student_id' => Session::get('student_id'),
            'jaccount' => Session::get('jaccount'),
        );
        echo $this->getUserFailedReservation(Carbon::now()->toDateString())->count();
        return view('reserve/detail',compact('user_info','all_reservations'));
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
