<?php

/**
 * [Helper] OperationHelper
 *
 * @category baserCMS Plugin
 * @package  Operation.View.Helper
 * @author   Ryosuke Momoi
 * @link     https://github.com/momofff/baserCMS-Operation
 */

class OperationHelper extends AppHelper
{


    /**
     * PageModel
     *
     * @var Page
     */
    public $Operation = NULL;


    /**
     * data
     *
     * @var array
     */
    public $data = [];


    /**
     * Helper
     *
     * @var array
     */
    public $helpers = ['BcBaser', 'BcHtml'];


    /**
     * getSiteId
     *
     * @return mixed int|boolean
     */
    public function getSiteId()
    {
        $post = $this->request->params;
        if (!empty($post['controller'])) {
            if ($post['controller'] == 'sites' && !empty($post['pass'][0])) {
                $siteId = $post['pass'][0];
            } elseif ($post['controller'] == 'site_configs') {
                $siteId = 0;
            }
        }
        return (isset($siteId)) ? $siteId : FALSE;
    } // end function getSiteId


    /**
     * setCustomField
     *
     */
    public function setCustomField()
    {
        $Operation = ClassRegistry::init('Operation.Operation');

        $siteId = $this->getSiteId();
        $userGroup          = $Operation->findUserGroups();
        $operationUserGroup = $Operation->findOperationUserGroups();
        $operationAll       = $Operation->isOperationAllUserGroups();

        $allUserGroupOption = [
            'type'    => 'radio',
            'options' => [
                [
                    'name'  => __d('baser', '全ユーザーグループ'),
                    'value' => 'ALL',
                    'class' => 'radio_user_group_all'
                ],
                [
                    'name'  => __d('baser', '特定のユーザーグループ'),
                    'value' => 0,
                    'class' => 'radio_user_group_any'
                ]
            ]
        ];
        $all = ($operationAll === TRUE) ? 0 : 1;
        $allUserGroupOption['options'][$all][] = 'checked';

        $adminsName               = Configure::read('Operation.admin.adminsName');
        $allowedAdminAllOperation = Configure::read('Operation.admin.allowedAdminAllOperation');

        $anyUserGroupOption = [];
        foreach ($userGroup as $userGroupId => $array) {
            $isChecked   = (!empty($array['Operation'][$siteId])) ? 'checked' : NULL;
            $isDisabled  = (in_array($array['UserGroup']['name'], $adminsName) && $allowedAdminAllOperation === TRUE) ? 'disabled' : NULL;
            $anyUserGroupOption[] = [
                'option' => [
                    'type'  => 'checkbox',
                    'value' => 1,
                    $isChecked,
                    $isDisabled
                ],
                'name'   => 'Operation.user_group.'.$array['UserGroup']['id'],
                'label'  => $array['UserGroup']['title']
            ];

        }

        $data = [
            'op' => [
                'all_user_group' => $allUserGroupOption,
                'any_user_group' => $anyUserGroupOption
            ]
        ];

        $this->BcBaser->element('sites/form', $data);
        return;
    } // end function setCustomField


} // end class OperationHelper
