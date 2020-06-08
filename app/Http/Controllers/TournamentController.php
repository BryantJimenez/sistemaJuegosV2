<?php

namespace App\Http\Controllers;

use App\Tournament;
use App\Gamer;
use App\Club;
use App\Group;
use App\Phase;
use App\Couple;
use App\CoupleGroup;
use App\GamerTournament;
use App\Game;
use App\CoupleGame;
use App\CoupleGamer;
use App\Winner;
use App\GameWinner;
use App\TournamentWinner;
use App\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Requests\TournamentStoreRequest;
use App\Http\Requests\TournamentUpdateRequest;

class TournamentController extends Controller
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
        $tournaments=Tournament::orderBy('id', 'DESC')->get();
        $clubs=Club::orderBy('id', 'DESC')->get();
        $num=1;
        return view('admin.tournaments.index', compact('tournaments', 'clubs', 'num'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clubs=Club::orderBy('id', 'DESC')->get();
        return view('admin.tournaments.create', compact('clubs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(TournamentStoreRequest $request)
    {
        $numberGroup=request('groups');
        $couplesMinMax=['couples' => request('couples'), 'minimo' => $numberGroup*3, 'maximo' => $numberGroup*6];

        $validator=Validator::make($couplesMinMax, [
            'couples' => [
                'required',
                'integer',
                'min:'.$couplesMinMax['minimo'],
                'max:'.$couplesMinMax['maximo']
            ]
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $count=Tournament::where('name', request('name'))->count();
        $slug=Str::slug(request('name'), '-');
        if ($count>0) {
            $slug=$slug.'-'.$count;
        }

        // Validación para que no se repita el slug
        $num=0; 
        while (true) {
            $count2=Tournament::where('slug', $slug)->count();
            if ($count2>0) {
                $slug=$slug.'-'.$num;
                $num++;
            } else {
                $club=Club::where('slug', request('club_id'))->firstOrFail();
                $data=$request->all();
                $data['start']=date('Y-m-d', strtotime(request('start')));
                $data['slug']=$slug;
                $data['club_id']=$club->id;
                break;
            }
        }

        $tournament=Tournament::create($data)->save();
        if ($tournament) {
            return redirect()->route('torneos.index')->with(['type' => 'success', 'title' => 'Registro exitoso', 'msg' => 'El torneo ha sido registrado exitosamente.']);
        } else {
            return redirect()->route('torneos.index')->with(['type' => 'error', 'title' => 'Registro fallido', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tournament  $tournament
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    { 
        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        $clubs=Club::orderBy('id', 'DESC')->get();
        $participants=GamerTournament::where('tournament_id', $tournament->id)->count();
        $groups=Group::where('tournament_id', $tournament->id)->where('phase_id', 1)->get();
        $semifinal=Group::where('tournament_id', $tournament->id)->where('phase_id', 2)->get();
        $final=Group::where('tournament_id', $tournament->id)->where('phase_id', 3)->get();

        $currentPhase=Group::select('phase_id')->where('tournament_id', $tournament->id)->orderBy('id', 'DESC')->first();

        if ($currentPhase!=NULL) {
            $phase=Phase::where('id', $currentPhase->phase_id)->first();
            $groupsPhase=Group::where('phase_id', $currentPhase->phase_id)->where('tournament_id', $tournament->id)->get();
            $gamesEnd=0;
            $gamesTotal=0;
            foreach ($groupsPhase as $groupPhase) {
                $gamesEnd+=Game::where('state', 3)->where('group_id', $groupPhase->id)->count();
                $gamesTotal+=Game::where('group_id', $groupPhase->id)->count();
            }
            $gamesFinish=$gamesTotal-$gamesEnd;
        }

        if ($tournament->state==3) {
            $winners=TournamentWinner::where('tournament_id', $tournament->id)->get();
        }

        $assignments=Assignment::where('tournament_id', $tournament->id)->count();

        return view('admin.tournaments.show', compact("tournament", "clubs", "participants", "groups", "semifinal", "final", "gamesFinish", "currentPhase", "phase", "winners", "assignments"));
    }

    public function edit($slug)
    {
        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        if ($tournament->state==1) {
            $clubs=Club::orderBy('id', 'DESC')->get();
            return view('admin.tournaments.edit', compact('clubs', 'tournament'));
        } else {
            return redirect()->route('torneos.edit', ['slug' => $slug])->with(['type' => 'warning', 'title' => 'Acción denegada', 'msg' => 'No puedes editar este torneo.']);
        }
    }

    public function update(TournamentUpdateRequest $request, $slug)
    {
        $numberGroup=request('groups');
        $couplesMinMax=['couples' => request('couples'), 'minimo' => $numberGroup*3, 'maximo' => $numberGroup*6];

        $validator=Validator::make($couplesMinMax, [
            'couples' => [
                'required',
                'integer',
                'min:'.$couplesMinMax['minimo'],
                'max:'.$couplesMinMax['maximo']
            ]
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        $gamers=GamerTournament::where('tournament_id', $tournament->id)->groupBy('couple_id')->count();

        if ($gamers<=request('couples')) {
            $club=Club::where('slug', request('club_id'))->firstOrFail();
            $data=$request->all();
            $data['start']=date('Y-m-d', strtotime(request('start')));
            $data['club_id']=$club->id;

            $tournament->fill($data)->save();
            if ($tournament) {
                return redirect()->route('torneos.edit', ['slug' => $slug])->with(['type' => 'success', 'title' => 'Edición exitosa', 'msg' => 'El torneo ha sido editado exitosamente.']);
            } else {
                return redirect()->route('torneos.edit', ['slug' => $slug])->with(['type' => 'error', 'title' => 'Edición fallida', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
            }
        } else {
            return redirect()->route('torneos.edit', ['slug' => $slug])->with(['type' => 'warning', 'title' => 'Edición fallida', 'msg' => 'La cantidad de parejas registradas en el torneo es mayor al número de parejas permitidas.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tournament  $tournament
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        $tournament->delete();

        if ($tournament) {
            return redirect()->route('torneos.index')->with(['type' => 'success', 'title' => 'Eliminación exitosa', 'msg' => 'El torneo ha sido eliminado exitosamente.']);
        } else {
            return redirect()->route('torneos.index')->with(['type' => 'error', 'title' => 'Eliminación fallida', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }

    public function listGamers($slug)
    {
        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        $gamers=GamerTournament::where('tournament_id', $tournament->id)->groupBy('couple_id')->get(); 
        $num=1;
        return view('admin.tournaments.list-gamers', compact('tournament', 'gamers', 'num'));
    }

    public function start($slug)
    {
        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        $assignmentsCount=Assignment::where('tournament_id', $tournament->id)->count();

        if ($assignmentsCount==0) {
            $num=0;
            $numCouples=0;
            // Ciclo para crear los grupos
            for ($i=0; $i < $tournament->groups; $i++) {

                $group=$this->tournamentGroups($tournament->id, $tournament->groups, $tournament->groups);

                // Cantidad de parejas para este grupo
                $round=intval(round($tournament->couples/$tournament->groups, 0, PHP_ROUND_HALF_UP));
                $currentCouplesTotal=$round*$i+$round;
                $couplesMissing=$tournament->couples-$currentCouplesTotal;

                if ($tournament->groups<6) {
                    if ($i==$tournament->groups-2) {
                        if ($couplesMissing==$round || $couplesMissing==$round+1) {
                            $numberCouplesGroup=$round;
                        } elseif ($couplesMissing==$round-1 || $couplesMissing==$round-2) {
                            $numberCouplesGroup=$round-1;
                        } elseif ($couplesMissing==$round+2) {
                            $numberCouplesGroup=$round+1;
                        }
                    } elseif ($i==$tournament->groups-1) {
                        if ($couplesMissing==0) {
                            $numberCouplesGroup=$round;
                        } else {
                            $numberCouplesGroup=$tournament->couples-$numCouples;
                        }
                    } else {
                        $numberCouplesGroup=$round;
                    }
                } else {
                    if ($i==$tournament->groups-3) {
                        if ($couplesMissing==$round*2 || $couplesMissing==$round*2+1 || $couplesMissing==$round*2+2 || $couplesMissing==$round*2-2 || $couplesMissing==$round*2-1) {
                            $numberCouplesGroup=$round;
                        } elseif ($couplesMissing==($round+1)*2+1) {
                            $numberCouplesGroup=$round+1;
                        }
                    } elseif ($i==$tournament->groups-2) {
                        if ($couplesMissing==$round || $couplesMissing==$round+1) {
                            $numberCouplesGroup=$round;
                        } elseif ($couplesMissing==$round-1 || $couplesMissing==$round-2) {
                            $numberCouplesGroup=$round-1;
                        } elseif ($couplesMissing==$round+2 || $couplesMissing==$round+3) {
                            $numberCouplesGroup=$round+1;
                        }
                    } elseif ($i==$tournament->groups-1) {
                        if ($couplesMissing==0) {
                            $numberCouplesGroup=$round;
                        } else {
                            $numberCouplesGroup=$tournament->couples-$numCouples;
                        }
                    } else {
                        $numberCouplesGroup=$round;
                    }
                }

                $numCouples+=$numberCouplesGroup;

                $loops=$this->tournamentStart($num, $tournament->id, $group->id, $numberCouplesGroup);
                $num=$loops;

                // Se crean los juegos de la primera vuelta
                $couples=CoupleGroup::where('group_id', $group->id)->get();
                // Se cuenta cuantas parejas hay en el grupo para saber cuantos juegos seran en la primera ronda
                if ($numberCouplesGroup==2) {
                    $slugGame=$this->gameSlug();
                    $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                    CoupleGame::create(['couple_id' => $couples[0]->couple_id, 'game_id' => $game->id])->save();
                    CoupleGame::create(['couple_id' => $couples[1]->couple_id, 'game_id' => $game->id])->save();
                } elseif ($numberCouplesGroup==3) {
                    $slugGame=$this->gameSlug();
                    $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                    CoupleGame::create(['couple_id' => $couples[0]->couple_id, 'game_id' => $game->id])->save();
                    CoupleGame::create(['couple_id' => $couples[1]->couple_id, 'game_id' => $game->id])->save();

                    $slugGame=$this->gameSlug();
                    $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                    CoupleGame::create(['couple_id' => $couples[1]->couple_id, 'game_id' => $game->id])->save();
                    CoupleGame::create(['couple_id' => $couples[2]->couple_id, 'game_id' => $game->id])->save();

                    $slugGame=$this->gameSlug();
                    $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                    CoupleGame::create(['couple_id' => $couples[2]->couple_id, 'game_id' => $game->id])->save();
                    CoupleGame::create(['couple_id' => $couples[0]->couple_id, 'game_id' => $game->id])->save();
                } else {

                    // Primera ronda
                    if ($numberCouplesGroup==6) {
                        $max=5;
                    } else {
                        $max=3;
                    }
                    for ($j=0; $j < $max; $j+=2) {
                        $slugGame=$this->gameSlug();
                        $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                        CoupleGame::create(['couple_id' => $couples[$j]->couple_id, 'game_id' => $game->id])->save();
                        CoupleGame::create(['couple_id' => $couples[$j+1]->couple_id, 'game_id' => $game->id])->save();
                    }

                    // Juegos de las demas rondas
                    $this->allGamesTournament($tournament->id, $group->phase_id, $group->id);

                }

            }
        } else {
            $num=1;
            // Ciclo para crear los grupos
            for ($i=0; $i < $tournament->groups; $i++) {

                $group=$this->tournamentGroups($tournament->id, $tournament->groups, $tournament->groups);

                // Ciclo para crear las parejas y agregarlas al grupo
                $couplesAssignmentsCount=Assignment::where('tournament_id', $tournament->id)->where('group', $num)->count();
                $couplesAssignments=Assignment::where('tournament_id', $tournament->id)->where('group', $num)->get();

                for ($j=0; $j < $couplesAssignmentsCount; $j++) {
                    $couple=$couplesAssignments[$j]->couple_id;
                    $coupleGroup=CoupleGroup::create(['couple_id' => $couple, 'group_id' => $group->id]);
                }

                // Se crean los juegos de la primera vuelta
                $couples=CoupleGroup::where('group_id', $group->id)->get();
                // Se cuenta cuantas parejas hay en el grupo para saber cuantos juegos seran en la primera ronda
                if ($couplesAssignmentsCount==2) {
                    $slugGame=$this->gameSlug();
                    $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                    CoupleGame::create(['couple_id' => $couples[0]->couple_id, 'game_id' => $game->id])->save();
                    CoupleGame::create(['couple_id' => $couples[1]->couple_id, 'game_id' => $game->id])->save();
                } elseif ($couplesAssignmentsCount==3) {
                    $slugGame=$this->gameSlug();
                    $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                    CoupleGame::create(['couple_id' => $couples[0]->couple_id, 'game_id' => $game->id])->save();
                    CoupleGame::create(['couple_id' => $couples[1]->couple_id, 'game_id' => $game->id])->save();

                    $slugGame=$this->gameSlug();
                    $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                    CoupleGame::create(['couple_id' => $couples[1]->couple_id, 'game_id' => $game->id])->save();
                    CoupleGame::create(['couple_id' => $couples[2]->couple_id, 'game_id' => $game->id])->save();

                    $slugGame=$this->gameSlug();
                    $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                    CoupleGame::create(['couple_id' => $couples[2]->couple_id, 'game_id' => $game->id])->save();
                    CoupleGame::create(['couple_id' => $couples[0]->couple_id, 'game_id' => $game->id])->save();
                } else {

                    // Primera ronda
                    if ($couplesAssignmentsCount==6) {
                        $max=5;
                    } else {
                        $max=3;
                    }
                    for ($j=0; $j < $max; $j+=2) {
                        $slugGame=$this->gameSlug();
                        $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                        CoupleGame::create(['couple_id' => $couples[$j]->couple_id, 'game_id' => $game->id])->save();
                        CoupleGame::create(['couple_id' => $couples[$j+1]->couple_id, 'game_id' => $game->id])->save();
                    }

                    // Juegos de las demas rondas
                    $this->allGamesTournament($tournament->id, $group->phase_id, $group->id);

                }
                $num++;
            }
        }

        $tournament->fill(['state' => 2])->save();

        return redirect()->back()->with(['type' => 'success', 'title' => 'Torneo iniciado', 'msg' => 'El torneo ha sido iniciado exitosamente.']);
    }

    public function tournamentGroups($id, $groups, $initialGroups, $phase=null)
    {
        $count=Group::where('tournament_id', $id)->count();
        $name="Grupo ".($count+1);
        $slug=Str::slug($name, '-');
        if ($phase==null && ($groups>2 || $initialGroups==2)) {
            $data=array('name' => $name, 'slug' => $slug, 'tournament_id' => $id, 'phase_id' => 1);
        } elseif ($phase==1 || $groups==2) {
            $data=array('name' => $name, 'slug' => $slug, 'tournament_id' => $id, 'phase_id' => 2);
        } else {
            $data=array('name' => $name, 'slug' => $slug, 'tournament_id' => $id, 'phase_id' => 3);
        }
        $group=Group::create($data);

        return $group;
    }

    public function tournamentStart($num, $tournament, $group, $couples)
    {
        // Ciclo para crear las parejas y agregarlas al grupo
        $couplesTournament=GamerTournament::where('tournament_id', $tournament)->groupBy('couple_id')->get();
        for ($j=0; $j < $couples; $j++) {
            $couple=$couplesTournament[$num]->couple_id;
            $coupleGroup=CoupleGroup::create(['couple_id' => $couple, 'group_id' => $group]);
            $num++;
        }

        return $num;
    }

    public function allGamesTournament($tournament, $phase, $groups)
    {
        // Obtengo a las parejas del grupo
        $couples=Couple::join('couple_group', 'couples.id', '=', 'couple_group.couple_id')->where('couple_group.group_id', $groups);
        $couplesCount=$couples->count();
        $couples=$couples->get();

        $couplesArray=array();
        for ($i=0; $i < $couplesCount; $i++) { 
            $couplesArray=Arr::add($couplesArray, $i, $couples[$i]->couple_id);
        }

        if ($couplesCount==4) {
            $couplesCount=8;
        } elseif ($couplesCount==5) {
            $couplesCount=16;
        } else {
            $couplesCount=24;
        }

        // Empieza el emparejamiento
        for ($i=0; $i < $couplesCount; $i+=2) {

            // Parejas
            $couplesArrayVs=$couplesArray;

            // Obtengo a la ronda del grupo
            $rond=Game::where('group_id', $groups)->groupBy('rond')->get();
            if ($i==0) {
                $rond=count($rond)+1;
            } elseif (($coupleCount==8 || $coupleCount==16) && ($i==4 || $i==8 || $i==12)) {
                $rond=count($rond)+1;
            } elseif ($coupleCount==24 && ($i==6 || $i==12 || $i==18)) {
                $rond=count($rond)+1;
            }

            $coupleCount=count($couplesArrayVs);
            for ($j=0; $j < $coupleCount; $j++) { 
                // Obtengo a la cantidad de juegos de la pareja
                $coupleGamesTotalCount=CoupleGame::join('games', 'couple_game.game_id', '=', 'games.id')->where('games.group_id', $groups)->where('couple_game.couple_id', $couplesArrayVs[$j])->count();

                if ($coupleGamesTotalCount>=$coupleCount-1) {
                    // Quito a las parejas que ya jugaron todos sus juegos del arreglo de parejas para el emparejamiento
                    $key=array_search($couplesArrayVs[$j], $couplesArrayVs);
                    unset($couplesArrayVs[$key]);
                }
            }

            // Obtengo a las parejas del grupo que ya tienen juegos en esta ronda
            $coupleGames=CoupleGame::join('games', 'couple_game.game_id', '=', 'games.id')->where('games.group_id', $groups)->where('games.rond', $rond)->get();
            $coupleGamesCount=$coupleGames->count();

            $coupleGame=array();
            for ($j=0; $j < $coupleGamesCount; $j++) { 
                $coupleGame=Arr::add($coupleGame, $j, $coupleGames[$j]->couple_id);
            }

            // Quito a las parejas con juegos del arreglo de parejas para el emparejamiento
            foreach ($coupleGame as $coupleGam) {
                $key=array_search($coupleGam, $couplesArrayVs);
                unset($couplesArrayVs[$key]);
            }

            // Selecciono a la primera pareja de juego
            $coupleSelected=current($couplesArrayVs);
            $coupleSelectedKey=array_search($coupleSelected, $couplesArrayVs);
            unset($couplesArrayVs[$coupleSelectedKey]);

            // Obtengo a las parejas del grupo que ya han jugado contra la primera pareja seleccionada
            $coupleSelectedGames=Game::join('couple_game', 'couple_game.game_id', '=', 'games.id')->where('games.group_id', $groups)->where('couple_game.couple_id', $coupleSelected)->get();
            $coupleSelectedGamesCount=$coupleSelectedGames->count();

            for ($j=0; $j < $coupleSelectedGamesCount; $j++) {
                $gameCouples=CoupleGame::where('game_id', $coupleSelectedGames[$j]->game_id);
                $gameCouplesCount=$gameCouples->count();
                $gameCouples=$gameCouples->get();

                // Quito a las parejas con quienes ha jugado del arreglo de parejas para el emparejamiento
                for ($k=0; $k < $gameCouplesCount; $k++) { 
                    if ($gameCouples[$k]->couple_id!=$coupleSelected) {
                        $key=array_search($gameCouples[$k]->couple_id, $couplesArrayVs);
                        unset($couplesArrayVs[$key]);
                    }
                }  
            }

            if (count($couplesArrayVs)>0) {
                // Selecciono a la segunda pareja de juego
                $CoupleVs=Arr::first($couplesArrayVs, function ($value, $key) {
                    return $value > 0;
                });

                // Registro el juego
                $slugGame=$this->gameSlug();
                $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $groups, 'rond' => $rond]);
                CoupleGame::create(['couple_id' => $coupleSelected, 'game_id' => $game->id])->save();
                CoupleGame::create(['couple_id' => $CoupleVs, 'game_id' => $game->id])->save();
            } else {
                // Parejas
                $newCouplesArrayVs=$couplesArray;

                $coupleSelectedKey=array_search($coupleSelected, $newCouplesArrayVs);
                unset($newCouplesArrayVs[$coupleSelectedKey]);

                for ($j=0; $j < $coupleSelectedGamesCount; $j++) {
                    $gameCouples=CoupleGame::where('game_id', $coupleSelectedGames[$j]->game_id);
                    $gameCouplesCount=$gameCouples->count();
                    $gameCouples=$gameCouples->get();

                    // Quito a las parejas con quienes ha jugado del arreglo de parejas para el emparejamiento
                    for ($k=0; $k < $gameCouplesCount; $k++) { 
                        if ($gameCouples[$k]->couple_id!=$coupleSelected) {
                            $key=array_search($gameCouples[$k]->couple_id, $newCouplesArrayVs);
                            unset($newCouplesArrayVs[$key]);
                        }
                    }
                }

                // Selecciono a la segunda pareja de juego
                $CoupleVs=Arr::first($newCouplesArrayVs, function ($value, $key) {
                    return $value >= 0;
                });

                // Registro el juego
                $slugGame=$this->gameSlug();
                $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $groups, 'rond' => $rond]);
                CoupleGame::create(['couple_id' => $coupleSelected, 'game_id' => $game->id])->save();
                CoupleGame::create(['couple_id' => $CoupleVs, 'game_id' => $game->id])->save();
            }
        }
    }

    public function phaseGroups($slug)
    {
        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        $groups=Group::where('tournament_id', $tournament->id)->where('phase_id', 1)->get();
        $phase=Phase::where('id', 1)->first();

        $currentPhase=Group::select('phase_id')->where('tournament_id', $tournament->id)->orderBy('id', 'DESC')->first();

        $groupsCount=Group::where('tournament_id', $tournament->id)->where('phase_id', 1)->count();
        $groupsPhase=Group::where('tournament_id', $tournament->id)->groupBy('phase_id')->count();
        $gamesEnd=0;
        $gamesTotal=0;
        foreach ($groups as $groupPhase) {
            $gamesEnd+=Game::where('state', 3)->where('group_id', $groupPhase->id)->count();
            $gamesTotal+=Game::where('group_id', $groupPhase->id)->count();
        }
        $gamesFinish=$gamesTotal-$gamesEnd;

        $num=1;

        return view('admin.tournaments.groups', compact("tournament", "groups", "phase", "gamesFinish", "groupsCount", "groupsPhase", "currentPhase", "num"));
    }

    public function semifinal($slug)
    {
        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        $groups=Group::where('tournament_id', $tournament->id)->where('phase_id', 2)->get();
        $phase=Phase::where('id', 2)->first();

        $currentPhase=Group::select('phase_id')->where('tournament_id', $tournament->id)->orderBy('id', 'DESC')->first();

        $groupsCount=Group::where('tournament_id', $tournament->id)->where('phase_id', 2)->count();
        $groupsPhase=Group::where('tournament_id', $tournament->id)->groupBy('phase_id')->count();

        $groupsPhases=Group::groupBy('phase_id')->count();
        $gamesEnd=0;
        $gamesTotal=0;
        foreach ($groups as $groupPhase) {
            $gamesEnd+=Game::where('state', 3)->where('group_id', $groupPhase->id)->count();
            $gamesTotal+=Game::where('group_id', $groupPhase->id)->count();
        }
        $gamesFinish=$gamesTotal-$gamesEnd;

        $num=1;

        return view('admin.tournaments.groups', compact("tournament", "groups", "phase", "gamesFinish", "groupsCount", "groupsPhase", "currentPhase", "num"));
    }

    public function finale($slug)
    {
        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        $groups=Group::where('tournament_id', $tournament->id)->where('phase_id', 3)->get();
        $phase=Phase::where('id', 3)->first();

        $groupsCount=Group::where('tournament_id', $tournament->id)->where('phase_id', 3)->count();
        $groupsPhase=Group::where('tournament_id', $tournament->id)->groupBy('phase_id')->count();
        $gamesEnd=0;
        $gamesTotal=0;
        foreach ($groups as $groupPhase) {
            $gamesEnd+=Game::where('state', 3)->where('group_id', $groupPhase->id)->count();
            $gamesTotal+=Game::where('group_id', $groupPhase->id)->count();
        }
        $gamesFinish=$gamesTotal-$gamesEnd;

        $num=1;

        return view('admin.tournaments.groups', compact("tournament", "groups", "phase", "gamesFinish", "groupsCount", "groupsPhase", "num"));
    }

    public function group($slug, $phase, $group)
    {
        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        $phase=Phase::where('slug', $phase)->firstOrFail();
        $groups=Group::where('slug', $group)->where('tournament_id', $tournament->id)->firstOrFail();

        $lastPhase=Group::where('tournament_id', $tournament->id)->orderBy('id', 'DESC')->firstOrFail();

        $data=Group::join('games', 'games.group_id', '=', 'groups.id')->where('groups.tournament_id', $tournament->id)->where('groups.phase_id', $phase->id)->where('groups.slug', $group)->get();

        $count=0;
        $games=[];
        foreach ($data as $game) {
            $game=Game::where('id', $game->id)->get();
            $games[$count]=$game[0];
            $count++;
        }
        $num=1;

        $gamesFinish=Game::where('group_id', $groups->id)->where('state', 2)->orWhere('games.state', '=', 1)->count();

        return view('admin.tournaments.group', compact("tournament", "groups", "phase", "games", "num", "gamesFinish", "lastPhase"));
    }

    public function table($slug, $phase, $group)
    {
        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        $phase=Phase::where('slug', $phase)->firstOrFail();
        $groups=Group::where('slug', $group)->where('tournament_id', $tournament->id)->firstOrFail();

        $lastPhase=Group::where('tournament_id', $tournament->id)->orderBy('id', 'DESC')->firstOrFail();

        $data=Group::join('games', 'games.group_id', '=', 'groups.id')->where('groups.tournament_id', $tournament->id)->where('groups.phase_id', $phase->id)->where('groups.slug', $group)->get();

        $count=0;
        $games=[];
        foreach ($data as $game) {
            $game=Game::where('id', $game->id)->get();
            $games[$count]=$game[0];
            $count++;
        }

        $groupCouples=$groups->couples;

        $count2=0;
        // Obteniendo las puntuaciones de cada parejas
        foreach ($groupCouples as $groupCouple) {

            $points=CoupleGame::join('games', 'couple_game.game_id', '=', 'games.id')->where('couple_game.couple_id', $groupCouple->pivot->couple_id)->where('games.group_id', $groups->id)->groupBy('couple_game.couple_id')->sum('couple_game.points');

            $wins=CoupleGame::join('games', 'couple_game.game_id', '=', 'games.id')->where('couple_game.couple_id', $groupCouple->pivot->couple_id)->where('games.group_id', $groups->id)->where('couple_game.points', 2)->count();
            $wins2=CoupleGame::join('games', 'couple_game.game_id', '=', 'games.id')->join('game_winner', 'games.id', '=', 'game_winner.game_id')->where('couple_game.couple_id', $groupCouple->pivot->couple_id)->where('games.group_id', $groups->id)->where('couple_game.points', 0)->where('game_winner.couple_id', $groupCouple->pivot->couple_id)->count();
            $wins=$wins+$wins2;

            $gamesCouple=Game::join('couple_game', 'couple_game.game_id', '=', 'games.id')->where('couple_game.couple_id', $groupCouple->pivot->couple_id)->where('games.group_id', $groups->id)->get();

            $counterPoints=0;
            foreach ($gamesCouple as $gameCouple) {
                $gameData=Game::join('couple_game', 'couple_game.game_id', '=', 'games.id')->where('games.id', $gameCouple->game_id)->where('couple_game.couple_id', '!=', $groupCouple->pivot->couple_id)->firstOrFail();
                $counterPoints+=$gameData->points;
            }

            $total=$points-$counterPoints;
            $couplesPoints[$count2]=array('couple_id' => $groupCouple->pivot->couple_id, 'points' => $points, 'wins' => $wins, 'counter' => $counterPoints, 'total' => $total);
            $count2++;
        }

        usort($couplesPoints, function($a, $b) {
            return $a['total'] - $b['total'];
        });

        usort($couplesPoints, function($a, $b) {
            return $a['wins'] - $b['wins'];
        });
        $couplesPointsOrder=array_reverse($couplesPoints);

        $num=1;

        return view('admin.tournaments.table', compact("tournament", "groups", "phase", "num", "games", "couplesPointsOrder", "lastPhase"));
    }

    public function game($slug)
    {
        $game=Game::where('slug', $slug)->firstOrFail();
        $couple1=$game->couples[0]->gamers[0]->name." ".$game->couples[0]->gamers[0]->lastname."<br>".$game->couples[0]->gamers[1]->name." ".$game->couples[0]->gamers[1]->lastname;
        $couple2=$game->couples[1]->gamers[0]->name." ".$game->couples[1]->gamers[0]->lastname."<br>".$game->couples[1]->gamers[1]->name." ".$game->couples[1]->gamers[1]->lastname;
        echo json_encode([
            'couple1' => $couple1,
            'couple2' => $couple2,
            'points1' => $game->couple_game[0]->points,
            'points2' => $game->couple_game[1]->points
        ]);
    }

    public function gameStore($slug, Request $request)
    {
        $game=Game::where('slug', $slug)->firstOrFail();
        $coupleGame1=CoupleGame::where('id', $game->couple_game[0]->id)->first();
        $coupleGame2=CoupleGame::where('id', $game->couple_game[1]->id)->first();
        $coupleGame1->fill(['points' => request('points1')])->save();
        $coupleGame2->fill(['points' => request('points2')])->save();

        if (request('points1')==2 || request('points2')==2) {
            if ($game->game_winner!=null) {
                $winnerGame=GameWinner::where('game_id', $game->id)->firstOrFail();
                if (request('points1')==2) {
                    $winnerGame->fill(['couple_id' => $game->couple_game[0]->couple_id])->save();
                } else {
                    $winnerGame->fill(['couple_id' => $game->couple_game[1]->couple_id])->save();
                }
            } else {
                $winner=Winner::create(['type' => 2]);
                if (request('points1')==2) {
                    GameWinner::create(['game_id' => $game->id, 'couple_id' => $game->couple_game[0]->couple_id, 'winner_id' => $winner->id]);
                } else {
                    GameWinner::create(['game_id' => $game->id, 'couple_id' => $game->couple_game[1]->couple_id, 'winner_id' => $winner->id]);
                }
            }
            $game->fill(['state' => 3])->save();
        } else {
            if ($game->game_winner!=null) {
                $winnerGame=GameWinner::where('game_id', $game->id)->firstOrFail();
                $winnerGame->delete();
            }
            $game->fill(['state' => 2])->save();
        }

        if ($coupleGame1 && $coupleGame2) {
            return redirect()->back()->with(['type' => 'success', 'title' => 'Juego registrado', 'msg' => 'El juego del torneo ha sido registrado exitosamente.']);
        } else {
            return redirect()->back()->with(['type' => 'error', 'title' => 'Registro fallido', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }

    public function gameBack($slug)
    {
        $game=Game::where('slug', $slug)->firstOrFail();
        $couple1=couplesNames($game->couples[0]->id, 4, 1, 1);
        $couple2=couplesNames($game->couples[1]->id, 4, 1, 1);
        $idCouple1=$game->couples[0]->id;
        $idCouple2=$game->couples[1]->id;
        echo json_encode([
            'couple1' => $couple1,
            'couple2' => $couple2,
            'idCouple1' => $idCouple1,
            'idCouple2' => $idCouple2
        ]);
    }

    public function gameBackStore($slug, Request $request)
    {
        $game=Game::where('slug', $slug)->firstOrFail();
        $coupleGame1=CoupleGame::where('id', $game->couple_game[0]->id)->first();
        $coupleGame2=CoupleGame::where('id', $game->couple_game[1]->id)->first();
        $coupleGame1->fill(['points' => 0])->save();
        $coupleGame2->fill(['points' => 0])->save();
        $winner=Winner::create(['type' => 2]);

        if (request('winnerCouple')==$game->couple_game[0]->couple_id) {
            GameWinner::create(['game_id' => $game->id, 'couple_id' => $game->couple_game[0]->couple_id, 'winner_id' => $winner->id]);
        } elseif (request('winnerCouple')==$game->couple_game[1]->couple_id) {
            GameWinner::create(['game_id' => $game->id, 'couple_id' => $game->couple_game[1]->couple_id, 'winner_id' => $winner->id]);
        } else {
            return redirect()->back()->with(['type' => 'error', 'title' => 'Registro fallido', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
        $game->fill(['state' => 3])->save();

        if ($coupleGame1 && $coupleGame2) {
            return redirect()->back()->with(['type' => 'success', 'title' => 'Juego registrado', 'msg' => 'El juego del torneo ha sido registrado exitosamente.']);
        } else {
            return redirect()->back()->with(['type' => 'error', 'title' => 'Registro fallido', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }

    public function nextPhase($slug, Request $request)
    {
        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        $phase=Phase::where('slug', request('phase'))->firstOrFail();
        $groups=Group::where('phase_id', $phase->id)->where('tournament_id', $tournament->id)->get();
        $countGroups=Group::where('phase_id', $phase->id)->where('tournament_id', $tournament->id)->count();

        $groupsCount=1;
        $count=0;
        // Obteniendo las parejas por grupos
        foreach ($groups as $group) {
            $groupCouples=$group->couples;

            $count2=0;
            // Obteniendo las puntuaciones de cada parejas
            foreach ($groupCouples as $groupCouple) {
                $points=CoupleGame::join('games', 'couple_game.game_id', '=', 'games.id')->where('couple_game.couple_id', $groupCouple->pivot->couple_id)->where('games.group_id', $group->id)->groupBy('couple_game.couple_id')->sum('couple_game.points');
                $gamesCouple=Game::join('couple_game', 'couple_game.game_id', '=', 'games.id')->where('couple_game.couple_id', $groupCouple->pivot->couple_id)->where('games.group_id', $group->id)->get();

                $counterPoints=0;
                foreach ($gamesCouple as $gameCouple) {
                    $gameData=Game::join('couple_game', 'couple_game.game_id', '=', 'games.id')->where('games.id', $gameCouple->game_id)->where('couple_game.couple_id', '!=', $groupCouple->pivot->couple_id)->firstOrFail();
                    $counterPoints+=$gameData->points;
                }

                $wins=CoupleGame::join('games', 'couple_game.game_id', '=', 'games.id')->where('couple_game.couple_id', $groupCouple->pivot->couple_id)->where('games.group_id', $group->id)->where('couple_game.points', 2)->count();
                $wins2=CoupleGame::join('games', 'couple_game.game_id', '=', 'games.id')->join('game_winner', 'games.id', '=', 'game_winner.game_id')->where('couple_game.couple_id', $groupCouple->pivot->couple_id)->where('games.group_id', $group->id)->where('couple_game.points', 0)->where('game_winner.couple_id', $groupCouple->pivot->couple_id)->count();
                $wins=$wins+$wins2;

                $total=$points-$counterPoints;
                $couplesPoints[$count2]=array('couple_id' => $groupCouple->pivot->couple_id, 'total' => $total, 'wins' => $wins);
                $count2++;
            }

            usort($couplesPoints, function($a, $b) {
                return $a['total'] - $b['total'];
            });

            usort($couplesPoints, function($a, $b) {
                return $a['wins'] - $b['wins'];
            });

            $couplesPointsOrder=array_reverse($couplesPoints);
            $couplesPointsOrder[0]['position']=1;
            $couplesPointsOrder[0]['group']=$groupsCount;
            if ($phase->id!=2 || ($phase->id==2 && $countGroups==1)) {
                $couplesPointsOrder[1]['position']=2;
                $couplesPointsOrder[1]['group']=$groupsCount;
            }

            $couplesWinners[$count]=$couplesPointsOrder[0];
            if ($phase->id!=2 || ($phase->id==2 && $countGroups==1)) {
                $couplesWinners[$count+1]=$couplesPointsOrder[1];
            }

            if ($phase->id!=2 || ($phase->id==2 && $countGroups==1)) {
                $count+=2;
            } else {
                $count++;
            }
            $groupsCount++;
        }

        $num=0;
        if (count($couplesWinners)>5) {
            $groupsNewPhase=2;
        } else {
            $groupsNewPhase=1;
        }
        // Ciclo para crear los grupos
        for ($i=0; $i < $groupsNewPhase; $i++) {

            $group=$this->tournamentGroups($tournament->id, $groupsNewPhase, $tournament->groups, $phase->id);

            // Se crean los juegos de la primera vuelta
            if ($groupsNewPhase==2) {
                if ($num==count($couplesWinners)/$groupsNewPhase) {
                    $secuenceCount=1;
                    for ($j=0; $j < count($couplesWinners)/$groupsNewPhase; $j++) { 
                        if ($j==0) {
                            $couples[$j]=$couplesWinners[$secuenceCount];
                            $secuenceCount+=1;
                        } elseif ($j%2==0) {
                            $couples[$j]=$couplesWinners[$secuenceCount];
                            $secuenceCount+=1;
                        } else {
                            $couples[$j]=$couplesWinners[$secuenceCount];
                            $secuenceCount+=3;
                        }
                    }
                } else {
                    $secuenceCount=0;
                    for ($j=0; $j < count($couplesWinners)/$groupsNewPhase; $j++) { 
                        if ($j==0) {
                            $couples[$j]=$couplesWinners[$secuenceCount];
                            $secuenceCount+=3;
                        } elseif ($j%2==0) {
                            $couples[$j]=$couplesWinners[$secuenceCount];
                            $secuenceCount+=3;
                        } else {
                            $couples[$j]=$couplesWinners[$secuenceCount];
                            $secuenceCount+=1;
                        }
                    }
                }
            } else {
                $couples=$couplesWinners;
            }

            // Ciclo para crear las parejas y agregarlas al grupo
            for ($j=0; $j < count($couplesWinners)/$groupsNewPhase; $j++) {
                CoupleGroup::create(['couple_id' => $couples[$j]['couple_id'], 'group_id' => $group->id]);
                $num++;
            }

            // Se cuenta cuantas parejas hay en el grupo para saber cuantos juegos seran en la primera ronda
            if (count($couplesWinners)/$groupsNewPhase==2) {
                $slugGame=$this->gameSlug();
                $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                CoupleGame::create(['couple_id' => $couples[0]['couple_id'], 'game_id' => $game->id])->save();
                CoupleGame::create(['couple_id' => $couples[1]['couple_id'], 'game_id' => $game->id])->save();
            } elseif (count($couplesWinners)/$groupsNewPhase==3) {
                $slugGame=$this->gameSlug();
                $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                CoupleGame::create(['couple_id' => $couples[0]['couple_id'], 'game_id' => $game->id])->save();
                CoupleGame::create(['couple_id' => $couples[1]['couple_id'], 'game_id' => $game->id])->save();

                $slugGame=$this->gameSlug();
                $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                CoupleGame::create(['couple_id' => $couples[1]['couple_id'], 'game_id' => $game->id])->save();
                CoupleGame::create(['couple_id' => $couples[2]['couple_id'], 'game_id' => $game->id])->save();

                $slugGame=$this->gameSlug();
                $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);
                CoupleGame::create(['couple_id' => $couples[2]['couple_id'], 'game_id' => $game->id])->save();
                CoupleGame::create(['couple_id' => $couples[0]['couple_id'], 'game_id' => $game->id])->save();
            } else {

                if (count($couplesWinners)/$groupsNewPhase==6) {
                    $max=5;
                } else {
                    $max=3;
                }
                for ($j=0; $j < $max; $j+=2) {
                    $slugGame=$this->gameSlug();
                    $game=Game::create(['slug' => $slugGame, 'type' => 2, 'state' => 1, 'group_id' => $group->id, 'rond' => 1]);

                    if ($groupsNewPhase==1) {
                        CoupleGame::create(['couple_id' => $couples[$j]['couple_id'], 'game_id' => $game->id])->save();
                        CoupleGame::create(['couple_id' => $couples[$j+1]['couple_id'], 'game_id' => $game->id])->save();
                    } else {
                        if ($num==count($couplesWinners)) {
                            CoupleGame::create(['couple_id' => $couples[$j]['couple_id'], 'game_id' => $game->id])->save();
                            CoupleGame::create(['couple_id' => $couples[$j+1]['couple_id'], 'game_id' => $game->id])->save();
                        } else {
                            CoupleGame::create(['couple_id' => $couples[$j]['couple_id'], 'game_id' => $game->id])->save();
                            CoupleGame::create(['couple_id' => $couples[$j+1]['couple_id'], 'game_id' => $game->id])->save();
                        }
                    }
                }

                // Juegos de las demas rondas
                $this->allGamesTournament($tournament->id, $group->phase_id, $group->id);
            }

        }

        return redirect()->back()->with(['type' => 'success', 'title' => 'Nueva fase', 'msg' => 'La nueva fase del torneo a sido iniciada exitosamente.']);
    }

    public function finalTournament($slug)
    {
        $tournament=Tournament::where('slug', $slug)->firstOrFail();
        $group=Group::where('phase_id', 3)->where('tournament_id', $tournament->id)->firstOrFail();

        $groupCouples=$group->couples;

        $count=0;
        foreach ($groupCouples as $groupCouple) {
            $points=CoupleGame::join('games', 'couple_game.game_id', '=', 'games.id')->where('couple_game.couple_id', $groupCouple->id)->where('games.group_id', $group->id)->groupBy('couple_game.couple_id')->sum('couple_game.points');
            $gamesCouple=Game::join('couple_game', 'couple_game.game_id', '=', 'games.id')->where('couple_game.couple_id', $groupCouple->id)->where('games.group_id', $group->id)->get();

            $counterPoints=0;
            foreach ($gamesCouple as $gameCouple) {
                $gameData=Game::join('couple_game', 'couple_game.game_id', '=', 'games.id')->where('games.id', $gameCouple->game_id)->where('couple_game.couple_id', '!=', $groupCouple->id)->firstOrFail();
                $counterPoints+=$gameData->points;
            }

            $total=$points-$counterPoints;
            $couplesPoints[$count]=array('couple_id' => $groupCouple->id, 'total' => $total);
            $count++;
        }

        usort($couplesPoints, function($a, $b) {
            return $a['total'] - $b['total'];
        });
        $couplesPointsOrder=array_reverse($couplesPoints);

        $couplesWinners[0]=$couplesPointsOrder[0];
        $couplesWinners[1]=$couplesPointsOrder[1];

        $firstPlaseWinner=Winner::create(['type' => 2, 'position' => 1]);
        $secondPlaseWinner=Winner::create(['type' => 2, 'position' => 2]);

        TournamentWinner::create(['couple_id' => $couplesWinners[0]['couple_id'], 'tournament_id' => $tournament->id, 'winner_id' => $firstPlaseWinner->id]);
        TournamentWinner::create(['couple_id' => $couplesWinners[1]['couple_id'], 'tournament_id' => $tournament->id, 'winner_id' => $secondPlaseWinner->id]);

        $tournament->fill(['state' => 3, 'end' => now()])->save();

        return redirect()->route('torneos.show', ['slug' => $slug])->with(['type' => 'success', 'title' => 'Torneo finalizado', 'msg' => 'El torneo ha finalizado exitosamente.']);
    }

    public function gameSlug()
    {
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

        return $slug;
    }

    public function addGroup(Request $request)
    {
        $count=GamerTournament::where('id', request('gamerTournament'))->count();
        $countTournament=Tournament::where('slug', request('tournament'))->count();
        if ($count>0 && $countTournament>0) {
            $gamerTournament=GamerTournament::where('id', request('gamerTournament'))->first();
            $tournament=Tournament::where('slug', request('tournament'))->first();
            $data=array();

            for ($i=0; $i < $tournament->groups; $i++) {

                // Cantidad de parejas para este grupo
                $round=intval(round($tournament->couples/$tournament->groups, 0, PHP_ROUND_HALF_UP));
                $currentCouplesTotal=$round*$i+$round;
                $couplesMissing=$tournament->couples-$currentCouplesTotal;

                if ($tournament->groups<6) {
                    if ($i==$tournament->groups-2) {
                        if ($couplesMissing==$round || $couplesMissing==$round+1) {
                            $numberCouplesGroup=$round;
                        } elseif ($couplesMissing==$round-1 || $couplesMissing==$round-2) {
                            $numberCouplesGroup=$round-1;
                        } elseif ($couplesMissing==$round+2) {
                            $numberCouplesGroup=$round+1;
                        }
                    } elseif ($i==$tournament->groups-1) {
                        if ($couplesMissing==0) {
                            $numberCouplesGroup=$round;
                        } else {
                            $numberCouplesGroup=$tournament->couples-$numCouples;
                        }
                    } else {
                        $numberCouplesGroup=$round;
                    }
                } else {
                    if ($i==$tournament->groups-3) {
                        if ($couplesMissing==$round*2 || $couplesMissing==$round*2+1 || $couplesMissing==$round*2+2 || $couplesMissing==$round*2-2 || $couplesMissing==$round*2-1) {
                            $numberCouplesGroup=$round;
                        } elseif ($couplesMissing==($round+1)*2+1) {
                            $numberCouplesGroup=$round+1;
                        }
                    } elseif ($i==$tournament->groups-2) {
                        if ($couplesMissing==$round || $couplesMissing==$round+1) {
                            $numberCouplesGroup=$round;
                        } elseif ($couplesMissing==$round-1 || $couplesMissing==$round-2) {
                            $numberCouplesGroup=$round-1;
                        } elseif ($couplesMissing==$round+2 || $couplesMissing==$round+3) {
                            $numberCouplesGroup=$round+1;
                        }
                    } elseif ($i==$tournament->groups-1) {
                        if ($couplesMissing==0) {
                            $numberCouplesGroup=$round;
                        } else {
                            $numberCouplesGroup=$tournament->couples-$numCouples;
                        }
                    } else {
                        $numberCouplesGroup=$round;
                    }
                }

                $assignments=Assignment::where('tournament_id', $tournament->id)->where('group', $i+1)->count();

                if ($numberCouplesGroup-$assignments>0) {
                    $group=$i+1;
                    $data[$i]=array('name' => 'Grupo '.$group, 'group' => $group);
                }
            }

            if (count($data)==0) {
                $data=array('status' => false, 'msj' => 'Todos los grupos estan llenos');
            }
        } else {
            $data=array('status' => false, 'msj' => 'Error');
        }

        echo json_encode($data);
    }

    public function addGroupStore(Request $request)
    {
        $tournament=Tournament::where('slug', request('tournament'))->firstOrFail();
        $gamerTournament=GamerTournament::where('id', request('gamerTournament'))->firstOrFail();

        $assignment=Assignment::create(['group' => request('group'), 'couple_id' => $gamerTournament->couple_id, 'tournament_id' => $tournament->id])->save();

        if ($assignment) {
            return redirect()->route('torneos.list.gamers', ['slug' => $tournament->slug])->with(['type' => 'success', 'title' => 'Registro exitoso', 'msg' => 'La pareja ha sido agregada al grupo exitosamente.']);
        } else {
            return redirect()->route('torneos.list.gamers', ['slug' => $tournament->slug])->with(['type' => 'error', 'title' => 'Registro fallido', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }

    public function changeGroup(Request $request)
    {
        $count=GamerTournament::where('id', request('gamerTournament'))->count();
        $countTournament=Tournament::where('slug', request('tournament'))->count();
        if ($count>0 && $countTournament>0) {
            $gamerTournament=GamerTournament::where('id', request('gamerTournament'))->first();
            $tournament=Tournament::where('slug', request('tournament'))->first();
            $data=array();
            $num=0;

            for ($i=0; $i < $tournament->groups; $i++) {

                // Cantidad de parejas para este grupo
                $round=intval(round($tournament->couples/$tournament->groups, 0, PHP_ROUND_HALF_UP));
                $currentCouplesTotal=$round*$i+$round;
                $couplesMissing=$tournament->couples-$currentCouplesTotal;

                if ($tournament->groups<6) {
                    if ($i==$tournament->groups-2) {
                        if ($couplesMissing==$round || $couplesMissing==$round+1) {
                            $numberCouplesGroup=$round;
                        } elseif ($couplesMissing==$round-1 || $couplesMissing==$round-2) {
                            $numberCouplesGroup=$round-1;
                        } elseif ($couplesMissing==$round+2) {
                            $numberCouplesGroup=$round+1;
                        }
                    } elseif ($i==$tournament->groups-1) {
                        if ($couplesMissing==0) {
                            $numberCouplesGroup=$round;
                        } else {
                            $numberCouplesGroup=$tournament->couples-$numCouples;
                        }
                    } else {
                        $numberCouplesGroup=$round;
                    }
                } else {
                    if ($i==$tournament->groups-3) {
                        if ($couplesMissing==$round*2 || $couplesMissing==$round*2+1 || $couplesMissing==$round*2+2 || $couplesMissing==$round*2-2 || $couplesMissing==$round*2-1) {
                            $numberCouplesGroup=$round;
                        } elseif ($couplesMissing==($round+1)*2+1) {
                            $numberCouplesGroup=$round+1;
                        }
                    } elseif ($i==$tournament->groups-2) {
                        if ($couplesMissing==$round || $couplesMissing==$round+1) {
                            $numberCouplesGroup=$round;
                        } elseif ($couplesMissing==$round-1 || $couplesMissing==$round-2) {
                            $numberCouplesGroup=$round-1;
                        } elseif ($couplesMissing==$round+2 || $couplesMissing==$round+3) {
                            $numberCouplesGroup=$round+1;
                        }
                    } elseif ($i==$tournament->groups-1) {
                        if ($couplesMissing==0) {
                            $numberCouplesGroup=$round;
                        } else {
                            $numberCouplesGroup=$tournament->couples-$numCouples;
                        }
                    } else {
                        $numberCouplesGroup=$round;
                    }
                }

                $assignments=Assignment::where('tournament_id', $tournament->id)->where('group', $i+1)->count();
                $assignmentCoupleCount=Assignment::where('tournament_id', $tournament->id)->where('couple_id', $gamerTournament->couple_id)->count();
                $group=$i+1;

                if ($numberCouplesGroup-$assignments>0) {
                    $data[$num]=array('name' => 'Grupo '.$group, 'group' => $group);
                }

                if ($assignmentCoupleCount>0) {
                    $assignmentCouple=Assignment::where('tournament_id', $tournament->id)->where('couple_id', $gamerTournament->couple_id)->firstOrFail();

                    if ($group==$assignmentCouple->group) {
                        unset($data[$num]);
                    } else {
                        $num++;
                    }
                }
            }

            if (count($data)==0) {
                $data=array('status' => false, 'msj' => 'El resto de grupos estan llenos');
            }
        } else {
            $data=array('status' => false, 'msj' => 'Error');
        }

        echo json_encode($data);
    }

    public function changeGroupUpdate(Request $request)
    {
        $tournament=Tournament::where('slug', request('tournament'))->firstOrFail();
        $gamerTournament=GamerTournament::where('id', request('gamerTournamentChange'))->firstOrFail();

        $assignment=Assignment::where('tournament_id', $tournament->id)->where('couple_id', $gamerTournament->couple_id)->firstOrFail();
        $assignment->fill(['group' => request('group')])->save();

        if ($assignment) {
            return redirect()->route('torneos.list.gamers', ['slug' => $tournament->slug])->with(['type' => 'success', 'title' => 'Cambio exitoso', 'msg' => 'La pareja ha sido cambiada de grupo exitosamente.']);
        } else {
            return redirect()->route('torneos.list.gamers', ['slug' => $tournament->slug])->with(['type' => 'error', 'title' => 'Cambio fallido', 'msg' => 'Ha ocurrido un error durante el proceso, intentelo nuevamente.']);
        }
    }
}