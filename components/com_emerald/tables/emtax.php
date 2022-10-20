<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class EmeraldTableEmTax extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__emerald_taxes', 'id', $db);
	}

	public function check()
	{
		if(empty($this->id))
		{
			$query = 'SELECT id FROM #__emerald_taxes WHERE country_id = "' . $this->country_id . '"';
			if($this->state_id)
			{
				$query .= ' AND state_id="' . $this->state_id . '"';
			}
			$this->_db->setQuery($query);
			$result = $this->_db->loadResult();
			if($result)
			{
				$this->setError(JText::_('E_TAX_EXISTS'));

				return FALSE;
			}
		}

		return parent::check();
	}
}