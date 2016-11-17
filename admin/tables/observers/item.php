<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_PLATFORM') or die;

/**
 * Abstract class defining methods that can be
 * implemented by an Observer class of a JTable class (which is an Observable).
 * Attaches $this Observer to the $table in the constructor.
 * The classes extending this class should not be instanciated directly, as they
 * are automatically instanciated by the JObserverMapper
 *
 * @package      Userideas
 * @subpackage   Component
 * @link         http://docs.joomla.org/JTableObserver
 * @since        3.1.2
 */
class UserideasObserverItem extends JTableObserver
{
    /**
     * The pattern for this table's TypeAlias
     *
     * @var    string
     * @since  3.1.2
     */
    protected $typeAliasPattern;

    /**
     * Creates the associated observer instance and attaches it to the $observableObject
     * $typeAlias can be of the form "{variableName}.type", automatically replacing {variableName} with table-instance variables variableName
     *
     * @param   JObservableInterface $observableObject The subject object to be observed
     * @param   array                $params           ( 'typeAlias' => $typeAlias )
     *
     * @return  UserideasObserverVote
     *
     * @since   3.1.2
     */
    public static function createObserver(JObservableInterface $observableObject, $params = array())
    {
        $observer = new self($observableObject);

        $observer->typeAliasPattern = Joomla\Utilities\ArrayHelper::getValue($params, 'typeAlias');

        return $observer;
    }

    /**
     * Pre-processor for $table->delete($pk)
     *
     * @param   mixed $pk An optional primary key value to delete.  If not set the instance property value is used.
     *
     * @throws  RuntimeException
     * @throws  UnexpectedValueException
     * @throws  InvalidArgumentException
     * @throws  \League\Flysystem\FileNotFoundException
     * @return  void
     *
     * @since   3.1.2
     */
    public function onAfterDelete($pk)
    {
        $itemId = (int)$this->table->get('id');
        if ($itemId > 0) {
            $db = $this->table->getDbo();

            // Delete comments.
            $query = $db->getQuery(true);
            $query
                ->delete($db->quoteName('#__uideas_comments'))
                ->where($db->quoteName('item_id') . '=' . (int)$itemId);

            $db->setQuery($query);
            $db->execute();

            // Delete votes.
            $query = $db->getQuery(true);
            $query
                ->delete($db->quoteName('#__uideas_votes'))
                ->where($db->quoteName('item_id') . '=' . (int)$itemId);

            $db->setQuery($query);
            $db->execute();

            // Delete attachments.
            $attachments = new Userideas\Attachment\Attachments($db);
            $attachments->load(array('item_id' => $itemId));

            if (count($attachments) > 0) {
                $params = JComponentHelper::getParams('com_userideas');

                $filesystemHelper  = new Prism\Filesystem\Helper($params);
                $storageFilesystem = $filesystemHelper->getFilesystem();

                foreach ($attachments as $attachment) {
                    $filepath = $filesystemHelper->getMediaFolder($itemId, Userideas\Constants::ITEM_FOLDER) .'/'. $attachment['filename'];
                    if ($storageFilesystem->has($filepath)) {
                        $storageFilesystem->delete($filepath);
                    }
                }
            }
        }
    }
}
