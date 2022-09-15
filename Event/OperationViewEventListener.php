<?php

/**
 * [ViewEventListener] OperationViewEventListener
 *
 * @category baserCMS Plugin
 * @package  Operation.Event
 * @author   Ryosuke Momoi
 * @link     https://github.com/momofff/baserCMS-Operation
 */

class OperationViewEventListener extends BcViewEventListener
{


    /**
     * Event
     *
     * @var array
     */
    public $events = [
        'beforeElement'
    ];


    /**
     * beforeElement
     *
     * @param  CakeEvent $event
     * @return boolean
     */
    public function beforeElement(CakeEvent $event)
    {
        if (!BcUtil::isAdminSystem()) return TRUE;

        $View = $event->subject();

        $loginUser = BcUtil::loginUser();
        $userGroup = $loginUser['UserGroup']['name'];

        $adminsName               = Configure::read('Operation.admin.adminsName');
        $allowedAdminAllOperation = Configure::read('Operation.admin.allowedAdminAllOperation');

        $action  = $View->request->params['action'];
        $element = $event->data['name'];

        if (preg_match('/\Aadmin_/', $action)) {
            switch ($element) {
            case 'contents/index_view_setting':
                if (in_array($userGroup, $adminsName) && $allowedAdminAllOperation === TRUE) break;

                $Operation = ClassRegistry::init('Operation');
                $operationSite = $Operation->findOperationSites();

                $site = [];
                if (!empty($operationSite) && is_array($operationSite)) {
                    foreach ($operationSite as $siteId => $array) {
                        $site[$siteId] = $array['Site']['title'];
                    }
                }
                $View->viewVars['sites'] = $site;
                break;
            }
        }

        return TRUE;
    } // end function beforeElement


} // end class OperationViewEventListener
