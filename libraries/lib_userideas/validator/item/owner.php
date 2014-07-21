<?php
/**
 * @package      UserIdeas
 * @subpackage   Validators\Items
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for validation item owner.
 *
 * @package      UserIdeas
 * @subpackage   Validators\Items
 */
class UserIdeasValidatorItemOwner implements ITPrismValidatorInterface
{
    protected $db;
    protected $itemId;
    protected $userId;

    /**
     * Initialize the object.
     *
     * <code>
     * $itemId = 1;
     * $userId = 2;
     *
     * $owner = new UserIdeasValidatorItemOwner(JFactory::getDbo(), $itemId, $userId);
     * </code>
     *
     * @param JDatabaseDriver $db        Database object.
     * @param int             $itemId    Item ID.
     * @param int             $userId    User ID.
     */
    public function __construct(JDatabaseDriver $db, $itemId, $userId)
    {
        $this->db        = $db;
        $this->itemId    = $itemId;
        $this->userId    = $userId;
    }

    /**
     * Validate item owner.
     *
     * <code>
     * $itemId = 1;
     * $userId = 2;
     *
     * $owner = new UserIdeasValidatorItemOwner(JFactory::getDbo(), $itemId, $userId);
     * if(!$owner->isValid()) {
     * ......
     * }
     * </code>
     *
     * @return bool
     */
    public function isValid()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select("COUNT(*)")
            ->from($this->db->quoteName("#__uideas_items", "a"))
            ->where("a.id = " . (int)$this->itemId)
            ->where("a.user_id = " . (int)$this->userId);

        $this->db->setQuery($query, 0, 1);
        $result = $this->db->loadResult();

        return (bool)$result;
    }
}
