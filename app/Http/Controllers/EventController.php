<?php

namespace App\Http\Controllers;

use App\Models\event;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function latest()
    {
        //
        try{
            $data = event::with(['images','user'])->where('is_published',1)->orderByDesc('created_at')->limit(5)->get();
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

    public function popular()
    {
        //
        try{
            $data = event::with(['images','user'])->where('is_published',1)->withCount('order')->orderByDesc('order_count')->limit(3)->get();
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

    public function featured()
    {
        //
        try{
            $data = event::with(['images','user'])->where('is_published',1)->get();
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

    public function index()
    {
        //
        try{
            $data = event::with(['images','user'])->get();
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
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string',
                'description' => 'required|string',
                'location' => 'required|string',
                'date' => 'required|date',
                'time' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|numeric|min:0',
                'images.*' => 'nullable|file|mimes:jpeg,png,jpg|max:2048'
            ]);
            unset($data['images']);
            $event = event::create($data);

            if($request->file('images') != null)
            foreach ($request->file('images') as $image) {

                $fileName = uniqid().'.'.$image->getClientOriginalExtension();

                $imageUrl = $image->storePubliclyAs('public/images', $fileName);

                $event->images()->create([
                    'url' => asset(Storage::url($imageUrl)),
                    'is_thumbnail' => false,
                ]);
            }

            return response( [
                'message' => 'success',
                'data' => event::with('images')->find($event->id)
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
            $data = event::with('images')->find($id);
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
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string',
                'description' => 'required|string',
                'location' => 'required|string',
                'date' => 'required|date',
                'time' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|numeric|min:0',
                'images.*' => 'nullable|file|mimes:jpeg,png,jpg|max:2048'
            ]);
            unset($data['images']);

            $event = event::where('id', $id)->update($data);

            if($request->file('images') != null){
                // dd(event::find($id)->images);
                foreach (event::find($id)->images as $image){
                    $tok = explode('/', $image->url);
                    Storage::delete('public/images/'.end($tok));
                    $image->delete();
                }
                foreach ($request->file('images') as $image) {
                    $fileName = uniqid().'.'.$image->getClientOriginalExtension();

                    $imageUrl = $image->storePubliclyAs('public/images', $fileName);

                    event::find($id)->images()->create([
                        'url' => asset(Storage::url($imageUrl)),
                        'is_thumbnail' => false,
                    ]);
                }
            }

            return response( [
                'message' => 'success',
                'data' => event::with('images')->find($id)
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
            foreach (event::find($id)->images as $image){
                $tok = explode('/', $image->url);
                Storage::delete('public/images/'.end($tok));
                $image->delete();
            }
            $data = event::where('id', $id)->delete();
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

    public function getUnpublished()
    {
        //
        try{
            $data = event::with('user')->where('is_published',0)->get();
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

    public function publish(Request $request, string $id)
    {
        try{
            $data = $request->validate([
                'is_published' => 'nullable|boolean',
                'is_deleted' => 'nullable|boolean',
            ]);

            $event = event::where('id', $id)->update($data);

            return response( [
                'message' => 'success',
                'data' => event::with('images')->find($id)
            ],200);
        }
        catch(\Exception $e){
            return response( [
                'message' => 'Something went wrong'.$e->getMessage(),
            ],400);
        }
    }
}
