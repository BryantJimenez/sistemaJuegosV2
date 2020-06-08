<?php

namespace App\Http\Controllers;

use App\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\ClubStoreRequest;
use App\Http\Requests\ClubUpdateRequest;


class ClubController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clubs=Club::orderBy('id', 'DESC')->get();
        $num=1;
        return view('admin.clubs.index', compact('clubs', 'num'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.clubs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClubStoreRequest $request)
    {
         $count=Club::where('name', request('name'))->count();
        $slug=Str::slug(request('name'), '-');
        if ($count>0) {
            $slug=$slug.'-'.$count;
        }

        // Validación para que no se repita el slug
        $num=0;
        while (true) {
            $count2=Club::where('slug', $slug)->count();
            if ($count2>0) {
                $slug=$slug.'-'.$num;
                $num++;
            } else {
                $data=array('name' => request('name'), 'slug' => $slug );
                break;
            }
        }


        $club=Club::create($data)->save();
        if ($club) {
            return redirect()->route('clubes.index')->with(['type' => 'success', 'title' => 'Registro exitoso', 'msg' => 'El club ha sido registrado exitosamente.']);
        } else {
            return redirect()->route('clubes.index')->with(['type' => 'error', 'title' => 'Registro fallido', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }

    /** 
     * Display the specified resource.
     *
     * @param  \App\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $club=Club::where('slug', $slug)->firstOrFail();
        echo json_encode([
            'name' => $club->name,
            'state' => userState($club->state)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $club=Club::where('slug', $slug)->firstOrFail();
        return view('admin.clubs.edit', compact("club"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function update(ClubUpdateRequest $request, $slug)
    {
        $club=Club::where('slug', $slug)->firstOrFail();
        $club->fill($request->all())->save();

        if ($club) {
            return redirect()->route('clubes.edit', ['slug' => $slug])->with(['type' => 'success', 'title' => 'Edición exitosa', 'msg' => 'El club ha sido editado exitosamente.']);
        } else {
            return redirect()->route('clubes.edit', ['slug' => $slug])->with(['type' => 'error', 'title' => 'Edición fallida', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $club=Club::where('slug', $slug)->firstOrFail();
        $club->delete();

        if ($club) {
            return redirect()->route('clubes.index')->with(['type' => 'success', 'title' => 'Eliminación exitosa', 'msg' => 'El club ha sido eliminado exitosamente.']);
        } else {
            return redirect()->route('clubes.index')->with(['type' => 'error', 'title' => 'Eliminación fallida', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }
}
