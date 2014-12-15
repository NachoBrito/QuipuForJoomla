<?php

/**
 * @copyright	Nacho Brito
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * Quipu Model
 */
class QuipuModelCustomer extends JModelAdmin {

    /**
     * 
     * @param type $joomla_id
     */
    public function getCustomerForCurrentUser($loadOrders = true) {
        $c = false;
        $user = JFactory::getUser();
        $joomla_id = $user->id;
        if ($joomla_id) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__quipu_customer');
            $query->where('user_id=' . $joomla_id);
            $db->setQuery($query);
            $res = $db->loadResult();
            if ($res) {
                $c = $this->getItem($res);
                if ($c) {
                    if ($loadOrders) {
                        $query = $db->getQuery(true);
                        $query->select('*');
                        $query->from('#__quipu_order');
                        $query->where('customer_id=' . $c->id);
                        $query->order('id desc');
                        $db->setQuery($query);
                        $c->orders = $db->loadObjectList();
                    }
                } else {
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_QUIPU_CUSTOMER_NOT_FOUND'));
                }
            }
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_QUIPU_NOT_LOGGED_IN'));
        }

        return $c;
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.7
     */
    public function getTable($type = 'Customer', $prefix = 'QuipuTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param	array	$data		Data for the form.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	mixed	A JForm object on success, false on failure
     * @since	1.7
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_quipu.customer', 'customer', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.7
     */
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_quipu.edit.customer.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

}