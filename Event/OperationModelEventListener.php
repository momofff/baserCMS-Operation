<?php

/**
 * [ModelEventListener] OperationModelEventListener
 *
 * @category baserCMS Plugin
 * @package  Operation.Event
 * @author   Ryosuke Momoi
 * @link     https://github.com/momofff/baserCMS-Operation
 */

class OperationModelEventListener extends BcModelEventListener
{


    /**
     * Event
     *
     * @var array
     */
    public $events = [
        'afterSave',
        'beforeFind'
    ];


    /**
     * afterSave
     *
     * @param  CakeEvent $event
     * @return boolean
     */
    public function afterSave(CakeEvent $event)
    {
        if (!BcUtil::isAdminSystem()) return TRUE;

        $Model     = $event->subject();
        $modelName = $Model->name;
        if (!in_array($modelName, ['Site', 'SiteConfig'])) return TRUE;

        if ($modelName == 'SiteConfig') $Model->data['Site']['id'] = 0;
        if (!empty($Model->data['Operation']) && isset($Model->data['Site']['id'])) {
            $inputData = $Model->data['Operation'];
        } else {
            return TRUE;
        }

        $Operation = ClassRegistry::init('Operation.Operation');
        $UserGroup = ClassRegistry::init('UserGroup');

        $operationUserGroup = (!empty($Operation->findOperationUserGroups())) ? $Operation->findOperationUserGroups() : [];
        if ($Operation->isOperationAllUserGroups() == TRUE) $operationUserGroup['all'] = 'ALL';

        $insertData        = [];
        $deleteUserGroupId = [];
        foreach ($inputData['user_group'] as $userGroupId => $value) {
            if (!$UserGroup->exists($userGroupId) && $userGroupId !== 'all') continue;
            if (!empty($value)) {
                if (!array_key_exists($userGroupId, $operationUserGroup)) {
                    $insertData[] = [
                        'site_id'       => $Model->data['Site']['id'],
                        'user_group_id' => $userGroupId
                    ];
                }
            } else {
                if (array_key_exists($userGroupId, $operationUserGroup)) {
                    $deleteUserGroupId[] = $userGroupId;
                }
            }
        }
        if (!empty($deleteUserGroupId)) {
            $deleteData = [
                'site_id'       => $Model->data['Site']['id'],
                'user_group_id' => $deleteUserGroupId
            ];
        }

        $insertResult = (!empty($insertData)) ? $Operation->saveAll($insertData) : TRUE;
        $deleteResult = (!empty($deleteData)) ? $Operation->deleteAll($deleteData) : TRUE;

        return ($insertResult == TRUE && $deleteResult == TRUE);
    } // end function afterSave


    /**
     * beforeFind
     *
     * @param  CakeEvent $event
     * @return boolean
     */
    public function beforeFind(CakeEvent $event)
    {
        if (!BcUtil::isAdminSystem()) return TRUE;

        $Model     = $event->subject();
        $modelName = $Model->name;
        if (!in_array($modelName, ['Content', 'Site', 'UploaderFile', 'Dblog'])) return TRUE;

        $loginUser = BcUtil::loginUser();
        $userGroup = $loginUser['UserGroup']['name'];

        $adminsName               = Configure::read('Operation.admin.adminsName');
        $allowedAdminAllOperation = Configure::read('Operation.admin.allowedAdminAllOperation');
        if (in_array($userGroup, $adminsName) && $allowedAdminAllOperation === TRUE) return TRUE;

        $conditions = $event->data[0]['conditions'];

        switch ($modelName) {
        case 'Content':
            if (array_key_exists('Content.deleted', $conditions)) {
                $Operation = ClassRegistry::init('Operation.Operation');

                $operationSite = (!empty($Operation->findOperationSites())) ? $Operation->findOperationSites() : [];
                $site          = array_keys($operationSite);
                $event->data[0]['conditions'][] = [
                    'Content.site_id' => $site
                ];
            }
            break;

        case 'UploaderFile':
            $allowedUploads = Configure::read('Operation.admin.allowedAllUserGroupUploads');
            if ($allowedUploads === TRUE) break;

            $event->data[0]['conditions'][] = [
                'User.user_group_id' => $loginUser['user_group_id']
            ];
            $event->data[0]['joins'][] = [
                'type'  => 'LEFT',
                'table' => 'users',
                'alias' => 'User',
                'conditions' => [
                    'UploaderFile.user_id = User.id'
                ]
            ];
            break;

        case 'Dblog':
            $allowedDblogs = Configure::read('Operation.admin.allowedAllUserGroupDblogs');
            if ($allowedDblogs === TRUE) break;

            $event->data[0]['conditions'][] = [
                'User.user_group_id' => $loginUser['user_group_id']
            ];
            break;
        }

        return TRUE;
    } // end function beforeFind


} // end class OperationModelEventListener
