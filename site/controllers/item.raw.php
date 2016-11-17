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
 * @package        Userideas
 * @subpackage     Component
 * @since          2.5
 */
class UserideasControllerItem extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    UserideasModelItem    The model.
     * @since    1.5
     */
    public function getModel($name = 'Item', $prefix = 'UserideasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    /**
     * This method store user vote.
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function vote()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */
        
        $response = new Prism\Response\Json();

        $params = $app->getParams('com_userideas');

        // Check for disabled payment functionality
        if ($params->get('debug_item_adding_disabled', 0)) {
            $error = JText::_('COM_USERIDEAS_ERROR_VOTING_HAS_BEEN_DISABLED');
            JLog::add($error);
            return;
        }

        $requestMethod = $app->input->getMethod();
        if ('POST' !== $requestMethod) {
            $error = 'COM_USERIDEAS_ERROR_INVALID_REQUEST_METHOD (' . $requestMethod . '):' ."\n";
            $error .= 'INPUT: ' . var_export($app->input, true) . "\n";
            JLog::add($error);
            return;
        }

        $user = JFactory::getUser();
        $userId = $user->get('id');

        if (!$user->authorise('userideas.vote', 'com_userideas')) {
            $message = $userId ? JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION') : JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION_NOT_REGISTERED');
            $response
                ->setTitle(JText::_('COM_USERIDEAS_FAILURE'))
                ->setText($message)
                ->failure();

            echo $response;
            $app->close();
        }

        $data = array(
            'id'      => $app->input->post->getInt('id'),
            'user_id' => $userId
        );

        // Save data
        try {
            // Events
            $dispatcher = JEventDispatcher::getInstance();

            // Execute event onBeforeVote
            JPluginHelper::importPlugin('userideas');
            $results = $dispatcher->trigger('onBeforeVote', array('com_userideas.beforevote', &$data, $params));

            // Check for error.
            foreach ($results as $result) {
                $success = Joomla\Utilities\ArrayHelper::getValue($result, 'success', false, 'bool');

                if (false === $success) {
                    $message = Joomla\Utilities\ArrayHelper::getValue($result, 'message', JText::_('COM_USERIDEAS_VOTED_UNSUCCESSFULLY'));

                    $response
                        ->setTitle(JText::_('COM_USERIDEAS_FAILURE'))
                        ->setText($message)
                        ->failure();

                    echo $response;
                    $app->close();
                }
            }

            // Execute event onVote
            $dispatcher->trigger('onVote', array('com_userideas.vote', &$data, $params));

            // Execute event onAfterVote
            $dispatcher->trigger('onAfterVote', array('com_userideas.aftervote', &$data, $params));

        } catch (Exception $e) {
            JLog::add($e->getMessage());

            $response
                ->setTitle(JText::_('COM_USERIDEAS_FAILURE'))
                ->setText(JText::_('COM_USERIDEAS_ERROR_SYSTEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        $responseData = Joomla\Utilities\ArrayHelper::getValue($data, 'response_data', [], 'array');
        $userVotes    = Joomla\Utilities\ArrayHelper::getValue($responseData, 'user_votes', 0, 'int');
        $votes        = Joomla\Utilities\ArrayHelper::getValue($responseData, 'votes', 0, 'int');

        if ($votes > 0 or $userVotes > 0) {
            $data = array(
                'votes' => $votes
            );

            $response
                ->setTitle(JText::_('COM_USERIDEAS_SUCCESS'))
                ->setText(JText::plural('COM_USERIDEAS_VOTED_SUCCESSFULLY', $userVotes))
                ->setData($data)
                ->success();
        } else {
            $response
                ->setTitle(JText::_('COM_USERIDEAS_FAILURE'))
                ->setText(JText::_('COM_USERIDEAS_VOTED_UNSUCCESSFULLY'))
                ->failure();
        }

        echo $response;
        $app->close();
    }
}
