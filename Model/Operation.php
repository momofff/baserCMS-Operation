<?php

/**
 * [Model] Operation
 *
 * @category baserCMS Plugin
 * @package  Operation.Model
 * @author   Ryosuke Momoi
 * @link     https://github.com/momofff/baserCMS-Operation
 */

class Operation extends AppModel
{


    /**
     * Model
     *
     * @var string
     */
    public $name = 'Operation';


    /**
     * Plugin
     *
     * @var string
     */
    public $plugin = 'Operation';


    /**
     * findSites
     *
     * @param  array $args
     * @return mixed array|boolean
     */
    public function findSites($args = NULL)
    {
        $Site = ClassRegistry::init('Site');

        $options = [];
        $options['fields'] = ['*'];
        $options['joins'][] = [
            'type'  => (!empty($args['conditions'])) ? 'INNER' : 'LEFT',
            'table' => 'operations',
            'alias' => 'Operation',
            'conditions' => [
                'Site.id = Operation.site_id'
            ]
        ];
        $options['recursive'] = -1;
        $options['order'] = [
            'Site.id'
        ];
        if (!empty($args)) {
            foreach ($args as $key => $value) {
                $options[$key] = (!empty($options[$key])) ? array_merge($options[$key], $value) : $value;
            }
        }

        $siteData     = $Site->find('all', $options);
        $mainSiteData = $this->findMainSite($args);
        if (!empty($mainSiteData)) array_unshift($siteData, $mainSiteData);
        if (empty($siteData)) return FALSE;

        $site   = [];
        $siteId = [];
        foreach ($siteData as $i => $key) {
            if (!in_array($key['Site']['id'], $siteId)) {
                $tmpSite = [
                    'Site'      => $key['Site'],
                    'Operation' => ($key['Site']['id'] == $key['Operation']['site_id'])
                        ? [$key['Operation']['user_group_id'] => $key['Operation']]
                        : NULL
                ];
                $site[$key['Site']['id']] = $tmpSite;
                $siteId[] = $key['Site']['id'];
            } else {
                $site[$key['Site']['id']]['Operation'][$key['Operation']['user_group_id']] = $key['Operation'];
            }
        }

        return (!empty($site)) ? $site : FALSE;
    } // end function findSites


    /**
     * findOperationSites
     *
     * @param  int   $userGroupId
     * @return mixed array|boolean
     */
    public function findOperationSites($userGroupId = NULL)
    {
        if (empty($userGroupId)) {
            $loginUser   = BcUtil::loginUser();
            $userGroupId = $loginUser['user_group_id'];
        }

        $options = [];
        $options['conditions'] = [
            'OR' => [
                ['Operation.user_group_id' => $userGroupId],
                ['Operation.user_group_id' => 'ALL']
            ]
        ];

        return $this->findSites($options);
    } // end function findOperationSites


    /**
     * findMainSite
     *
     * @param  array $args
     * @return mixed array|boolean
     */
    public function findMainSite($args = NULL)
    {
        $Operation = ClassRegistry::init('Operation');

        $options = [];
        $options['conditions'] = [
            'site_id' => 0
        ];
        if (!empty($args)) {
            foreach ($args as $key => $value) {
                $options[$key] = (!empty($options[$key])) ? array_merge($options[$key], $value) : $value;
            }
        }

        $operationMainSite = $Operation->find('first', $options);
        if (empty($operationMainSite)) return FALSE;

        $SiteConfig = ClassRegistry::init('SiteConfig');

        $options = [];
        $options['conditions'] = [
            'name' => 'name'
        ];

        $mainSiteName = $SiteConfig->find('first', $options);
        if (empty($mainSiteName)) return FALSE;

        $mainSite = [
            'Site' => [
                'id'           => 0,
                'display_name' => $mainSiteName['SiteConfig']['value'],
                'title'        => $mainSiteName['SiteConfig']['value']
            ],
            'Operation' => $operationMainSite['Operation']
        ];

        return (!empty($mainSite)) ? $mainSite : FALSE;
    } // end function findMainSite


    /**
     * findUserGroups
     *
     * @param  array $args
     * @return mixed array|boolean
     */
    public function findUserGroups($args = NULL)
    {
        $UserGroup = ClassRegistry::init('UserGroup');

        $options = [];
        $options['fields'] = ['*'];
        $options['joins'][] = [
            'type'  => (!empty($args['conditions'])) ? 'INNER' : 'LEFT',
            'table' => 'operations',
            'alias' => 'Operation',
            'conditions' => [
                'UserGroup.id = Operation.user_group_id'
            ]
        ];
        $options['recursive'] = -1;
        $options['order'] = [
            'UserGroup.id'
        ];
        if (!empty($args)) {
            foreach ($args as $key => $value) {
                $options[$key] = (!empty($options[$key])) ? array_merge((array) $options[$key], $value) : $value;
            }
        }

        $userGroupData = $UserGroup->find('all', $options);
        if (empty($userGroupData)) return FALSE;

        $userGroup   = [];
        $userGroupId = [];
        foreach ($userGroupData as $i => $key) {
            if (!in_array($key['UserGroup']['id'], $userGroupId)) {
                $tmpUserGroup = [
                    'UserGroup' => $key['UserGroup'],
                    'Operation' => ($key['UserGroup']['id'] == $key['Operation']['user_group_id'])
                        ? [$key['Operation']['site_id'] => $key['Operation']]
                        : NULL
                ];
                $userGroup[$key['UserGroup']['id']] = $tmpUserGroup;
                $userGroupId[] = $key['UserGroup']['id'];
            } else {
                $userGroup[$key['UserGroup']['id']]['Operation'][$key['Operation']['site_id']] = $key['Operation'];
            }
        }

        return (!empty($userGroup)) ? $userGroup : FALSE;
    } // end function findUserGroups


    /**
     * findOperationUserGroups
     *
     * @param  int   $siteId
     * @return mixed array|boolean
     */
    public function findOperationUserGroups($siteId = NULL)
    {
        if (empty($siteId)) {
            App::import('Helper', 'Operation.Operation');
            $OperationHelper = new OperationHelper(new View(NULL));

            $siteId = $OperationHelper->getSiteId();
            if ($siteId === FALSE) return FALSE;
        }

        $options = [];
        $options['conditions'] = [
            'Operation.site_id' => $siteId
        ];

        return $this->findUserGroups($options);
    } // end function findOperationUserGroups


    /**
     * isOperationAllUserGroups
     *
     * @param  int     $siteId
     * @return boolean
     */
    public function isOperationAllUserGroups($siteId = NULL)
    {
        if (empty($siteId)) {
            App::import('Helper', 'Operation.Operation');
            $OperationHelper = new OperationHelper(new View(NULL));

            $siteId = $OperationHelper->getSiteId();
            if ($siteId === FALSE) return FALSE;
        }

        $Operation = ClassRegistry::init('Operation');

        $options = [];
        $options['conditions'] = [
            'site_id'       => $siteId,
            'user_group_id' => 'ALL'
        ];

        return (boolean) $Operation->find('first', $options);
    } // end function isOperationAllUserGroups


} // end class Operation
