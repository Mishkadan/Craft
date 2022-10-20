<?php
/**
 *
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 *
 */
defined('_JEXEC') or die('Restricted access');

class EmeraldViewsEmRulesHtml extends JViewHtml
{
	function render()
	{
		$this->model = new EmeraldModelsEmRules();
		$this->rules = $this->model->getRules($this->plan->id);

		return parent::render();
	}
	function getName()
	{
		return 'emrules';
	}
}