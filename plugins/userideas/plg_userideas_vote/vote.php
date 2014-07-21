<?php
/**
 * @package         UserIdeas
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * UserIdeas Vote Plugin
 *
 * @package        UserIdeas
 * @subpackage     Plugins
 */
class plgUserIdeasVote extends JPlugin
{
    /**
     *
     * This method is triggered bofore user vote be stored.
     *
     * @param string    $context
     * @param array     $data
     * @param Joomla\Registry\Registry $params
     *
     * @return null|array
     */
    public function onBeforeVote($context, &$data, $params)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return null;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();
        if (strcmp("raw", $docType) != 0) {
            return null;
        }

        if (strcmp("com_userideas.beforevote", $context) != 0) {
            return null;
        }

        $itemId = JArrayHelper::getValue($data, "id", 0, "int");
        $userId = JArrayHelper::getValue($data, "user_id", 0, "int");

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select("COUNT(*)")
            ->from($db->quoteName("#__uideas_votes", "a"))
            ->where("a.item_id = " . (int)$itemId)
            ->where("a.user_id = " . (int)$userId);

        $db->setQuery($query, 0, 1);
        $result = $db->loadResult();

        if (!$result) { // User vote is not recorded. Return true

            $result = array(
                "success" => true
            );

        } else { // User vote is recorded. Return false.

            $this->loadLanguage();
            $result = array(
                "success" => false,
                "message" => JText::_("PLG_USERIDEAS_VOTE_YOU_HAVE_VOTED")
            );

        }

        return $result;

    }

    /**
     * Store user vote.
     *
     * @param string    $context
     * @param array     $data   This is a data about user and his vote
     * @param Joomla\Registry\Registry $params The parameters of the component
     *
     * @return  null|array
     */
    public function onVote($context, &$data, $params)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return;
        }

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        // Check document type
        $docType = $doc->getType();

        if (strcmp("raw", $docType) != 0) {
            return;
        }

        if (strcmp("com_userideas.vote", $context) != 0) {
            return;
        }

        $itemId = JArrayHelper::getValue($data, "id", 0, "int");
        $userId = JArrayHelper::getValue($data, "user_id", 0, "int");

        // Save vote
        jimport("userideas.item");

        $item = new UserIdeasItem(JFactory::getDbo());
        $item->load($itemId);

        if (!$item->getId()) {
            return null;
        }

        $item->vote();

        // Add record to history table
        jimport("userideas.vote");
        $history = new UserIdeasVote(JFactory::getDbo());

        $history
            ->setUserId($userId)
            ->setItemId($itemId)
            ->setVotes(1)
            ->store();

        // Prepare response data
        $data["response_data"] = array(
            "user_votes" => 1,
            "votes"      => $item->getVotes()
        );

    }
}
