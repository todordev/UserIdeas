<?php
/**
 * @package      UserIdeas
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');
jimport('joomla.plugin.plugin');
jimport('Prism.init');
jimport('Userideas.init');

/**
 * This plugin initializes the job of the button
 * which is used for voting.
 *
 * @package        UserIdeas
 * @subpackage     Plugins
 */
class plgSystemUserIdeasVote extends JPlugin
{
    /**
     * Include a script that initialize vote buttons.
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

        // Check component enabled
        if (!JComponentHelper::isEnabled('com_userideas', true)) {
            return;
        }

        // Check for right extension.
        $option = $app->input->get('option');
        if (strcmp('com_userideas', $option) !== 0) {
            return null;
        }

        // Check for view. The extensions will work only on view 'items'
        $allowedViews = array('items', 'details', 'category');
        $view         = $app->input->getCmd('view');
        if (!in_array($view, $allowedViews, true)) {
            return;
        }

        JHtml::_('Prism.ui.joomlaHelper');

        $document->addScriptDeclaration('
            var userIdeas = {
                url: "'.JUri::root().'index.php?option=com_userideas&task=item.vote&format=raw",
                token: {
                    "'.JSession::getFormToken().'": 1
                }
            };
        ');

        $document->addScript('plugins/system/userideasvote/votebutton.js');
    }
}
