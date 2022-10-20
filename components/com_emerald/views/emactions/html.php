<?php
/**
 *
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 *
 */
defined('_JEXEC') or die('Restricted access');

class EmeraldViewsEmActionsHtml extends JViewHtml
{
	function render()
	{
		$this->model = new EmeraldModelsEmActions();
		$this->actions = $this->model->getActions($this->plan->id);

		return parent::render();
	}
	function getName()
	{
		return 'emactions';
	}
}