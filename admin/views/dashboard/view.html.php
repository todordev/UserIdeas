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

class UserideasViewDashboard extends JViewLegacy
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
    protected $prismVersion;
    protected $prismVersionLowerMessage;

    protected $sidebar;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->version = new Userideas\Version();

        // Load ITPrism library version
        if (!class_exists('Prism\\Version')) {
            $this->prismVersion = JText::_('COM_USERIDEAS_PRISM_LIBRARY_DOWNLOAD');
        } else {
            $prismVersion       = new Prism\Version();
            $this->prismVersion = $prismVersion->getShortVersion();

            if (version_compare($this->prismVersion, $this->version->requiredPrismVersion, '<')) {
                $this->prismVersionLowerMessage = JText::_('COM_USERIDEAS_PRISM_LIBRARY_LOWER_VERSION');
            }
        }

        $basic               = new Userideas\Statistic\Basic(JFactory::getDbo());
        $this->totalItems    = $basic->getTotalItems();
        $this->totalVotes    = $basic->getTotalVotes();
        $this->totalComments = $basic->getTotalComments();

        // Get popular items.
        $this->popular = new Userideas\Statistic\Items\Popular(JFactory::getDbo());
        $this->popular->load(array('limit' => 5));

        // Get most voted items.
        $this->mostVoted = new Userideas\Statistic\Items\MostVoted(JFactory::getDbo());
        $this->mostVoted->load(array('limit' => 5));

        // Get latest items.
        $this->latest = new Userideas\Statistic\Items\Latest(JFactory::getDbo());
        $this->latest->load(array('limit' => 5));

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
        UserideasHelper::addSubmenu($this->getName());

        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('COM_USERIDEAS_DASHBOARD'));

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
    }
}
