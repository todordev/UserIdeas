<?php
/**
 * @package      UserIdeas
 * @subpackage   Constants
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * UserIdeas constants.
 *
 * @package      UserIdeas
 * @subpackage   Constants
 */
class UserIdeasConstants
{
    // States
    const PUBLISHED   = 1;
    const UNPUBLISHED = 0;
    const TRASHED     = -2;

    // Mail modes - html and plain text.
    const MAIL_MODE_HTML  = true;
    const MAIL_MODE_PLAIN = false;
}
