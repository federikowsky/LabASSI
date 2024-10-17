<?php 
    // Funzione per trovare il nome del partecipante dato il suo ID
    function player_name($participants, $participant_id) {
        foreach ($participants as $participant) {
            if ($participant['participant_id'] == $participant_id) {
                return $participant['username'];
            }
        }
        return 'Unknown';
    }

    // Raggruppa i match per round
    function group_matches_by_round($matches) {
        $grouped = [];
        foreach ($matches as $match) {
            $round = $match['round'];
            if (!isset($grouped[$round])) {
                $grouped[$round] = [];
            }
            $grouped[$round][] = $match;
        }
        return $grouped;
    }

    // Raggruppa i match in base ai round
    $grouped_matches = group_matches_by_round($matches);

    // Definisci il numero di match per ciascun round basato sulla potenza di 2
    $matches_per_round = [
        1 => 4,  // Round 1: 4 match
        2 => 2,  // Round 2: 2 match
        3 => 1,  // Round 3: 1 match
        4 => 1   // Round 4: 1 match (finale)
    ];

    function get_player_score(array $match, int $player_id): ?int
    {
        // Splitta il risultato per ottenere i punteggi
        $scores = explode(' - ', $match['result']);
        
        // Se il risultato non è valido, continua al prossimo match
        if (count($scores) !== 2 || !is_numeric($scores[0]) || !is_numeric($scores[1])) {
            return null;
        }

        $score1 = (int) $scores[0];
        $score2 = (int) $scores[1];

        // Controlla se il player_id corrisponde a uno dei partecipanti e restituisci il suo punteggio
        if ($match['participant1_id'] == $player_id) {
            return $score1;
        } elseif ($match['participant2_id'] == $player_id) {
            return $score2;
        }

        // Se non è stato trovato il punteggio per quel giocatore, restituisci null
        return null;
    }

?>

<div class="container tourn-dashboard">
    <div class="tourn-header text-white">
        <h1 class="h1-colored"><?= htmlspecialchars($tournament['name']) ?> Tournament</h>
        <h2><?= htmlspecialchars($tournament['description']) ?></h2>
    </div>
    
    <!-- Tournament Bracket Section -->
    <div class="tournament-bracket tournament-bracket--rounded">
        <?php for ($i = 1; $i <= 4; $i++) : // Itera su ciascun round ?>
            <div class="tournament-bracket__round tournament-bracket__round--<?= $i ?>">
                <?php if ($i < 4) : ?>
                    <h3 class="tournament-bracket__round-title">Round <?= $i ?></h3>
                <?php else : ?>
                    <h3 class="tournament-bracket__round-title">Winner</h3>
                <?php endif; ?>
                <ul class="tournament-bracket__list">
                    <?php 
                    // Conta quanti match devono esserci in questo round
                    $num_matches_in_round = $matches_per_round[$i];
                    $matches_in_current_round = isset($grouped_matches[$i]) ? $grouped_matches[$i] : [];

                    // Mostra i match effettivi
                    for ($j = 0; $j < $num_matches_in_round; $j++) :
                        $match = $matches_in_current_round[$j] ?? null; // Prendi il match o null se non esiste
                    ?>
                        <li class="tournament-bracket__item">
                            <div class="tournament-bracket__match" tabindex="0" data-id="<?= $match ? htmlspecialchars($match['id']) : '' ?>"
                                <?php if ($match && (!$match['is_dummy1'] || !$match['is_dummy2']) && $match['status'] === 'upcoming') : ?>
                                    onclick="window.location.href='/tournaments/<?= $tournament['id'] ?>/match/<?= $match['id'] ?>/play'"
                                <?php endif; ?>
                                >
                                <table class="tournament-bracket__table">
                                    <tbody class="tournament-bracket__content">
                                        <?php if ($match) : 
                                            $player1_name = htmlspecialchars(player_name($participants, $match['participant1_id']));
                                            $player2_name = htmlspecialchars(player_name($participants, $match['participant2_id']));
                                        ?>
                                        <!-- Player 1 -->
                                        <tr class="tournament-bracket__team <?= $match['winner_id'] == $match['participant1_id'] ? 'tournament-bracket__team--winner' : '' ?>">
                                            <td class="tournament-bracket__country">
                                                <abbr class="tournament-bracket__code" title="<?= $player1_name ?>"><?= substr($player1_name, 0, 3) ?></abbr>
                                            </td>
                                            <td class="tournament-bracket__score">
                                                <?php if ($match['status'] === 'completed') : ?>
                                                    <span class="tournament-bracket__number"><?= get_player_score($match, $match['participant1_id']) ?></span>
                                                <?php else : ?>
                                                    <span class="tournament-bracket__number">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <!-- Player 2 -->
                                        <tr class="tournament-bracket__team <?= $match['winner_id'] == $match['participant2_id'] ? 'tournament-bracket__team--winner' : '' ?>">
                                            <td class="tournament-bracket__country">
                                                <abbr class="tournament-bracket__code" title="<?= $player2_name ?>"><?= substr($player2_name, 0, 3) ?></abbr>
                                            </td>
                                            <td class="tournament-bracket__score">
                                                <?php if ($match['status'] === 'completed') : ?>
                                                    <span class="tournament-bracket__number"><?= get_player_score($match, $match['participant2_id']) ?></span>
                                                <?php else : ?>
                                                    <span class="tournament-bracket__number">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <?php else : // Se non c'è un match, mostra una riga vuota ?>
                                            <?php if ($i != 4) : ?>
                                                <tr class="tournament-bracket__team">
                                                    <td class="tournament-bracket__country">
                                                        <abbr class="tournament-bracket__code" title="No match">---</abbr>
                                                    </td>
                                                    <td class="tournament-bracket__score">
                                                        <span class="tournament-bracket__number">-</span>
                                                    </td>
                                                </tr>
                                                <tr class="tournament-bracket__team">
                                                    <td class="tournament-bracket__country">
                                                        <abbr class="tournament-bracket__code" title="No match">---</abbr>
                                                    </td>
                                                    <td class="tournament-bracket__score">
                                                        <span class="tournament-bracket__number">-</span>
                                                    </td>
                                                </tr>
                                            <?php else : ?>
                                                <?php if ($matches[count($matches) - 1]['winner_id']) : ?>
                                                    <tr class="tournament-bracket__team tournament-bracket__team--winner" style="text-align: center;">
                                                        <td colspan="2" class="tournament-bracket__medal">
                                                            <div class="tournament-bracket__medal tournament-bracket__medal--gold fa fa-trophy" aria-label="Gold medal" style="font-size: 2em;"></div>
                                                        </td>
                                                    </tr>
                                                    <tr class="tournament-bracket__team" style="text-align: center;">
                                                        <td colspan="2" class="tournament-bracket__country">
                                                            <abbr class="tournament-bracket__code" style="font-size: 1.5em;">
                                                                <?= htmlspecialchars(player_name($participants, $matches[count($matches) - 1]['winner_id'])) ?>
                                                            </abbr>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>
        <?php endfor; ?>
    </div>
</div>
