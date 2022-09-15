<?php

/**
 * [ControllerEventListener] OperationControllerEventListener
 *
 * @category baserCMS Plugin
 * @package  Operation.Event
 * @author   Ryosuke Momoi
 * @link     https://github.com/momofff/baserCMS-Operation
 */

class OperationControllerEventListener extends BcControllerEventListener
{


    /**
     * Event
     *
     * @var array
     */
    public $events = [
        'startup',
        'beforeRender'
    ];


    /**
     * startup
     *
     * @param  CakeEvent $event
     * @return boolean
     */
    public function startup(CakeEvent $event)
    {
        if (!BcUtil::isAdminSystem()) return TRUE;

        $Controller     = $event->subject();
        $controllerName = $Controller->name;
        $action         = $Controller->action;

        $loginUser = BcUtil::loginUser();
        $userGroup = $loginUser['UserGroup']['name'];

        $adminsName               = Configure::read('Operation.admin.adminsName');
        $allowedAdminAllOperation = Configure::read('Operation.admin.allowedAdminAllOperation');
        if (in_array($userGroup, $adminsName) && $allowedAdminAllOperation === TRUE) return TRUE;

        $Operation = ClassRegistry::init('Operation.Operation');

        $Controller->uses[] = 'Site';

        $operationSite = $Operation->findOperationSites();
        if (!empty($operationSite) && is_array($operationSite)) {
            $site = [];
            foreach ($operationSite as $siteId => $array) {
                $site[$siteId] = $array['Site']['title'];
            }
        }

        if (preg_match('/\Aadmin_/', $action)) {
            switch ($controllerName) {
            case 'Contents':
                switch ($action) {
                case 'admin_index':
                    if (!empty($site)) {
                        if (isset($Controller->passedArgs['site_id']) && !array_key_exists($Controller->passedArgs['site_id'], $site)) {
                            $keys = array_keys($site);
                            $Controller->passedArgs['site_id'] = $keys[0];
                        }
                    } else {
                        $Controller->passedArgs['site_id'] = NULL;
                    }
                    break;
                }
                break;
            }
        }

        return TRUE;
    } // end function startup


    /**
     * beforeRender
     *
     * @param  CakeEvent $event
     * @return boolean
     */
    public function beforeRender(CakeEvent $event)
    {
        if (!BcUtil::isAdminSystem()) return TRUE;

        $Controller     = $event->subject();
        $controllerName = $Controller->name;
        $action         = $Controller->action;

        $loginUser = BcUtil::loginUser();
        $userGroup = $loginUser['UserGroup']['name'];

        $adminsName               = Configure::read('Operation.admin.adminsName');
        $allowedAdminAllOperation = Configure::read('Operation.admin.allowedAdminAllOperation');
        if (in_array($userGroup, $adminsName) && $allowedAdminAllOperation === TRUE) return TRUE;

        if (preg_match('/\Aadmin_/', $action)) {
            switch ($controllerName) {
            case 'Contents':
                switch ($action) {
                case 'admin_index':
                    $Operation = ClassRegistry::init('Operation.Operation');

                    $operationSite = $Operation->findOperationSites();
                    if (empty($operationSite)) $Controller->viewVars['datas'] = NULL;
                    break;

                case 'admin_ajax_contents_info':
                    $Operation = ClassRegistry::init('Operation.Operation');

                    $operationSite = $Operation->findOperationSites();
                    $site          = [];
                    if (!empty($operationSite) && is_array($operationSite)) {
                        foreach ($Controller->viewVars['sites'] as $i => $array) {
                            if (array_key_exists($array['Site']['id'], $operationSite)) $site[] = $array;
                        }
                    }

                    if (!empty($site)) {
                        $Controller->viewVars['sites'] = $site;
                    } else {
                        $Controller->viewVars['sites'] = [];
                        echo sprintf('<ul><li>%s</li></ul>', __d('baser', 'データが登録されていません。'));
                    }
                    break;
                }
                break;

            case 'Pages':
                if (!isset($Controller->passedArgs['content_id'])) break;

                $Content       = ClassRegistry::init('Content');
                $contentId     = $Controller->passedArgs['content_id'];
                $contentSiteId = $Content->findById($contentId, 'site_id')['Content']['site_id'];
                break;

            default:
                if (isset($Controller->request->params['Content']['site_id'])) $contentSiteId = $Controller->request->params['Content']['site_id'];
                break;
            }

            if (isset($contentSiteId)) {
                $Operation = ClassRegistry::init('Operation.Operation');

                $operationSite = $Operation->findOperationSites();
                if (!empty($operationSite) && is_array($operationSite)) {
                    $site = [];
                    foreach ($operationSite as $siteId => $array) {
                        $site[$siteId] = $array['Site']['title'];
                    }
                }
                if (!array_key_exists($contentSiteId, $site)) {
                    $Controller->setMessage(__d('baser', '指定されたページへのアクセスは許可されていません。'), TRUE);
				    $Controller->redirect($Controller->BcAuth->loginRedirect);
                }
            }
        }

        return TRUE;
    } // end function contentsBeforeRender


} // end class OperationControllerEventListener
