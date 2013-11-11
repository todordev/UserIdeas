<?php
/**
 * @package      UserIdeas
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_PLATFORM') or die;

JLoader::register("UserIdeasTableEmail", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_userideas" . DIRECTORY_SEPARATOR . "tables" . DIRECTORY_SEPARATOR . "email.php");
JLoader::register("UserIdeasInterfaceTable", JPATH_LIBRARIES . DIRECTORY_SEPARATOR . "userideas" . DIRECTORY_SEPARATOR . "interface" . DIRECTORY_SEPARATOR . "table.php");

class UserIdeasEmail implements UserIdeasInterfaceTable {
    
    const MAIL_MODE_HTML    = true;
    const MAIL_MODE_PLAIN   = false;
    
    protected $table;
    protected static $instances = array();
    protected $replaceable = array(
        "{SITE_NAME}", 
        "{SITE_URL}", 
        "{ITEM_TITLE}", 
        "{ITEM_URL}", 
        "{SENDER_NAME}", 
        "{SENDER_EMAIL}", 
        "{RECIPIENT_NAME}", 
        "{RECIPIENT_EMAIL}"
    );

    public function __construct($id) {
        $this->table = new UserIdeasTableEmail(JFactory::getDbo());
        
        if (!empty($id)) {
            $this->load($id);
        }
    }

    /**
     *
     * Create an instance of the object and load data.
     *
     * @param number $id
     *
     * @return null|UserIdeasItem
     * 
     * <code>
     *
     * $itemId  = 1;
     * $item    = UserIdeasItem::getInstance($itemId);
     *
     * </code>
     *
     */
    public static function getInstance($id) {
        if (empty(self::$instances[$id])) {
            $item = new UserIdeasEmail($id);
            self::$instances[$id] = $item;
        }
        
        return self::$instances[$id];
    }

    public function load($keys, $reset = true) {
        $this->table->load($keys, $reset);
    }

    public function bind($src, $ignore = array()) {
        $this->table->bind($src, $ignore);
    }

    public function store($updateNulls = false) {
        $this->table->store($updateNulls);
    }

    public function getId() {
        return $this->table->id;
    }

    public function setSubject($subject) {
        $this->table->subject   = strip_tags($subject);
        return $this;
    }
    
    public function getSubject() {
        return strip_tags($this->table->subject);
    }

    /**
     * Return e-mail body.
     * 
     * @param string Mail type - plain ( plain text ) or html.
     * 
     * @return string
     * 
     * <code>
     * 
     * $itemId  = 1;
     * $item    = UserIdeasItem::getInstance($itemId);
     * 
     * $body    = $item->getBody("plain");
     * 
     * </code>
     */
    public function getBody($mode = "html") {
        
        $mode = JString::strtolower($mode);
        if(strcmp("plain", $mode) == 0) {
            $body = str_replace("<br />", "\n", $this->table->body);
            $body = strip_tags($body);
            
            return $body;
        } else {
            return $this->table->body;
        }
        
    }

    public function setSenderName($name) {
        $this->table->sender_name = $name;
        return $this;
    }

    public function getSenderName() {
        return $this->table->sender_name;
    }

    public function setSenderEmail($email) {
        $this->table->sender_email = $email;
        return $this;
    }

    public function getSenderEmail() {
        return $this->table->sender_email;
    }
    
    public function parse($data) {
        
        foreach($data as $key => $value) {
            
            // Prepare flag
            $search = "{".JString::strtoupper($key)."}";
            
            // Parse subject
            $this->table->subject = str_replace($search, $value, $this->table->subject);
            
            // Parse body
            $this->table->body = str_replace($search, $value, $this->table->body);
            
        }
        
    }
    
}