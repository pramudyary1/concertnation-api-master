<?php

namespace App\Http\Controllers;

use App\Mail\Ticket;
use App\Models\order;
use App\Models\event;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try{
            $data = order::with('image')->get();
            return response( [
                'message' => 'success',
                'data' => $data
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong',
            ],400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try{
            $data = $request->validate([
                'event_id' => 'required|exists:events,id',
                'user_id' => 'required|exists:users,id',
                'quantity' => 'required|numeric|min:0',
                'payment_type' => 'required|string',
                'bank_name' => 'required|string',
                'account_name' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);
            unset($data['image']);
            $randomId = Str::random(10);
            if($data['account_name'] == null)  $data['account_name'] = "";
            $data['order_number'] = strtoupper($randomId);
            $data['total'] = $data['quantity'] * event::find($data['event_id'])->price;
            $data['order_number'] = strtoupper($randomId);
            $data['status'] = $data['payment_type'] == 'e-wallet' ? 'verified' : 'pending';

            event::find($data['event_id'])->decrement('stock', $data['quantity']);

            $order = order::create($data);
            if($data['status'] == 'verified'){
                \Mail::to(order::find($order->id)->user->email)->send(new Ticket( order::with(['event.images','user'])->find($order->id) ));
            }

            if($request->file('image') != null){
                $image = $request->file('image');

                $fileName = uniqid().'.'.$image->getClientOriginalExtension();

                $imageUrl = $image->storePubliclyAs('public/images', $fileName);

                $order->image()->create([
                    'url' => asset(Storage::url($imageUrl)),
                    'is_thumbnail' => false,
                ]);
            }

            return response( [
                'message' => 'success',
                'data' => order::with(['image','user','event'])->find($order->id)
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong'.$e->getMessage(),
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try{
            $data = order::with(['image','user','event'])->find($id);
            return response( [
                'message' => 'success',
                'data' => $data
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong',
            ],400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $data = $request->validate([
                'status' => 'required|string',
            ]);
            // $data['status'] = 'pending';
            $order = order::where('id', $id)->update($data);
            if($data['status'] == 'verified'){
                \Mail::to(order::find($id)->user->email)->send(new Ticket( order::with(['event.images','user'])->find($id) ));
            }
            return response( [
                'message' => 'success',
                'data' => order::with('image')->find($id)
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong'.$e->getMessage(),
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try{
            $image = order::find($id)->image ;

            $tok = explode('/', $image->url);
            Storage::delete('public/image/'.end($tok));
            $image->delete();

            $data = order::where('id', $id)->delete();
            return response( [
                'message' => 'success',
                'data' => $data
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong',
            ],400);
        }
    }

    public function getOrderByPromotor(string $id)
    {
        try{
            $data = order::with(['event', 'user'])->whereHas('event', function ($query) use($id) {
                return $query->where('user_id', $id);
            })->get();

            return response( [
                'message' => 'success',
                'data' => $data
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong'.$e,
            ],400);
        }
    }
}
