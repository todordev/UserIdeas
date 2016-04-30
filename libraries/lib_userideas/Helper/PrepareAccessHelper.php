<?php
/**
 * @package      Userideas
 * @subpackage   Helpers
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Helper;

use Prism\Helper\HelperInterface;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality to prepare the access to the items.
 *
 * @package      Userideas
 * @subpackage   Helpers
 */
class PrepareAccessHelper implements HelperInterface
{
    /**
     * User object.
     *
     * @var \JUser
     */
    protected $user;

    /**
     * Initialize the object.
     *
     * @param \JUser $user
     */
    public function __construct(\JUser $user)
    {
        $this->user = $user;
    }

    /**
     * Prepare the access levels of the items.
     *
     * @param array $data
     * @param array $options
     */
    public function handle(&$data, array $options = array())
    {
        if (count($data) > 0) {
            $userId = (int)$this->user->get('id');
            $guest  = $this->user->get('guest');
            $groups = $this->user->getAuthorisedViewLevels();

            foreach ($data as $key => $item) {
                // Compute the asset access permissions.
                // Technically guest could edit an article, but lets not check that to improve performance a little.
                if (!$guest) {
                    $asset = 'com_userideas.item.' . $item->id;

                    // Check general edit permission first.
                    if ($this->user->authorise('core.edit', $asset)) {
                        $item->params->set('access-edit', true);
                    } // Now check if edit.own is available.
                    elseif ($userId > 0 and $this->user->authorise('core.edit.own', $asset)) {
                        // Check for a valid user and that they are the owner.
                        if ($userId === (int)$item->user_id) {
                            $item->params->set('access-edit', true);
                        }
                    }
                }

                // If no access filter is set, the layout takes some responsibility for display of limited information.
                if (!$item->catid or !$item->category_access) {
                    $item->params->set('access-view', in_array((int)$item->access, $groups, true));
                } else {
                    $item->params->set('access-view', in_array((int)$item->access, $groups, true) and in_array((int)$item->category_access, $groups, true));
                }
            }
        }
    }
}
