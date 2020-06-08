<?php

namespace App\Http\Controllers;

use App\Game;
use App\Gamer;
use App\Couple;
use App\CoupleGroup;
use App\CoupleGame;
use App\CoupleGamer;
use Illuminate\Http\Request;
use App\Http\Requests\GameStoreRequest;
 
class GameController extends Controller
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
        $games=Game::where('state', 2)->orWhere('state', 3)->orderBy('id', 'DESC')->get();
        $num=1;
        return view('admin.games.index', compact('games', 'num'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $gamers=Gamer::all();
        return view('admin.games.create', compact('gamers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GameStoreRequest $request)
    {
        $couple1=request('couple1');
        $gamer1=Gamer::where('slug', $couple1[0])->firstOrFail();
        $gamer2=Gamer::where('slug', $couple1[1])->firstOrFail();

        $couple2=request('couple2');
        $gamer3=Gamer::where('slug', $couple2[0])->firstOrFail();
        $gamer4=Gamer::where('slug', $couple2[1])->firstOrFail();

        $couples1=Couple::create();
        $couples2=Couple::create();

        CoupleGamer::create(['couple_id' => $couples1->id, 'gamer_id' => $gamer1->id]);
        CoupleGamer::create(['couple_id' => $couples1->id, 'gamer_id' => $gamer2->id]);
        CoupleGamer::create(['couple_id' => $couples2->id, 'gamer_id' => $gamer3->id]);
        CoupleGamer::create(['couple_id' => $couples2->id, 'gamer_id' => $gamer4->id]);

        $count=Game::all()->count();
        $countRepeat=Game::where('slug', 'juego-'.$count)->count();

        if ($countRepeat>0) {
            $slug='juego-'.$countRepeat.'-'.$count;
        } else {
            $slug='juego-'.$count;
        }

        // Validación para que no se repita el slug
        $num=0; 
        while (true) {
            $count2=Game::where('slug', $slug)->count();
            if ($count2>0) {
                $slug='juego-'.$num.'-'.$count;
                $num++;
            } else {
                break;
            }
        }

        if (request('points1')==2 || request('points2')==2) {
            $state=3;
        } else {
            $state=2;
        }

        $data=array('slug' => $slug, 'state' => $state);
        $game=Game::create($data);

        $coupleGame1=CoupleGame::create(['couple_id' => $couples1->id, 'game_id' => $game->id, 'points' => request('points1')])->save();
        $coupleGame2=CoupleGame::create(['couple_id' => $couples2->id, 'game_id' => $game->id, 'points' => request('points2')])->save();

        if ($coupleGame1 && $coupleGame2) {
            return redirect()->route('juegos.index')->with(['type' => 'success', 'title' => 'Registro exitoso', 'msg' => 'El juego ha sido registrado exitosamente.']);
        } else {
            return redirect()->route('juegos.index')->with(['type' => 'error', 'title' => 'Registro fallido', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $game=Game::where('slug', $slug)->firstOrFail();
        
        echo json_encode([
            'type' => gameType($game->type),
            'state' => gameState($game->state),
            'points1' => $game->couple_game[0]->points,
            'points2' => $game->couple_game[1]->points,
            'couple1' => couplesNames($game->couples, 1, 1),
            'couple2' => couplesNames($game->couples, 2, 1)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $game=Game::where('slug', $slug)->firstOrFail();
        $game->delete();

        if ($game) {
            return redirect()->route('juegos.index')->with(['type' => 'success', 'title' => 'Eliminación exitosa', 'msg' => 'El juego ha sido eliminado exitosamente.']);
        } else {
            return redirect()->route('juegos.index')->with(['type' => 'error', 'title' => 'Eliminación fallida', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }
}
