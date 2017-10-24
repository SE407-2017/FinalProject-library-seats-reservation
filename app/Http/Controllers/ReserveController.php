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
            return $all_failed_resv->whereDate('created_at', '=', $date)->get();
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
                $status = [1, "等待前往"];
            }
        }
        return $status;
    }

    public function apiReservationAll()
    {
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
