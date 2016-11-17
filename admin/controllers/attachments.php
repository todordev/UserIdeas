<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Userideas attachments controller class
 *
 * @package     Userideas
 * @subpackage  Components
 */
class UserideasControllerAttachments extends Prism\Controller\Admin
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return UserideasModelAttachment
     */
    public function getModel($name = 'Attachment', $prefix = 'UserideasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
