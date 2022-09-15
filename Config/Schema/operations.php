<?php

/**
 * [Schema] operations
 *
 * @category baserCMS Plugin
 * @package  Operation.Config
 * @author   Ryosuke Momoi
 * @link     https://github.com/momofff/baserCMS-Operation
 */

class OperationsSchema extends CakeSchema
{


    /**
     * File
     *
     * @var string
     */
    public $file = 'operations.php';


    /**
     * Query
     *
     * @var array
     */
    public $operations = [
        'id'              => ['type' => 'integer',  'null' => FALSE, 'default' => NULL, 'length' => 8, 'key' => 'primary'],
        'site_id'         => ['type' => 'integer',  'null' => FALSE, 'default' => NULL, 'length' => 8],
        'user_group_id'   => ['type' => 'string',   'null' => FALSE, 'default' => NULL, 'length' => 255],
        'created'         => ['type' => 'datetime', 'null' => TRUE,  'default' => NULL],
        'modified'        => ['type' => 'datetime', 'null' => TRUE,  'default' => NULL],
        'indexes'         => ['PRIMARY' => ['column' => 'id']],
        'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
    ];


} // end class OperationsSchema
