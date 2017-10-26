<?php

namespace App\Http\Controllers\Auth;

include 'clsJAccount.php';

use Illuminate\Support\Facades\Input;
use JaHelper;
use Session;
use Hash;
use Illuminate\Http\Request;
use Auth;
use Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Jaccount;
use App\User;
use App\Schedule;

class JaccountController extends Controller
{
    public function login($redirect = "/reserve/home")
    {
        $jatkt = Input::get('jatkt');
        $ht = JaHelper::jalogin($jatkt, '/auth/login');
        // $ht = JaHelper::jalogin($jatkt,'/');
        isset($ht['uid']) ? ($jaccount_id = $ht['uid']) : exit;

        Session::put('true_name', iconv("GB2312//IGNORE", "UTF-8", $ht['chinesename']));
        Session::put('student_id', $ht['id']);
        Session::put('jaccount', $ht['uid']);

        if(Jaccount::where('account_name', '=', $ht['uid'])->count() == 0){
            $realName = array_key_exists('chinesename',$ht)?$ht['chinesename']:'';
            $snum = array_key_exists('id',$ht)?$ht['id']:'';
            $isStudent = array_key_exists('student',$ht) ? (($ht['student'] == 'yes') ? 1 : 0) : 0;
            $dept = array_key_exists('dept',$ht)?$ht['dept']:'';
            $tel = array_key_exists('tel',$ht)?$ht['tel']:'';
            $realName=mb_convert_encoding($realName,'UTF-8','GBK');
            $dept=mb_convert_encoding($dept,'UTF-8','GBK');
            $email = $ht['uid']."@sjtu.edu.cn";

            $user = new User;
            $user->name = $ht['uid'];
            $user->password = Hash::make('jaccount.password');
            $user->email = $email;
            $user->is_admin = 0;
            $user->save();
            
            $jaccount = new Jaccount;
            $jaccount->user_id = $user->id;
            $jaccount->account_name = $ht['uid'];
            $jaccount->real_name = $realName;
            $jaccount->department = $dept;
            $jaccount->student_number = $snum;
            $jaccount->is_student = $isStudent;
            $jaccount->save();
        }
        $credentials = array('name' => ($ht['uid']), 'password' => 'jaccount.password');

        Auth::attempt($credentials);

        return redirect($redirect);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        JaHelper::jalogout($request->query('redirect', ''));
    }

    public function forbidden()
    {
        return view('forbidden');
    }

    public function redirect()
    {
        if (Auth::check()) {
            if (Auth::user()->is_admin == 0)
                return redirect('/reserve');
            else
                return redirect('/admin');
        } else
            return redirect('/');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
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
