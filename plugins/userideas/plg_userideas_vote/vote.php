<?php
/**
 * @package         UserIdeas
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/licenses/gpl-3.0.en.html
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
     * Hash used to recognize anonymous users.
     *
     * @var string
     */
    protected $hash;

    /**
     *
     * This method is triggered bofore user vote be stored.
     *
     * @param string                   $context
     * @param array                    $data
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
        if (strcmp('raw', $docType) !== 0) {
            return null;
        }

        if (strcmp('com_userideas.beforevote', $context) !== 0) {
            return null;
        }

        $numberOfVotes = abs($this->params->get('votes_per_item', 0));

        $itemId = Joomla\Utilities\ArrayHelper::getValue($data, 'id', 0, 'int');
        $userId = Joomla\Utilities\ArrayHelper::getValue($data, 'user_id', 0, 'int');

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('COUNT(*)')
            ->from($db->quoteName('#__uideas_votes', 'a'))
            ->where('a.item_id = ' . (int)$itemId);

        // Check if it is anonymous user.
        if (!$userId) {
            $hash = $this->generateHash();
            $query->where('a.hash = ' . $db->quote($hash));
        } else {
            $query->where('a.user_id = ' . (int)$userId);
        }

        $db->setQuery($query, 0, 1);
        $result = $db->loadResult();

        $votingAllowed = false;
        if (!$result or ($numberOfVotes === 0)) {
            $votingAllowed = true;
        }

        if ($numberOfVotes > 0 and ($result < $numberOfVotes)) {
            $votingAllowed = true;
        }

        if ($votingAllowed) { // User vote is not recorded. Return true
            $result = array(
                'success' => true
            );
        } else { // User vote is recorded. Return false.
            $this->loadLanguage();
            $result = array(
                'success' => false,
                'message' => JText::_('PLG_USERIDEAS_VOTE_YOU_HAVE_VOTED')
            );
        }
        
        return $result;
    }

    /**
     * Store user vote.
     *
     * @param string                   $context
     * @param array                    $data   This is a data about user and his vote
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

        if (strcmp('raw', $docType) !== 0) {
            return;
        }

        if (strcmp('com_userideas.vote', $context) !== 0) {
            return;
        }

        $itemId = (!empty($data['id'])) ? (int)$data['id'] : 0;
        $userId = (!empty($data['user_id'])) ? (int)$data['user_id'] : 0;

        // Save vote
        $item = new Userideas\Item\Item(JFactory::getDbo());
        $item->load($itemId);

        if (!$item->getId()) {
            return null;
        }

        $item->vote();

        // Add record to history table
        $history = new Userideas\Vote\Vote(JFactory::getDbo());

        if (!$userId) {
            $hash = $this->generateHash();
            $history->setHash($hash);
        } else {
            $history->setUserId($userId);
        }

        $history
            ->setItemId($itemId)
            ->setVotes(1)
            ->store();

        // Prepare response data
        $data['response_data'] = array(
            'user_votes' => 1,
            'votes'      => $item->getVotes()
        );
    }

    protected function generateHash()
    {
        if (!$this->hash) {

            // Get user IP address
            $app = JFactory::getApplication();
            $app->input->server->get('HTTP_CLIENT_IP');

            if ($app->input->server->get('HTTP_CLIENT_IP')) {
                $ip = $app->input->server->get('HTTP_CLIENT_IP');
            } elseif ($app->input->server->get('HTTP_X_FORWARDED_FOR')) {
                $ip = $app->input->server->get('HTTP_X_FORWARDED_FOR');
            } else {
                $ip = ($app->input->server->get('REMOTE_ADDR')) ?: '0.0.0.0';
            }

            // Validate the IP address.
            $ip = filter_var($ip, FILTER_VALIDATE_IP);
            $ip = ($ip === false) ? '0.0.0.0' : $ip;

            $this->hash = md5($ip);
        }

        return $this->hash;
    }
}
