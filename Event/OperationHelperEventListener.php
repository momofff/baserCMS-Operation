<?php

/**
 * [HelperEventListener] OperationHelperEventListener
 *
 * @category baserCMS Plugin
 * @package  Operation.Event
 * @author   Ryosuke Momoi
 * @link     https://github.com/momofff/baserCMS-Operation
 */

class OperationHelperEventListener extends BcHelperEventListener
{


    /**
     * Event
     *
     * @var array
     */
    public $events = [
        'Form.afterForm'
    ];


    /**
     * formAfterForm
     *
     * @param  CakeEvent $event
     * @return boolean
     */
    public function formAfterForm(CakeEvent $event)
    {
        if (!BcUtil::isAdminSystem()) return TRUE;

        $View = $event->subject();
        $originalPlugin  = $View->plugin;
        $View->helpers[] = 'Operation';
        $View->plugin    = 'Operation';

        $View->BcBaser->css('Operation.admin/style');
        $View->BcBaser->js('Operation.admin/script');

        switch ($event->data['id']) {
        case 'SiteAdminEditForm':
            $View->Operation->setCustomField();
            break;

        case 'SiteConfigFormForm':
            $View->Operation->setCustomField();
            break;
        }

        $View->plugin = $originalPlugin;
        return TRUE;
    } // end function formAfterForm


} // end class OperationHelperEventListener
