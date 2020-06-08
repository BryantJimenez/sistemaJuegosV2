<?php

namespace App\Http\Controllers;

use App\Gamer;
use App\GameWinner;
use App\TournamentWinner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\GamerStoreRequest;
use App\Http\Requests\GamerUpdateRequest;

class GamerController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gamers=Gamer::orderBy('id', 'DESC')->get();
        $num=1;
        return view('admin.gamers.index', compact('gamers', 'num'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.gamers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GamerStoreRequest $request)
    {
        $count=Gamer::where('name', request('name'))->where('lastname', request('lastname'))->count();
        $slug=Str::slug(request('name')." ".request('lastname'), '-');
        if ($count>0) {
            $slug=$slug.'-'.$count;
        }

        // Validación para que no se repita el slug
        $num=0;
        while (true) {
            $count2=Gamer::where('slug', $slug)->count();
            if ($count2>0) {
                $slug=$slug.'-'.$num;
                $num++;
            } else {
                $data=array('name' => request('name'), 'lastname' => request('lastname'), 'slug' => $slug);
                break;
            }
        }

        // Mover imagen a carpeta users y extraer nombre
        if ($request->hasFile('photo')) {
            $file=$request->file('photo');
            $photo=time()."_".$file->getClientOriginalName();
            $file->move(public_path().'/admins/img/users/', $photo);
            $data['photo']=$photo;
        }

        $gamer=Gamer::create($data)->save();
        if ($gamer) {
            return redirect()->route('jugadores.create')->with(['type' => 'success', 'title' => 'Registro exitoso', 'msg' => 'El jugador ha sido registrado exitosamente.']);
        } else {
            return redirect()->route('jugadores.create')->with(['type' => 'error', 'title' => 'Registro fallido', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Gamer  $gamer
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $gamer=Gamer::where('slug', $slug)->firstOrFail();
        $games=0;
        $winners=0;
        $winnerTournaments=0;
        $default=0;
        $gamesInfo=[];
        foreach ($gamer->couples as $couple) {
            $winners+=GameWinner::where('couple_id', $couple->id)->count();

            $winnerTournaments+=TournamentWinner::where('couple_id', $couple->id)->count();

            foreach ($couple->games as $game) {
                if ($game->state==1 || $game->state==2) {
                    $default++;
                }
                $gamesInfo[$games]=['slug' => $game->slug, 'type' => $game->type, 'state' => $game->state, 'created_at' => $game->created_at];
                $games++;
            }
        }
        $loses=$games-$winners-$default;
        $num=1;

        return view('admin.gamers.show', compact("gamer", "games", "winners", "loses", "winnerTournaments", 'gamesInfo', 'num'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Gamer  $gamer
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $gamer=Gamer::where('slug', $slug)->firstOrFail();
        return view('admin.gamers.edit', compact("gamer"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Gamer  $gamer
     * @return \Illuminate\Http\Response
     */
    public function update(GamerUpdateRequest $request, $slug)
    {
        $data=$request->all();
        $gamer=Gamer::where('slug', $slug)->firstOrFail();
        // Mover imagen a carpeta users y extraer nombre
        if ($request->hasFile('photo')) {
            $file=$request->file('photo');
            $photo=time()."_".$file->getClientOriginalName();
            $file->move(public_path().'/admins/img/users/', $photo);
            $data['photo']=$photo;
        }
        $gamer->fill($data)->save();

        if ($gamer) {
            return redirect()->route('jugadores.edit', ['slug' => $slug])->with(['type' => 'success', 'title' => 'Edición exitosa', 'msg' => 'El jugador ha sido editado exitosamente.']);
        } else {
            return redirect()->route('jugadores.edit', ['slug' => $slug])->with(['type' => 'error', 'title' => 'Edición fallida', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Gamer  $gamer
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $gamer=Gamer::where('slug', $slug)->firstOrFail();
        $gamer->delete();

        if ($gamer) {
            return redirect()->route('jugadores.index')->with(['type' => 'success', 'title' => 'Eliminación exitosa', 'msg' => 'El jugador ha sido eliminado exitosamente.']);
        } else {
            return redirect()->route('jugadores.index')->with(['type' => 'error', 'title' => 'Eliminación fallida', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }
}
