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

use Response;
use Session;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Helper\Table;

class AdminController extends Controller
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
        return view('admin/home')->with(array(
                'user_info' => $user,
            )
        );
    }

    public function apiFloorsGet() {
        $floors = Floors::get();
        return Response::json(array(
            "success" => true,
            "data" => $floors,
        ));
    }

    public function apiTablesGet() {
        $tables = Tables::get();
        foreach ($tables as $table) {
            $table->floor;
        }
        return Response::json(array(
            "success" => true,
            "data" => $tables,
        ));
    }

    public function apiTablesGetByFloor(Request $request) {
        $tables = Tables::where('floor_id', $request->floor_id)->get();
        foreach ($tables as $table) {
            $table->floor;
        }
        return Response::json(array(
            "success" => true,
            "data" => $tables,
        ));
    }

    public function apiTablesRemove(Request $request) {
        $table = Tables::where('id', $request->table_id)->get();
        $success = true;
        $err_msg = "";
        if ($table->count() == 0) {
            $success = false;
            $err_msg = "桌位不存在";
        } else {
            $table->first()->forceDelete();
        }
        return Response::json(array(
            "success" => $success,
            "msg" => $err_msg,
        ));
    }

    public function apiTablesAdd(Request $request) {
        $floor_id = $request->floor_id;
        $seats_count = $request->seats_count;
        $has_socket = $request->has_socket;

        $success = true;
        $err_msg = "";
        if (Floors::where('id', $floor_id)->count == 0) {
            $success = false;
            $err_msg = "楼层不存在";
        } else {
            $table = new Tables(array(
                "floor_id" => $floor_id,
                "seats_count" => $seats_count,
                "has_socket" => $has_socket,
            ));
            $table->save();
        }
        return Response::json(array(
            "success" => $success,
            "msg" => $err_msg,
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
