<?php 

namespace App\Controllers;

use App\Core\Flash;
use App\Exceptions\HTTP\MethodNotAllowedException;
use App\Services\TournamentService;
use App\Facades\Filter;
use App\Services\GameService;

class TournamentController
{
    protected $tournamentService;
    protected $gameService;

    public function __construct(TournamentService $tournamentService, GameService $gameService)
    {
        $this->tournamentService = $tournamentService;
        $this->gameService = $gameService;
    }

    public function index()
    {
        $user_id = session()->get('user_id');
        
        // Ottieni tutti i tornei
        $tournaments = $this->tournamentService->get_tournaments();
        
        // Ottieni tutti i tournament_id ai quali l'utente è registrato
        $userRegistrations = $this->tournamentService->get_user_tournament_registrations($user_id);
        
        // Aggiungi la chiave is_registered a ciascun torneo
        foreach ($tournaments as &$tournament) {
            // Controlla se l'utente è registrato a questo torneo
            $tournament['is_registered'] = in_array($tournament['id'], $userRegistrations);
        }
        
        return view('tournaments/tournaments')->with_input([
            'tournaments' => $tournaments,
        ]);
    }

    /************************ Tournament Status ************************/

    public function create_tournament()
    {
        if (request()->is_method('post')) {
            $fields = [
                'csrf_token' => 'string',
                'name' => 'string | required | between: 3, 25',
                'description' => 'string | required',
                'start_date' => 'string | required',
            ];

            $messages = [
                'name' => [
                    'required' => 'Name is required'
                ],
                'description' => [
                    'required' => 'Description is required'
                ],
                'start_date' => [
                    'required' => 'Start date is required'
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->post(), $fields, $messages);

            if ($errors) {
                return redirect()->back()->with_message('Tournament could not be created, try again', Flash::FLASH_ERROR);
            }

            $name = $inputs['name'];
            $description = $inputs['description'];
            $start_date = $inputs['start_date'];
            if ($this->tournamentService->create_tournament($name, $description, $start_date))
                return redirect()->back()->with_message('Tournament created successfully');
            return redirect()->back()->with_message('Tournament could not be created, try again', Flash::FLASH_ERROR);
        }

        throw new MethodNotAllowedException("Method not allowed");
    }

    public function start_tournament()
    {
        if (request()->is_method('post')) {
            $fields = [
                'csrf_token' => 'string',
                'tournament_id' => 'int | required',
            ];

            $messages = [
                'tournament_id' => [
                    'required' => 'Tournament ID is required'
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->post(), $fields, $messages);

            if ($errors) {
                return redirect()->back()->with_message('Tournament could not be created, try again', Flash::FLASH_ERROR);
            }

            $tournament_id = $inputs['tournament_id'];
            if ($this->tournamentService->start_tournament((int) $tournament_id))
                return redirect()->back()->with_message('Tournament started successfully');
            return redirect()->back()->with_message('Tournament could not be started', Flash::FLASH_ERROR);
        }
        throw new MethodNotAllowedException("Method not allowed");
    }

    public function complete_tournament()
    {
        if (request()->is_method('post')) {
            $fields = [
                'csrf_token' => 'string',
                'tournament_id' => 'int | required',
            ];

            $messages = [
                'tournament_id' => [
                    'required' => 'Tournament ID is required'
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->post(), $fields, $messages);

            if ($errors) {
                return redirect()->back()->with_message('Tournament could not be completed, try again', Flash::FLASH_ERROR);
            }

            $tournament_id = $inputs['tournament_id'];
            if($this->tournamentService->complete_tournament((int) $tournament_id))
                return redirect()->back()->with_message('Tournament completed successfully');
            return redirect()->back()->with_message('Tournament could not be completed', Flash::FLASH_ERROR);

        }
        throw new MethodNotAllowedException("Method not allowed");
    }

    public function delete_tournament()
    {
        if (request()->is_method('post')) {
            $fields = [
                'csrf_token' => 'string',
                'tournament_id' => 'int | required',
            ];

            $messages = [
                'tournament_id' => [
                    'required' => 'Tournament ID is required'
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->post(), $fields, $messages);

            if ($errors) {
                return redirect()->back()->with_message('Tournament could not be deleted', Flash::FLASH_ERROR);
            }

            $tournament_id = $inputs['tournament_id'];
            if($this->tournamentService->delete_tournament((int) $tournament_id))
                return redirect()->back()->with_message('Tournament deleted successfully');
            return redirect()->back()->with_message('Tournament could not be deleted', Flash::FLASH_ERROR);
        }
        throw new MethodNotAllowedException("Method not allowed");
    }

    /*********************** User Action ***********************/
    public function subscribe(int $tournament_id)
    {
        if (request()->is_method('post')) {
            $fields = [
                'csrf_token' => 'string | required'
            ];

            $messages = [
                'csrf_token' => [
                    'required' => 'CSRF token is required'
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->post(), $fields, $messages);

            if ($errors) {
                return redirect()->back()->with_message('Could not subscribe to tournament', Flash::FLASH_ERROR);
            }

            $user_id = session()->get('user_id');
            if ($this->tournamentService->add_participant($tournament_id, $user_id))
                return redirect()->back()->with_message('Subscribed to tournament successfully');
            return redirect()->back()->with_message('Could not subscribe to tournament', Flash::FLASH_ERROR);
        }
        throw new MethodNotAllowedException("Method not allowed");
    }
    
    public function unsubscribe(int $tournament_id)
    {
        if (request()->is_method('post')) {
            $fields = [
                'csrf_token' => 'string | required'
            ];

            $messages = [
                'csrf_token' => [
                    'required' => 'CSRF token is required'
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->post(), $fields, $messages);

            if ($errors) {
                return redirect()->back()->with_message('Could not unsubscribe from tournament', Flash::FLASH_ERROR);
            }

            $user_id = session()->get('user_id');
            if ($this->tournamentService->remove_participant($tournament_id, $user_id))
                return redirect()->back()->with_message('Unsubscribed from tournament successfully');
            return redirect()->back()->with_message('Could not unsubscribe from tournament', Flash::FLASH_ERROR);
        }
        throw new MethodNotAllowedException("Method not allowed");
    }

    public function dashboard(int $tournament_id)
    {
        // 1. Verifica se il torneo esiste
        $tournament = $this->tournamentService->get_tournament_by_id($tournament_id);
        if (!$tournament) {
            return redirect('/tournaments')->with_message('Tournament not found.', Flash::FLASH_ERROR);
        }

        // 2. Verifica se l'utente è iscritto al torneo
        $user_id = session()->get('user_id');
        if (!$this->tournamentService->is_partecipant($tournament_id, $user_id)) {
            return redirect('/tournaments')->with_message('You are not registered for this tournament.', Flash::FLASH_ERROR);
        }

        // 3. Controlla se il torneo è ongoing
        if ($tournament['status'] === 'upcoming') {
            return redirect('/tournaments')->with_message('This tournament is not currently active.', Flash::FLASH_ERROR);
        }

        $this->tournamentService->check_and_advance_round($tournament_id);
        // 4. Prepara i dati per la view (es. informazioni su partecipanti e partite)
        $participants = $this->tournamentService->get_participants($tournament_id);
        $matches = $this->tournamentService->get_matches($tournament_id);

        // 5. Mostra la dashboard del torneo
        return view('tournaments/dashboard')->with_input([
            'tournament' => $tournament,
            'participants' => $participants,
            'matches' => $matches,
        ]);
    }


    public function play(int $tournament_id, int $match_id)
    {
        $match = $this->tournamentService->get_match($match_id);

        $user_id = session()->get('user_id');


        if (!$match || $match['status'] !== 'upcoming' || ($match['participant1_id'] !== $user_id && $match['participant2_id'] !== $user_id)) {
            $url = '/'. 'tournaments/' . $tournament_id . '/dashboard';
            return redirect($url)->with_message('Match not found', Flash::FLASH_ERROR);
        }

        $game = $this->gameService->get_game_by_name('dot');

        $stats = $this->gameService->game_stats('dot');

        return view('games/dot')->with_input([
            'game' => $game,
            'stats' => $stats,
            'id' => $match_id
        ]);
    }

    public function result(int $tournament_id, int $match_id)
    {
        if (request()->is_method('post')) {
            $fields = [
                'score' => 'int | required',
            ];

            $messages = [
                'score' => [
                    'required' => 'Score is required'
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->post(), $fields, $messages);

            if ($errors) {
                return redirect()->back()->with_message('Score is required', Flash::FLASH_ERROR);
            }

            $user_id = session()->get('user_id');

            $data = [
                'player_score' => $inputs['score'],
                'user_id' => $user_id
            ];

            if(!$this->tournamentService->update($match_id, $data))
                return redirect()->back()->with_message('Could not update score', Flash::FLASH_ERROR);
            
            $url = '/'. 'tournaments/' . $tournament_id . '/dashboard';
            return redirect($url);
        }
        throw new MethodNotAllowedException("Method not allowed");
    }
}
