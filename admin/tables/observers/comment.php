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
class UserideasObserverComment extends JTableObserver
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
        $commentId = (int)$this->table->get('id');
        if ($commentId > 0) {
            $db = $this->table->getDbo();

            // Delete attachment.
            $itemId     = (int)$this->table->get('item_id');
            $attachment = new \Userideas\Attachment\Attachment($db);
            $attachment->load(['item_id'=> $itemId, 'comment_id' => $commentId, 'source' => 'comment']);

            if ($attachment->getId() > 0) {
                $params = JComponentHelper::getParams('com_userideas');

                $filesystemHelper  = new Prism\Filesystem\Helper($params);
                $storageFilesystem = $filesystemHelper->getFilesystem();

                $filepath = $filesystemHelper->getMediaFolder($itemId, Userideas\Constants::ITEM_FOLDER) .'/'. $attachment->getFilename();

                if ($storageFilesystem->has($filepath)) {
                    $storageFilesystem->delete($filepath);
                }
            }
        }
    }
}
