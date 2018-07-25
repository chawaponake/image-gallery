<?php

namespace App\Http\Controllers;

use App\Medias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $medias = Medias::all();
            $data = [];

            foreach ($medias as $media){
                array_push($data,[
                    'id' => $media->id,
                    'name' => $media->name,
                    'media' => asset(Storage::disk('local')->url($media->media)),
                    'isImage' => $media->mime_type == 'image/jpeg' || $media->mime_type == 'image/png' ? true  : false,
                    'isOverMaxSize' => $media->size > 10485760 ? true : false
                ]);
            }

            return response()->json([
                'msg' => 'Successfully',
                'data' => $data
            ]);
        }else{
            return view('gallery');
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [];
        $msg = 'Fails';
        if($request->hasFile('files')){
//            foreach ($request->file('files') as $file){
            $file = $request->file('files')[0];
            $name = $file->getClientOriginalName();
            $media = $file->store('public/images');
            $mime_type = $file->getClientMimeType();
            $size = $file->getClientSize();

            $media = Medias::create([
                'name' => $name,
                'media' => $media,
                'mime_type' => $mime_type,
                'size' => $size
            ]);

            array_push($data,[
                'id' => $media->id,
                'name' => $media->name,
                'media' => asset(Storage::disk('local')->url($media->media)),
                'isImage' => $mime_type == 'image/jpeg' || $mime_type == 'image/png' ? true  : false,
                'isOverMaxSize' => $size > 10485760 ? true : false
            ]);


//            }
            $msg = 'Successfully';
        }

        return response()->json([
            'msg' => $msg,
            'data' => $data
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $media = Medias::findOrFail($id);

        Storage::delete($media->media);

        $media->delete();

        return response()->json([
            'msg' => 'Successfully',
        ]);
    }
}
