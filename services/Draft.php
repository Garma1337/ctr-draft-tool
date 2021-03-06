<?php

declare(strict_types = 1);

namespace DraftTool\Services;

use Carbon\Carbon;
use DraftTool\Lib\App;
use PDO;

/**
 * Service to handle draft functions
 * @author Garma
 */
class Draft
{
    public const MODE_RACE = 1;
    public const MODE_BATTLE = 2;
    
    public const PHASE_BAN = 'ban';
    public const PHASE_PICK = 'pick';
    public const PHASE_DONE = 'done';
    
    public const TRACK_SPYRO_CIRCUIT = 34;
    public const TRACK_HYPER_SPACEWAY = 31;
    public const TRACK_RETRO_STADIUM = 40;
    
    /**
     * Creates a new draft and returns the data of the inserted row (id, access keys)
     * @param int $mode
     * @param string $teamA
     * @param string $teamB
     * @param int $bans
     * @param int $picks
     * @param int|null $timeout
     * @param bool $enableSpyroCircuit
     * @param bool $enableHyperSpaceway
     * @param bool $enableRetroStadium
     * @param bool $splitTurboRetro
     * @param bool $allowTrackRepeats
     */
    public function create(
        int $mode,
        string $teamA,
        string $teamB,
        int $bans,
        int $picks,
        ?int $timeout,
        bool $enableSpyroCircuit,
        bool $enableHyperSpaceway,
        bool $enableRetroStadium,
        bool $splitTurboRetro,
        bool $allowTrackRepeats
    ): array {
        /* These tracks are unavailable in battle mode obviously */
        if ($mode === self::MODE_BATTLE) {
            $enableSpyroCircuit = false;
            $enableHyperSpaceway = false;
            $enableRetroStadium = false;
            $splitTurboRetro = false;
        }
        
        App::db()->beginTransaction();
        
        App::db()->insert('drafts', [
            'mode'                  => $mode,
            'bans'                  => $bans,
            'picks'                 => $picks,
            'timeout'               => $timeout,
            'enableSpyroCircuit'    => (int) $enableSpyroCircuit,
            'enableHyperSpaceway'   => (int) $enableHyperSpaceway,
            'enableRetroStadium'    => (int) $enableRetroStadium,
            'splitTurboRetro'       => (int) $splitTurboRetro,
            'allowTrackRepeats'     => (int) $allowTrackRepeats,
            'created'               => Carbon::now()->toDateTimeString()
        ]);
        
        $id = App::db()->lastInsertId();
        
        $accessKeyA = $this->generateAccessKey();
        $accessKeyB = $this->generateAccessKey();
        
        App::db()->insert('draft_teams', [
            'draftId'       => $id,
            'teamName'      => $teamA,
            'accessKey'     => $accessKeyA
        ]);
        
        App::db()->insert('draft_teams', [
            'draftId'       => $id,
            'teamName'      => $teamB,
            'accessKey'     => $accessKeyB,
            'ready'         => (int) ($timeout === null)
        ]);
        
        App::db()->commit();
        
        return [
            'id'            => $id,
            'accessKeyA'    => $accessKeyA,
            'accessKeyB'    => $accessKeyB
        ];
    }
    
    /**
     * Generates a random string that serves as access key for a draft
     * @return string
     */
    public function generateAccessKey(): string
    {
        $characters = array_merge(
            str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),
            str_split('0123456789')
        );
        
        $accessKey = '';
        
        for ($i = 0; $i <= 20; $i++) {
            $randomKey = mt_rand(0, 35);
            $accessKey .= $characters[$randomKey];
        }
        
        return $accessKey;
    }
    
    /**
     * Finds the team id by draft and access key
     * @param int $draftId
     * @param string $accessKey
     * @return int|null
     */
    public function getTeamIdbyAccessKey(int $draftId, string $accessKey): ?int
    {
        $teamId = App::db()
            ->executeQuery('SELECT id FROM draft_teams WHERE draftId = ? AND accessKey LIKE ?', [$draftId, $accessKey])
            ->fetchColumn()
        ;
        
        if ($teamId === false) {
            return null;
        }
        
        return (int) $teamId;
    }
    
    /**
     * Returns the ID of the team that is currently picking/banning
     * @param int $draftId
     * @return int|null
     */
    public function getCurrentTurn(int $draftId): ?int
    {
        $currentTurn = null;
        
        $currentPhase = $this->getCurrentPhase($draftId);
        $draft = $this->findById($draftId);
        
        if ($currentPhase === self::PHASE_BAN) {
            $countBannedTracks = count($draft['bannedTracks']);
            
            if ($countBannedTracks < ($draft['bans'] * 2)) {
                /* Team A bans first */
                if ($countBannedTracks % 2 === 0) {
                    $currentTurn = $draft['teams'][0]['id'];
                } else {
                    $currentTurn = $draft['teams'][1]['id'];
                }
            }
        }
        
        if ($currentPhase === self::PHASE_PICK) {
            $countPickedTracks = count($draft['pickedTracks']);
            
            if ($countPickedTracks < ($draft['picks'] * 2)) {
                if (($countPickedTracks + 1) % 4 === 0 || ($countPickedTracks + 1) % 4 === 1) {
                    $currentTurn = $draft['teams'][1]['id'];
                } else {
                    $currentTurn = $draft['teams'][0]['id'];
                }
            }
        }
        
        return (int) $currentTurn;
    }
    
    /**
     * Returns draft data of a given ID
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $query = 'SELECT
                    d.*,
                    dm.name AS modeName
                  FROM drafts d
                  INNER JOIN draft_modes dm
                  ON d.mode = dm.id
                  WHERE d.id = ?';
        
        $draft = App::db()->executeQuery($query, [$id])->fetch();
        
        if ($draft === false) {
            return null;
        }
        
        $query = 'SELECT * FROM draft_teams WHERE draftId = ?';
        $draft['teams'] = App::db()->executeQuery($query, [$id])->fetchAll();
        
        $query = 'SELECT
                    db.*,
                    t.name
                  FROM draft_bans db
                  INNER JOIN tracks t
                  ON db.trackId = t.id
                  WHERE draftId = ?';
        
        $draft['bannedTracks'] = App::db()->executeQuery($query, [$id])->fetchAll();
        
        $query = 'SELECT
                    dp.*,
                    t.name
                  FROM draft_picks dp
                  INNER JOIN tracks t
                  ON dp.trackId = t.id
                  WHERE draftId = ?';
        
        $draft['pickedTracks'] = App::db()->executeQuery($query, [$id])->fetchAll();
        
        return $draft;
    }
    
    /**
     * Returns a random available track
     * @param int $draftId
     * @return int
     */
    public function getRandomAvailableTrack(int $draftId): int
    {
        $availableTracks = $this->getAvailableTracks($draftId);
        
        $randomKey = mt_rand(0, (count($availableTracks) - 1));
        $randomTrack = $availableTracks[$randomKey];
        
        return (int) $randomTrack['id'];
    }
    
    /**
     * Returns the current draft phase (pick, ban or done)
     * @param int $draftId
     * @return string
     */
    public function getCurrentPhase(int $draftId): string
    {
        $phase = self::PHASE_DONE;
        $draft = $this->findById($draftId);
        
        if (count($draft['bannedTracks']) < ($draft['bans'] * 2)) {
            $phase = self::PHASE_BAN;
        } else if (count($draft['pickedTracks']) < ($draft['picks'] * 2)) {
            $phase = self::PHASE_PICK;
        }
        
        return $phase;
    }
    
    /**
     * Returns a list of all drafts
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findAll(int $limit, int $offset): array
    {
        $drafts = [];
        
        /*
         * Show newest drafts first.
         *
         * Also: Doctrine DBAL is pepega and doesn't support
         * parameters for LIMIT and OFFSET
         */
        $query = 'SELECT id
                  FROM drafts
                  ORDER BY created DESC
                  LIMIT ' . $limit . '
                  OFFSET ' . $offset;
        
        $ids = App::db()->executeQuery($query)->fetchAll(PDO::FETCH_COLUMN);
        
        /* Bad performance, I guess I could just rewrite the query above to join with the draft_teams table */
        foreach ($ids as $id) {
            $drafts[$id] = $this->findById((int) $id);
        }
        
        return $drafts;
    }
    
    /**
     * Returns the total amount of all drafts that have ever been done
     * @return int
     */
    public function countDrafts(): int
    {
        return (int) App::db()->executeQuery('SELECT COUNT(id) FROM drafts')->fetchColumn();
    }
    
    /**
     * Checks if a track is still available for either ban or pick
     * @param int $trackId
     * @param int $draftId
     * @return bool
     */
    public function isTrackAvailable(int $trackId, int $draftId): bool
    {
        $isTrackAvailable = false;
        $availableTracks = $this->getAvailableTracks($draftId);
        
        foreach ($availableTracks as $availableTrack) {
            if ((int) $availableTrack['id'] === $trackId) {
                $isTrackAvailable = true;
                
                break;
            }
        }
        
        return $isTrackAvailable;
    }
    
    /**
     * Finds all available tracks for a given draft
     * @param int $draftId
     * @return array
     */
    public function getAvailableTracks(int $draftId): array
    {
        $draft = $this->findById($draftId);
        
        $queryBuilder = App::db()
            ->createQueryBuilder()
            ->select('*')
            ->from('tracks')
            ->where('id NOT IN (SELECT trackId FROM draft_bans WHERE draftId = :draftId)')
            ->andWhere('mode = :mode')
            ->setParameter('draftId', $draftId)
            ->setParameter('mode', $draft['mode'])
        ;
        
        if ((int) $draft['mode'] === self::MODE_RACE) {
            if (!$draft['enableSpyroCircuit']) {
                $queryBuilder->andWhere('id != ' . self::TRACK_SPYRO_CIRCUIT);
            }
            
            if (!$draft['enableHyperSpaceway']) {
                $queryBuilder->andWhere('id != ' . self::TRACK_HYPER_SPACEWAY);
            }
            
            if (!$draft['enableRetroStadium'] || !$draft['splitTurboRetro']) {
                $queryBuilder->andWhere('id != ' . self::TRACK_RETRO_STADIUM);
            }
        }
        
        if (!$draft['allowTrackRepeats']) {
            $queryBuilder->andWhere('id NOT IN (SELECT trackId FROM draft_picks WHERE draftId = :draftId)');
        }
        
        return $queryBuilder->execute()->fetchAll();
    }
    
    /**
     * Returns a list of all existing tracks, optionally filtered by mode
     * @param int|null $mode
     * @return array
     */
    public function getExistingTracks(?int $mode = null): array
    {
        $params = [];
        $query = 'SELECT * FROM tracks';
        
        if ($mode !== null) {
            $query .= ' WHERE mode = ?';
            $params = [$mode];
        }
        
        return App::db()->executeQuery($query, $params)->fetchAll();
    }
    
    /**
     * Returns a list of all draft modes
     * @return array
     */
    public function getModes(): array
    {
        $query = 'SELECT * FROM draft_modes';
        return App::db()->executeQuery($query)->fetchAll();
    }
    
    /**
     * Bans a track
     * @param int $draftId
     * @param int $trackId
     * @param int $teamId
     */
    public function banTrack(int $draftId, int $trackId, int $teamId): void
    {
        if (!$this->isTrackAvailable($trackId, $draftId)) {
            return;
        }
        
        App::db()->insert('draft_bans', [
            'draftId'   => $draftId,
            'trackId'   => $trackId,
            'teamId'    => $teamId,
            'sortOrder' => 0
        ]);
    }
    
    /**
     * Picks a track
     * @param int $draftId
     * @param int $trackId
     * @param int $teamId
     */
    public function pickTrack(int $draftId, int $trackId, int $teamId): void
    {
        if (!$this->isTrackAvailable($trackId, $draftId)) {
            return;
        }
        
        App::db()->insert('draft_picks', [
            'draftId'   => $draftId,
            'trackId'   => $trackId,
            'teamId'    => $teamId,
            'sortOrder' => 0
        ]);
    }
    
    /**
     * Validates a draft
     * @param array $params
     * @return array
     */
    public function validateParams(array $params): array
    {
        $errors = [];
        
        if (empty($params['mode'])) {
            $errors[] = App::translator()->translate('action.createDraft.modeMissing');
        } else {
            if ($params['mode'] !== self::MODE_RACE && $params['mode'] !== self::MODE_BATTLE) {
                $errors[] = App::translator()->translate('action.createDraft.modeDoesNotExist');
            }
        }
        
        if (empty($params['teamA'])) {
            $errors[] = App::translator()->translate('action.createDraft.teamANameMissing');
        }
        
        if (empty($params['teamB'])) {
            $errors[] = App::translator()->translate('action.createDraft.teamBNameMissing');
        }
        
        if (!empty($params['teamA']) && !empty($params['teamB'])) {
            if ($params['teamA'] === $params['teamB']) {
                $errors[] = App::translator()->translate('action.createDraft.sameTeamNames');
            }
        }
        
        if ($params['bans'] > 0) {
            if (!$params['allowTrackRepeats']) {
                $limit = ($params['mode'] === self::MODE_RACE ? 15 : 5);
                
                if ($params['bans'] > $limit) {
                    $errors[] = str_replace('#1', $limit, App::translator()->translate('action.createDraft.maxNumberBans'));
                }
            } else {
                $limit = ($params['mode'] === self::MODE_RACE ? 17 : 5);
                
                if ($params['bans'] > $limit) {
                    $errors[] = str_replace('#1', $limit, App::translator()->translate('action.createDraft.maxNumberBans'));;
                }
            }
        }
        
        if ($params['picks'] <= 0) {
            $errors[] = App::translator()->translate('action.createDraft.numberPicksMissing');
        } else {
            if (!$params['allowTrackRepeats']) {
                $limit = ($params['mode'] === self::MODE_RACE ? 18 : 6);
                
                if ($params['picks'] > $limit) {
                    $errors[] = str_replace('#1', $limit, App::translator()->translate('action.createDraft.maxNumberPicks'));
                }
            } else {
                if ($params['picks'] > 30) {
                    $errors[] = str_replace('#1', 30, App::translator()->translate('action.createDraft.maxNumberPicks'));
                }
            }
        }
        
        if (!empty($params['timeout'])) {
            if ($params['timeout'] < 15 || $params['timeout'] > 60) {
                $errors[] = App::translator()->translate('action.createDraft.timeValueIncorrect');
            }
        }
        
        return $errors;
    }
}
