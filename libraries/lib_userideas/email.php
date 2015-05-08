<?php
/**
 * @package      UserIdeas
 * @subpackage   Emails
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for parsing email template.
 *
 * @package      UserIdeas
 * @subpackage   Emails
 */
class UserIdeasEmail
{
    protected $id = 0;
    protected $title = "";

    protected $subject = "";
    protected $body = "";
    protected $sender_name = "";
    protected $sender_email = "";

    /**
     * @var JDatabaseDriver
     */
    protected $db;

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

    /**
     * This method initializes the object.
     *
     * <code>
     * $subject = "My subject...";
     * $body    = "My body...";
     *
     * $email   = new UserIdeasEmail($subject, $body);
     * </code>
     *
     * @param string $subject Mail subject.
     * @param string $body    Mail body.
     */
    public function __construct($subject = "", $body = "")
    {
        $this->subject = $subject;
        $this->body    = $body;
    }

    /**
     * This method sets a database driver.
     *
     * <code>
     * $email   = new UserIdeasEmail();
     * $email->setDb(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     *
     * @return self
     */
    public function setDb(JDatabaseDriver $db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * This method loads data about e-mail template from a database.
     *
     * <code>
     * $emailId = 1;
     *
     * $email   = new UserIdeasEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     * </code>
     */
    public function load($id)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.title, a.subject, a.body, a.sender_name, a.sender_email")
            ->from($this->db->quoteName("#__uideas_emails", "a"))
            ->where("a.id = " . (int)$id);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) {
            $this->bind($result);
        }
    }

    /**
     * This method sets data to object parameters.
     *
     * <code>
     * $data = array(
     *      "subject"       => "My subject",
     *      "body"          => "My body"
     *      "sender_name"   => "John Dow"
     *      "sender_email"  => "john@mydomain.com"
     * );
     *
     * $email   = new UserIdeasEmail();
     * $email->bind($data);
     * </code>
     */
    public function bind($data)
    {

        $this->id    = JArrayHelper::getValue($data, "id", 0, "int");
        $this->title = JArrayHelper::getValue($data, "title");

        $this->setSubject(JArrayHelper::getValue($data, "subject"));
        $this->setBody(JArrayHelper::getValue($data, "body"));
        $this->setSenderName(JArrayHelper::getValue($data, "sender_name"));
        $this->setSenderEmail(JArrayHelper::getValue($data, "sender_email"));

        return $this;
    }

    /**
     * It returns an id of an email template.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email   = new UserIdeasEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     *
     * if (!$email->getId()) {
     * ....
     * }
     * </code>
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * This method sets a subject of the e-mail template.
     *
     * <code>
     * $subject  = "My subject...";
     *
     * $email   = new UserIdeasEmail();
     * $email->setSubject($subject);
     * </code>
     *
     * @param $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = strip_tags($subject);

        return $this;
    }

    /**
     * This method returns the subject of an email template.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email   = new UserIdeasEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $subject = $email->getSubject();
     * </code>
     *
     * @return string
     */
    public function getSubject()
    {
        return strip_tags($this->subject);
    }

    /**
     * This method sets a body of an email template.
     *
     * <code>
     * $body  = "My text...";
     *
     * $email   = new UserIdeasEmail();
     * $email->setBody($body);
     * </code>
     *
     * @param $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * This method returns email body in one of following formats.
     *
     * plain - it does not contain HTML code.
     * html  - it can contain HTML code.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email   = new UserIdeasEmail($itemId);
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $email->parse();
     *
     * $body    = $item->getBody("plain");
     * </code>
     *
     * @param string $mode Mail type - plain ( plain text ) or html.
     *
     * @return string
     */
    public function getBody($mode = "html")
    {
        $mode = JString::strtolower($mode);
        if (strcmp("plain", $mode) == 0) {
            $body = str_replace("<br />", "\n", $this->body);
            $body = strip_tags($body);

            return $body;
        } else {
            return $this->body;
        }
    }

    /**
     * It sets a name of a sender.
     *
     * <code>
     * $senderName  = "John Dow";
     *
     * $email   = new UserIdeasEmail();
     * $email->setSenderEmail($senderName);
     * </code>
     *
     * @param $name
     *
     * @return $this
     */
    public function setSenderName($name)
    {
        $this->sender_name = $name;

        return $this;
    }

    /**
     * It returns a sender name.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email   = new UserIdeasEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $senderName = $email->getSenderName();
     * </code>
     *
     * @return string
     */
    public function getSenderName()
    {
        return $this->sender_name;
    }

    /**
     * It sets a sender e-mail address.
     *
     * <code>
     * $senderEmail  = "john@gmail.com";
     *
     * $email   = new UserIdeasEmail();
     * $email->setSenderEmail($senderEmail);
     * </code>
     *
     * @param $email
     *
     * @return $this
     */
    public function setSenderEmail($email)
    {
        $this->sender_email = $email;

        return $this;
    }

    /**
     * It returns a sender e-mail address.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email   = new UserIdeasEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $senderEmail = $email->getSenderEmail();
     * </code>
     *
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->sender_email;
    }

    /**
     * This method parse the body of the e-mail.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email   = new UserIdeasEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $email->parse();
     * </code>
     *
     * @param $data
     *
     * @return self
     */
    public function parse($data)
    {
        foreach ($data as $key => $value) {

            // Prepare flag
            $search = "{" . JString::strtoupper($key) . "}";

            // Parse subject
            $this->subject = str_replace($search, $value, $this->subject);

            // Parse body
            $this->body = str_replace($search, $value, $this->body);
        }

        return $this;
    }
}
