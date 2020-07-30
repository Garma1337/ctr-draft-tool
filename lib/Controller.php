<?php

declare(strict_types = 1);

namespace DraftTool\Lib;

use DraftTool\Services\Draft;
use DraftTool\Services\Request;
use DraftTool\Services\Router;
use Smarty;
use SmartyException;

/**
 * Main Controller
 * @author Garma
 */
class Controller
{
    /**
     * @var Request
     */
    protected Request $request;
    
    /**
     * @var Router
     */
    protected Router $router;
    
    /**
     * @var Smarty
     */
    protected Smarty $template;
    
    public function __construct()
    {
        $this->request = App::request();
        $this->router = App::router();
        $this->template = App::template();
    }
    
    /**
     * Index Action
     * @throws SmartyException
     */
    public function indexAction(): void
    {
        $this->renderTemplate('index');
    }
    
    /**
     * "Create Draft" Action
     * @throws SmartyException
     */
    public function newAction(): void
    {
        $this->template->assign([
            'baseUrl'       => $this->router->getBaseUrl(),
            'formAction'    => $this->router->generateUrl('createDraft')
        ]);
        
        $this->renderTemplate('new');
    }
    
    /**
     * "Show Draft" Action
     * @throws SmartyException
     */
    public function showAction(): void
    {
        $draftId = (int) $this->request->getParam('id');
        $accessKey = $this->request->getParam('accessKey');
        
        $draft = App::draft()->findById($draftId);
        
        if ($draft !== null) {
            $teamId = null;
            
            if ($accessKey !== null) {
                $teamId = App::draft()->getTeamIdbyAccessKey($draftId, $accessKey);
            }
            
            $tracks = App::draft()->getAvailableTracks($draftId);
            
            /* Add random track to the selection */
            $tracks[] = [
                'id'    => 0,
                'name'  => 'Random'
            ];
            
            $this->template->assign([
                'id'                        => $draftId,
                'accessKey'                 => $accessKey,
                'draft'                     => $draft,
                'tracks'                    => $tracks,
                'teamA'                     => $draft['teams'][0]['id'],
                'teamB'                     => $draft['teams'][1]['id'],
                'teamId'                    => $teamId,
                'selectionThumbnailSize'    => 150,
                'trackGridThumbnailSize'    => 250,
                'currentPhase'              => App::draft()->getCurrentPhase($draftId),
                'currentTurn'               => App::draft()->getCurrentTurn($draftId)
            ]);
        } else {
            $this->template->assign('id', $draftId);
        }
        
        $this->renderTemplate('show');
    }
    
    /**
     * Displays a list of all drafts
     * @throws SmartyException
     */
    public function draftListAction(): void
    {
        $limit = 10;
        
        $page = (int) $this->request->getParam('page', 1);
        if ($page < 1) {
            $page = 1;
        }
        
        $totalDrafts = App::draft()->countDrafts();
        
        $pages = (int) ceil($totalDrafts / $limit);
        if ($page > $pages) {
            $page = $pages;
        }
        
        $offset = ($page - 1) * $limit;
        $drafts = App::draft()->findAll($limit, $offset);
        
        $this->template->assign([
            'drafts'    => $drafts,
            'pages'     => $pages,
            'page'      => $page
        ]);
        
        $this->renderTemplate('draftList');
    }
    
    /**
     * Ajax action to create a draft and return the created draft's data
     */
    public function createDraftAction(): void
    {
        if ($this->request->isPost()) {
            $response = [];
            
            $requestParams = [
                'teamA'                 => $this->request->getParam('teamA'),
                'teamB'                 => $this->request->getParam('teamB'),
                'bans'                  => (int) $this->request->getParam('bans'),
                'picks'                 => (int) $this->request->getParam('picks'),
                'timeout'               => (int) $this->request->getParam('timeout'),
                'enableSpyroCircuit'    => (bool) $this->request->getParam('enableSpyroCircuit'),
                'enableHyperSpaceway'   => (bool) $this->request->getParam('enableHyperSpaceway'),
                'enableRetroStadium'    => (bool) $this->request->getParam('enableRetroStadium'),
                'splitTurboRetro'       => (bool) $this->request->getParam('splitTurboRetro'),
                'allowTrackRepeats'     => (bool) $this->request->getParam('allowTrackRepeats')
            ];
            
            if (empty($requestParams['teamA'])) {
                $response['errors'][] = 'You must enter the name of Team A.';
            }
            
            if (empty($requestParams['teamB'])) {
                $response['errors'][] = 'You must enter the name of Team B.';
            }
            
            if (!empty($requestParams['teamA']) && !empty($requestParams['teamB'])) {
                if ($requestParams['teamA'] === $requestParams['teamB']) {
                    $response['errors'][] = 'Team A and Team B cannot have the same name.';
                }
            }
            
            if ($requestParams['bans'] <= 0) {
                $response['errors'][] = 'You must enter the number of bans per team.';
            } else {
                if (!$requestParams['allowTrackRepeats']) {
                    if ($requestParams['bans'] > 10) {
                        $response['errors'][] = 'The number of bans per team cannot be more than 10.';
                    }
                } else {
                    if ($requestParams['bans'] > 17) {
                        $response['errors'][] = 'The number of bans per team cannot be more than 17.';
                    }
                }
            }
            
            if ($requestParams['picks'] <= 0) {
                $response['errors'][] = 'You must enter the number of picks per team.';
            } else {
                if (!$requestParams['allowTrackRepeats']) {
                    if ($requestParams['picks'] > 18) {
                        $response['errors'][] = 'The number of picks per team cannot be more than 18.';
                    }
                } else {
                    if ($requestParams['picks'] > 30) {
                        $response['errors'][] = 'The number of picks per team cannot be more than 30.';
                    }
                }
            }
            
            if (!empty($requestParams['timeout'])) {
                if ($requestParams['timeout'] < 15 || $requestParams['timeout'] > 60) {
                    $response['errors'][] = 'The value for the timeout has to be between 15 and 60.';
                }
            }
            
            if (!is_array($response['errors']) || count($response['errors']) <= 0) {
                $draft = App::draft()->create(
                    $requestParams['teamA'],
                    $requestParams['teamB'],
                    $requestParams['bans'],
                    $requestParams['picks'],
                    $requestParams['timeout'],
                    $requestParams['enableSpyroCircuit'],
                    $requestParams['enableHyperSpaceway'],
                    $requestParams['enableRetroStadium'],
                    $requestParams['splitTurboRetro'],
                    $requestParams['allowTrackRepeats']
                );
                
                $response['success'] = 'Your draft was created successfully!';
                $response['draftData'] = $draft;
            }
            
            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'Invalid request method']);
        }
    }
    
    /**
     * Action to update a draft (ban / pick)
     */
    public function updateDraftAction(): void
    {
        if ($this->request->isPost()) {
            $draftId = (int) $this->request->getParam('draftId');
            $draft = App::draft()->findById($draftId);
            
            /* Do nothing if draft doesn't exist */
            if ($draft === null) {
                return;
            }
            
            $accessKey = $this->request->getParam('accessKey');
            $postedTeamId = (int) $this->request->getParam('teamId');
            $trackId = (int) $this->request->getParam('trackId');
            
            /* Double check - just for safety */
            $teamId = App::draft()->getTeamIdbyAccessKey($draftId, $accessKey);
            if ($teamId !== $postedTeamId) {
                return;
            }
            
            /* Check if a random track was selected */
            if ($trackId === 0) {
                $trackId = App::draft()->getRandomAvailableTrack($draftId);
            }
            
            $currentPhase = App::draft()->getCurrentPhase($draftId);
            
            if ($currentPhase === Draft::PHASE_BAN) {
                App::draft()->banTrack($draftId, $trackId, $postedTeamId);
            } else if ($currentPhase === Draft::PHASE_PICK) {
                App::draft()->pickTrack($draftId, $trackId, $postedTeamId);
            }
            
            header('Location: ' . $this->router->generateUrl('show', ['id' => $draftId, 'accessKey' => $accessKey]));
        } else {
            echo json_encode(['error' => 'Invalid request method']);
        }
    }
    
    /**
     * Renders the template
     * @param string $action
     * @throws SmartyException
     */
    protected function renderTemplate(string $action): void
    {
        $this->template->assign([
            'action' => $action,
            'router' => $this->router // any smarty extensions?
        ]);
        
        $content = $this->template->fetch($action . '.tpl');
        
        $layout = App::template();
        $layout->assign('content', $content);
        
        $layout->display('layout.tpl');
    }
}
