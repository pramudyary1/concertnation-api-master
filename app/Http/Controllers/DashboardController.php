<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\event;
use App\Models\order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function admin(){
        try{
            $user = User::where('role','<>','admin')->count();
            $event = event::count();
            $order = order::whereMonth('created_at', Carbon::now()->month)->count();
            $session = DB::table('personal_access_tokens')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->get();
            $popular = event::with(['images'])->withCount('order')->orderBy('order_count','desc')->limit(5)->get();
            return response( [
                'message' => 'success',
                'data' => [
                    'user'=> $user,
                    'event'=> $event,
                    'order'=> $order,
                    'session'=> $session,
                    'popular' => $popular
                ]
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong'.$e->getMessage(),
            ],400);
        }
    }

    public function promotor(string $id){
        try{
            $buyer = order::with('event')->whereRelation('event', 'user_id',$id)->count();
            $event = event::where('user_id',$id)->count();
            $order = order::with('event')->whereRelation('event', 'user_id',$id)->whereMonth('created_at', Carbon::now()->month)->count();
            $sales = DB::table('orders')
            ->select(DB::raw('DATE(orders.created_at) as date'), DB::raw('COUNT(*) as count'))
            ->leftJoin('events', 'event_id', '=', 'event_id')
            ->where('events.user_id', $id)
            ->groupBy('orders.created_at')
            ->get();
            $payment = order::with(['event','user'])->whereRelation('event', 'user_id',$id)->orderBy('created_at','desc')->limit(10)->get();

            return response( [
                'message' => 'success',
                'data' => [
                    'buyer'=> $buyer,
                    'event'=> $event,
                    'order'=> $order,
                    'sales'=> $sales,
                    'payment' => $payment
                ]
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong'.$e->getMessage(),
            ],400);
        }
    }
}
