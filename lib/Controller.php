<?php

declare(strict_types = 1);

namespace DraftTool\Lib;

use DraftTool\Services\Draft;
use DraftTool\Services\Request;
use DraftTool\Services\Translator;
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
     * @var Smarty
     */
    protected Smarty $template;
    
    public function __construct()
    {
        $this->request = App::request();
        $this->template = App::template();
    }
    
    /**
     * This method is always executed before every action
     */
    public function preDispatch(): void
    {
        if ($this->request->has('language')) {
            $language = $this->request->getParam('language');
            
            if (!App::translator()->languageExists($language)) {
                $_SESSION['language'] = Translator::LANGUAGE_ENGLISH;
            } else {
                $_SESSION['language'] = $language;
            }
        }
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
     * Action to create a new draft
     * @throws SmartyException
     */
    public function newAction(): void
    {
        $this->template->assign([
            'baseUrl'       => App::router()->getBaseUrl(),
            'formAction'    => App::router()->generateUrl('createDraft'),
            'modes'         => App::draft()->getModes()
        ]);
        
        $this->renderTemplate('new');
    }
    
    /**
     * Action to show a draft
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
            
            $existingTracks = App::draft()->getExistingTracks((int) $draft['mode']);
            $availableTracks = App::draft()->getAvailableTracks($draftId);
            
            /* Bad performance */
            foreach ($existingTracks as $index => $existingTrack) {
                $isAvailable = false;
                
                foreach ($availableTracks as $availableTrack) {
                    if ($availableTrack['id'] === $existingTrack['id']) {
                        $isAvailable = true;
                        
                        false;
                    }
                }
                
                $existingTracks[$index]['isAvailable'] = $isAvailable;
            }
            
            /* Add random track to the selection */
            $existingTracks[] = [
                'id'            => 0,
                'name'          => 'Random',
                'isAvailable'   => true
            ];
            
            $this->template->assign([
                'id'                        => $draftId,
                'accessKey'                 => $accessKey,
                'draft'                     => $draft,
                'tracks'                    => $existingTracks,
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
        if ($pages <= 0) {
            $pages = 1;
        }
        
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
                'mode'                  => (int) $this->request->getParam('mode'),
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
            
            $response['errors'] = App::draft()->validateParams($requestParams);
            
            if (count($response['errors']) <= 0) {
                $draft = App::draft()->create(
                    $requestParams['mode'],
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
            
            $this->redirect('show', ['id' => $draftId, 'accessKey' => $accessKey]);
        } else {
            echo json_encode(['error' => 'Invalid request method']);
        }
    }
    
    /**
     * This method is always executed after every action
     */
    public function postDispatch(): void
    {
    }
    
    /**
     * Renders the template
     * @param string $action
     * @throws SmartyException
     */
    protected function renderTemplate(string $action): void
    {
        /* Imagine assigning objects to the view KEKW ... I am lazy */
        $this->template->assign([
            'action'        => $action,
            'router'        => App::router(),
            'translator'    => App::translator(),
            'selectedTheme' => $_SESSION['theme'],
            'lightTheme'    => App::THEME_LIGHT,
            'darkTheme'     => App::THEME_DARK,
        ]);
        
        $content = $this->template->fetch($action . '.tpl');
        
        $layout = App::template();
        $layout->assign('content', $content);
        
        $layout->display('layout.tpl');
    }
    
    /**
     * Redirects a user to a given action
     * @param string $action
     * @param array $params
     */
    protected function redirect(string $action, array $params = []): void
    {
        $url = App::router()->generateUrl($action, $params);
        header('Location: ' . $url, true, 301);
    }
}
