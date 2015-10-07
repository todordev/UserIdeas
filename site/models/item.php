<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class UserIdeasModelItem extends JModelItem
{
    protected $item;

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $value = $app->input->getInt('id');
        $this->setState($this->getName() . '.id', $value);

        // Load the component parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);

    }

    /**
     * Method to get an object.
     *
     * @param    integer  $id  The id of the object to get.
     *
     * @return    mixed    Object on success, false on failure.
     */
    public function getItem($id = null)
    {
        if ($this->item === null) {

            if ($id === null) {
                $id = $this->getState($this->getName() . '.id');
            }

            // Get a level row instance.
            $table = JTable::getInstance('Item', 'UserIdeasTable');

            // Attempt to load the row.
            if ($table->load($id)) {

                if (!$table->get('published')) {
                    return $this->item;
                }

                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(true);
                $this->item = Joomla\Utilities\ArrayHelper::toObject($properties, 'JObject');
            }
        }

        return $this->item;
    }
}
