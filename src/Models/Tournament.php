<?php

namespace App\Models;

use PDO;

class Tournament
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $query = 'SELECT * FROM tournaments';
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_participants(int $tournament_id): array
    {
        $query = "
            SELECT 
                tp.*, 
                CASE 
                    WHEN tp.is_dummy = 1 THEN dp.name
                    ELSE u.username 
                END AS username
            FROM tournament_participants tp
            LEFT JOIN users u ON tp.participant_id = u.id AND tp.is_dummy = 0
            LEFT JOIN dummy_players dp ON tp.participant_id = dp.id AND tp.is_dummy = 1
            WHERE tp.tournament_id = :tournament_id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_matches(int $tournament_id): array
    {
        $query = 'SELECT * FROM matches WHERE tournament_id = :tournament_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tournaments_by_status(string $status): array
    {
        $query = 'SELECT * FROM tournaments WHERE status = :status';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tournament_status(int $tournament_id): string
    {
        $query = 'SELECT status FROM tournaments WHERE id = :tournament_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function get_tournament_by_id(int $tournament_id): array
    {
        $query = 'SELECT * FROM tournaments WHERE id = :tournament_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_user_tournament_registrations(int $user_id): array
    {
        $query = 'SELECT tournament_id 
                FROM tournament_participants 
                WHERE participant_id = :user_id AND is_dummy = 0';  // Consideriamo solo gli utenti reali

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Restituisce un array di `tournament_id` a cui l'utente è iscritto
    }

    /************************ Tournament ************************/

    public function create_tournament(string $name, string $description, string $start_date): bool
    {
        $query = 'INSERT INTO tournaments (name, description, start_date, status) 
                  VALUES (:name, :description, :start_date, :status)';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindValue(':status', 'upcoming');

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function start_tournament(int $tournament_id): bool
    {
        $query = 'UPDATE tournaments SET status = :status, start_date = NOW() WHERE id = :tournament_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':status', 'ongoing');
        $stmt->bindParam(':tournament_id', $tournament_id);
        return $stmt->execute();
    }

    public function complete_tournament(int $tournament_id): bool
    {
        $query = 'UPDATE tournaments SET status = :status, end_date = NOW() WHERE id = :tournament_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':status', 'completed');
        $stmt->bindParam(':tournament_id', $tournament_id);
        return $stmt->execute();
    }

    public function delete_tournament(int $tournament_id): bool
    {
        $query = 'DELETE FROM tournaments WHERE id = :tournament_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        return $stmt->execute();
    }

    /************************ Participant ************************/
    
    public function remove_participant(int $tournament_id, int $participant_id, bool $is_dummy = false): bool
    {
        $query = 'DELETE FROM tournament_participants 
                WHERE tournament_id = :tournament_id 
                AND participant_id = :participant_id 
                AND is_dummy = :is_dummy';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':participant_id', $participant_id);
        $stmt->bindParam(':is_dummy', $is_dummy, PDO::PARAM_BOOL); // Usa is_dummy per distinguere

        return $stmt->execute();
    }

    public function add_dummy_participants(int $tournament_id, int $dummy_count): bool
    {
        for ($i = 0; $i < $dummy_count; $i++) {
            $dummy_name = 'Player' . rand(1000, 9999);  // Genera un nome fittizio

            // Inserisce il dummy player nella tabella dummy_players
            $query = "INSERT INTO dummy_players (tournament_id, name) 
                    VALUES (:tournament_id, :dummy_name)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':tournament_id', $tournament_id);
            $stmt->bindParam(':dummy_name', $dummy_name);

            
            $stmt->execute();
            
            // Ottieni l'ID del dummy player appena creato
            $dummy_id = $this->db->lastInsertId();

            $this->add_participant($tournament_id, $dummy_id, true);
        }

        return true;
    }

    public function add_participant(int $tournament_id, int $participant_id, bool $is_dummy = false): bool
    {
        $query = 'INSERT INTO tournament_participants (tournament_id, participant_id, is_dummy) 
                VALUES (:tournament_id, :participant_id, :is_dummy)';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':participant_id', $participant_id);
        $stmt->bindParam(':is_dummy', $is_dummy, PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }

    public function get_dummy_participants(int $tournament_id): array
    {
        $query = 'SELECT * FROM dummy_players WHERE tournament_id = :tournament_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function is_partecipant(int $tournament_id, int $participant_id, bool $is_dummy = false): bool
    {
        $query = 'SELECT * FROM tournament_participants 
                WHERE tournament_id = :tournament_id 
                AND participant_id = :participant_id 
                AND is_dummy = :is_dummy';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':participant_id', $participant_id);
        $stmt->bindParam(':is_dummy', $is_dummy, PDO::PARAM_BOOL); // Aggiungi il controllo per il flag dummy
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /************************ Matches ************************/

    public function create_matches(int $tournament_id, int $round, array $player1, array $player2): bool
    {
        $participant1_id = $player1['participant_id'];
        $is_dummy1 = $player1['is_dummy']; // Verifica se player1 è dummy

        $participant2_id = $player2['participant_id'];
        $is_dummy2 = $player2['is_dummy']; // Verifica se player2 è dummy

        $query = 'INSERT INTO matches (tournament_id, participant1_id, participant2_id, is_dummy1, is_dummy2, match_time, round) 
                VALUES (:tournament_id, :participant1_id, :participant2_id, :is_dummy1, :is_dummy2, NOW(), :round)';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':participant1_id', $participant1_id);
        $stmt->bindParam(':participant2_id', $participant2_id);
        $stmt->bindParam(':is_dummy1', $is_dummy1, PDO::PARAM_BOOL);
        $stmt->bindParam(':is_dummy2', $is_dummy2, PDO::PARAM_BOOL);
        $stmt->bindParam(':round', $round);

        $stmt->execute();

        return true;
    }

    public function get_winners_by_round(int $tournament_id, int $round)
    {
        $query = 'SELECT p.participant_id, p.tournament_id, p.is_dummy, COUNT(m.winner_id) as wins 
              FROM matches m
              JOIN tournament_participants p 
              ON m.winner_id = p.participant_id AND p.tournament_id = m.tournament_id
              WHERE m.tournament_id = :tournament_id 
              AND m.round = :round 
              GROUP BY p.participant_id, p.tournament_id, p.is_dummy';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':round', $round);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function get_match(int $match_id): array
    {
        $query = 'SELECT * FROM matches WHERE id = :match_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':match_id', $match_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_dummy_match(int $tournament_id, int $round): array
    {
        $query = 'SELECT * FROM matches 
              WHERE tournament_id = :tournament_id AND round = :round
              AND is_dummy1 = 1 
              AND is_dummy2 = 1 
              AND result IS NULL';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':round', $round);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_match(int $match_id, array $data): bool
    {
        $query = 'UPDATE matches 
            SET winner_id = :winner_id, result = :result, status = :status, match_time = NOW() 
            WHERE id = :match_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':match_id', $match_id);
        $stmt->bindParam(':result', $data['result']);
        $stmt->bindParam(':winner_id', $data['winner_id']);
        $stmt->bindParam(':status', $data['status']);

        return $stmt->execute();
    }

    public function get_current_round(int $tournament_id): int
    {
        $query = 'SELECT MAX(round) AS current_round FROM matches WHERE tournament_id = :tournament_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_COLUMN) ?? 1; // Se non ci sono round, inizia da 1
    }

    public function get_completed_matches_by_round(int $tournament_id, int $round): array
    {
        $query = 'SELECT * FROM matches WHERE tournament_id = :tournament_id AND round = :round AND status = "completed"';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':round', $round);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_total_matches_by_round(int $tournament_id, int $round): int
    {
        $query = 'SELECT COUNT(*) FROM matches WHERE tournament_id = :tournament_id AND round = :round';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tournament_id', $tournament_id);
        $stmt->bindParam(':round', $round);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

}
