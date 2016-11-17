<?php
/**
 * @package      Userideas
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('Prism.init');
jimport('Userideas.init');

/**
 * This plugin initializes the job of the button
 * which is used for voting.
 *
 * @package        Userideas
 * @subpackage     Plugins
 */
class plgSystemUserideasVote extends JPlugin
{
    /**
     * Include a script that initialize vote buttons.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function onBeforeCompileHead()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return;
        }

        $document = JFactory::getDocument();
        /** @var $document JDocumentHTML */

        $type = $document->getType();
        if (strcmp('html', $type) !== 0) {
            return;
        }

        // Check for right extension.
        $option = $app->input->get('option');
        if (strcmp('com_userideas', $option) !== 0) {
            return;
        }

        // Check for view. The extensions will work only on view 'items'
        $allowedViews = array('items', 'details', 'category');
        $view         = $app->input->getCmd('view');
        if (!in_array($view, $allowedViews, true)) {
            return;
        }

        JHtml::_('Prism.ui.pnotify');
        JHtml::_('Prism.ui.joomlaHelper');
        JHtml::_('Userideas.loadVoteScript', $this->params->get('counter_button', Prism\Constants::NO));
    }
}
