<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class UserIdeasViewDashboard extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    public $latest;
    public $popular;
    public $mostVoted;

    protected $option;

    protected $totalItems;
    protected $totalVotes;
    protected $totalComments;

    protected $version;
    protected $itprismVersion;

    protected $sidebar;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $this->version = new UserIdeasVersion();

        // Load ITPrism library version
        jimport("itprism.version");
        if (!class_exists("ITPrismVersion")) {
            $this->itprismVersion = JText::_("COM_USERIDEAS_ITPRISM_LIBRARY_DOWNLOAD");
        } else {
            $itprismVersion       = new ITPrismVersion();
            $this->itprismVersion = $itprismVersion->getShortVersion();
        }

        jimport("userideas.statistics.basic");
        $basic               = new UserIdeasStatisticsBasic(JFactory::getDbo());
        $this->totalItems    = $basic->getTotalItems();
        $this->totalVotes    = $basic->getTotalVotes();
        $this->totalComments = $basic->getTotalComments();

        // Get popular items.
        jimport("userideas.statistics.items.popular");
        $this->popular = new UserIdeasStatisticsItemsPopular(JFactory::getDbo());
        $this->popular->load(5);

        // Get most voted items.
        jimport("userideas.statistics.items.mostvoted");
        $this->mostVoted = new UserIdeasStatisticsItemsMostVoted(JFactory::getDbo());
        $this->mostVoted->load(5);

        // Get latest items.
        jimport("userideas.statistics.items.latest");
        $this->latest = new UserIdeasStatisticsItemsLatest(JFactory::getDbo());
        $this->latest->load(5);

        // Add submenu
        UserIdeasHelper::addSubmenu($this->getName());

        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_("COM_USERIDEAS_DASHBOARD"));

        JToolbarHelper::preferences('com_userideas');
        JToolbarHelper::divider();

        // Help button
        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Link', 'help', JText::_('JHELP'), JText::_('COM_USERIDEAS_HELP_URL'));
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_USERIDEAS_DASHBOARD'));

        $this->document->addStyleSheet("../media/" . $this->option . '/css/backend.style.css');
    }
}
