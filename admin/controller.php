<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Default controller
 *
 * @package        Userideas
 * @subpackage     Component
 */
class UserideasController extends JControllerLegacy
{
    public function display($cachable = false, $urlparams = array())
    {
        JHtml::_('Prism.ui.backendStyles');
        JHtml::_('Prism.ui.styles');
        
        $viewName = $this->input->getCmd('view', 'dashboard');
        $this->input->set('view', $viewName);

        parent::display();

        return $this;
    }
}
