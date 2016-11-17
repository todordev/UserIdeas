<?php
/**
 * @package         Userideas
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/licenses/gpl-3.0.en.html
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Userideas Vote Plugin
 *
 * @package        Userideas
 * @subpackage     Plugins
 */
class plgUserideasVote extends JPlugin
{
    /**
     * Hash used to recognize anonymous users.
     *
     * @var string
     */
    protected $hash;

    /**
     *
     * This method is triggered before user vote be stored.
     *
     * @param string                   $context
     * @param array                    $data
     * @param Joomla\Registry\Registry $params
     *
     * @throws \Exception
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return null|array
     */
    public function onBeforeVote($context, $data, $params)
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

        $numberOfVotes = $this->params->get('votes_per_item');

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
        if (!$result or ($numberOfVotes === null)) {
            $votingAllowed = true;
        }

        if (($numberOfVotes !== null and $numberOfVotes > 0) and ($result < $numberOfVotes)) {
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
     * @throws \Exception
     * @return  null|array
     */
    public function onVote($context, &$data, $params)
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

        if (strcmp('com_userideas.vote', $context) !== 0) {
            return null;
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
            $ip = Prism\Utilities\NetworkHelper::getIpAddress();

            // Validate the IP address.
            if ($ip === '') {
                $ip = '0.0.0.0';
            }

            $this->hash = md5($ip);
        }

        return $this->hash;
    }
}
