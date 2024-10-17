<?php 

namespace App\Services;

use App\Models\Tournament;

class TournamentService
{
    protected $tournamentModel;

    public function __construct(Tournament $tournamentModel)
    {
        $this->tournamentModel = $tournamentModel;
    }

    public function get_tournaments(): array
    {
        return $this->tournamentModel->all();
    }

    public function get_participants(int $tournament_id): array
    {
        return $this->tournamentModel->get_participants($tournament_id);
    }

    public function get_user_tournament_registrations(int $user_id): array
    {
        return $this->tournamentModel->get_user_tournament_registrations($user_id);
    }

    public function get_tournament_by_id(int $tournament_id): array
    {
        return $this->tournamentModel->get_tournament_by_id($tournament_id);
    }

    public function get_tournaments_by_status(string $status): array
    {
        return $this->tournamentModel->get_tournaments_by_status($status);
    }

    /************************ Tournament ************************/

    public function create_tournament(string $name, string $description, string $start_date): bool
    {
        return $this->tournamentModel->create_tournament($name, $description, $start_date);
    }

    public function start_tournament(int $tournament_id): bool
    {
        // 1. Ottieni i partecipanti attuali
        $participants = $this->get_participants($tournament_id);
        $current_count = count($participants);

        if ($current_count < 1) {
            return false;
        }

        // 2. Se i partecipanti sono meno di 8, aggiungi utenti dummy
        if ($current_count < 8) {
            $dummy_users_needed = 8 - $current_count;
            $this->tournamentModel->add_dummy_participants($tournament_id, $dummy_users_needed);
        }

        // 3. Avvia il torneo
        if (!$this->tournamentModel->start_tournament($tournament_id))
            return false;

        // 4. Crea i match
        if(!$this->create_matches_for_round($tournament_id, 1))
            return false;

        return true;
    }

    public function complete_tournament(int $tournament_id): bool
    {
        return $this->tournamentModel->complete_tournament($tournament_id);
    }

    public function delete_tournament(int $tournament_id): bool
    {
        return $this->tournamentModel->delete_tournament($tournament_id);
    }

    /************************ Match ************************/

    public function get_match(int $match_id): array
    {
        return $this->tournamentModel->get_match($match_id);
    }

    public function get_matches(int $tournament_id): array
    {
        return $this->tournamentModel->get_matches($tournament_id);
    }


    public function create_matches_for_round(int $tournament_id, int $round): bool
    {
        // Verifica che il torneo sia ongoing
        $tournament = $this->get_tournament_by_id($tournament_id);
        if ($tournament['status'] !== 'ongoing') {
            return false;
        }
    
        // Ottieni i partecipanti vincenti del round precedente se non è il primo round
        if ($round > 1) {
            $previous_round_winners = $this->tournamentModel->get_winners_by_round($tournament_id, $round - 1);
            if (count($previous_round_winners) % 2 != 0) {
                return false; // Numero dispari di vincitori
            }
    
            $participants = $previous_round_winners; // Utilizza i vincitori del round precedente
        } else {
            // Se è il primo round, ottieni tutti i partecipanti
            $participants = $this->get_participants($tournament_id);
        }
    
        // Mescola i partecipanti
        shuffle($participants);
    
        // Crea i match per il round
        for ($i = 0; $i < count($participants); $i += 2)
        {
            $participant1 = $participants[$i];
            $participant2 = $participants[$i + 1];
    
            // Creazione del match
            if (!$this->tournamentModel->create_matches($tournament_id, $round, $participant1, $participant2))
                return false;
        }

        if(!$this->dummy_match_update($tournament_id, $round))
            return false;
    
        return true;
    }
    
    public function check_and_advance_round(int $tournament_id): bool
    {
        if ($this->tournamentModel->get_tournament_status($tournament_id) === 'completed') {
            return false;
        }

        // Ottieni l'ultimo round completato
        $current_round = $this->tournamentModel->get_current_round($tournament_id);
    
        // Controlla se tutti i match del round corrente sono stati completati
        $completed_matches = $this->tournamentModel->get_completed_matches_by_round($tournament_id, $current_round);
        $total_matches = $this->tournamentModel->get_total_matches_by_round($tournament_id, $current_round);
    
        if (count($completed_matches) == $total_matches) {
            // Tutti i match sono completati, avanza al round successivo
            $next_round = $current_round + 1;
            if ($next_round == 4)
            {
                // Se è l'ultimo round, completa il torneo
                if ($this->complete_tournament($tournament_id))
                    return true;
            }
            return $this->create_matches_for_round($tournament_id, $next_round);
        }
    
        return false; // Non ancora pronti per il prossimo round
    }
    
    private function dummy_match_update(int $tournament_id, int $round)
    {
        $dummy_matches = $this->tournamentModel->get_dummy_match($tournament_id, $round);

        if (count($dummy_matches) == 0) {
            return false;
        }

        foreach ($dummy_matches as $dummy_match) {
            // Genera punteggi casuali per entrambi i partecipanti
            $score1 = rand(20, 100);
            $score2 = rand(20, 100);
    
            // Determina il vincitore in base al punteggio più alto
            $winner_id = $score1 > $score2 ? $dummy_match['participant1_id'] : $dummy_match['participant2_id'];
    
            // Crea una stringa risultato nel formato "score1 - score2"
            $result = "{$score1} - {$score2}";

            $data = [
                'winner_id' => $winner_id,
                'result' => $result,
                'status' => 'completed'
            ];

            // Aggiorna il match con il risultato
            if (!$this->tournamentModel->update_match($dummy_match['id'], $data))
                return false;
        }

        return true;
    }

    public function update(int $match_id, array $data): bool
    {
        // Ottieni i dettagli del match
        $match = $this->tournamentModel->get_match($match_id);

        // Identifica quale partecipante è l'utente reale
        $player1_is_user = $match['participant1_id'] === $data['user_id'];
        $player2_is_user = $match['participant2_id'] === $data['user_id'];

        if ($player1_is_user || $player2_is_user) {
            // Punteggio dell'utente reale
            $player_score = isset($data['player_score']) ? (int) $data['player_score'] : null;

            // Genera un punteggio casuale per il dummy, se presente
            $dummy_score = rand(20, 70);

            // Verifica se uno dei partecipanti è un dummy e se bisogna completare il match
            if ($match['is_dummy1'] && $player2_is_user) {
                // Il partecipante 1 è un dummy e il partecipante 2 è l'utente reale
                $result = "{$dummy_score} - {$player_score}";
                $winner_id = $dummy_score > $player_score ? $match['participant1_id'] : $match['participant2_id'];
            } elseif ($match['is_dummy2'] && $player1_is_user) {
                // Il partecipante 2 è un dummy e il partecipante 1 è l'utente reale
                $result = "{$player_score} - {$dummy_score}";
                $winner_id = $player_score > $dummy_score ? $match['participant1_id'] : $match['participant2_id'];
            } elseif ($player1_is_user && $player2_is_user) {
                // Entrambi i partecipanti sono utenti reali e hanno giocato
                if (isset($data['participant1_score']) && isset($data['participant2_score'])) {
                    $result = "{$data['participant1_score']} - {$data['participant2_score']}";
                    $winner_id = $data['participant1_score'] > $data['participant2_score'] ? $match['participant1_id'] : $match['participant2_id'];
                } else {
                    throw new \InvalidArgumentException('Entrambi i punteggi dei giocatori sono necessari.');
                }
            }

            // Aggiorna lo stato del match e segna come completato
            $data['result'] = $result;
            $data['winner_id'] = $winner_id;
            $data['status'] = 'completed';

            // Esegui l'aggiornamento del match
            return $this->tournamentModel->update_match($match_id, $data);
        }

        // Se nessuno dei partecipanti corrisponde all'utente reale, ritorna false
        return false;
    }


    /************************ Participant ************************/

    public function is_partecipant(int $tournament_id, int $user_id): bool
    {
        return $this->tournamentModel->is_partecipant($tournament_id, $user_id);
    }

    public function add_participant(int $tournament_id, int $user_id): bool
    {
        $num_partecipants = $this->get_participants($tournament_id);
        if (count($num_partecipants) >= 8) {
            return false;
        }
        return $this->tournamentModel->add_participant($tournament_id, $user_id);
    }

    public function remove_participant(int $tournament_id, int $user_id): bool
    {
        return $this->tournamentModel->remove_participant($tournament_id, $user_id);
    }

}
