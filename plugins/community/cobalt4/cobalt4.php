<?php
/**
 * @category           Plugins
 * @package            JomSocial
 * @copyright      (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license            GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . '/components/com_community/libraries/core.php');
require_once JPATH_ROOT . '/components/com_cobalt/library/php/helpers/helper.php';

if(!class_exists('plgCommunityCobalt4'))
{

	class plgCommunityCobalt4 extends CApplications
	{

		var $name = 'Cobalt';
		var $_name = 'Cobalt';

		public function onCommunityStreamRender($act)
		{
			$actor = CFactory::getUser($act->actor);

			$options = new JRegistry($act->params);
			$record  = ItemsStore::getRecord($act->cid);
			$type    = ItemsStore::getType($record->type_id);
			$section = ItemsStore::getSection($record->section_id);

			$msg = str_replace(
				array(
					 '[TYPE]',
					 '[TITLE]',
					 '[RECORD]',
					 '[SECTION]',
					 '[USER]',
					 '[RATE]'
				), array(
						$type->name,
						$record->title,
						JHtml::link(JRoute::_(Url::record($record, $type, $section)), $record->title),
						JHtml::link(JRoute::_(Url::records($section)), $section->name),
						'<a class="cStream-Author" href="' . CUrlHelper::userLink($actor->id) . '">' . $actor->getDisplayName() . '</a>',
						$options->get('new_vote')
				   ), JText::_($act->title));

			$stream           = new stdClass();
			$stream->message  = '';
			$stream->actor    = $actor;
			$stream->headline = $msg;

			return $stream;
		}

		function plgCommunityCobalt4(& $subject, $config)
		{
			parent::__construct($subject, $config);
			$api = JPATH_ROOT . '/components/com_cobalt/api.php';
			if(!JFile::exists($api))
			{
				JError::raiseError(500, 'Cobalt API not found. Trun off Cobalt - JomSocial integration plugin.');

				return;
			}

			require_once $api;
		}

		function onActivityContentDisplay($args)
		{
			return $args;
		}

		function onProfileDisplay()
		{
			$api = new CobaltApi();

			$user   = CFactory::getRequestUser();
			$userid = $user->id;

			$list = (array)$this->params->get('list_type');
			$api  = new CobaltApi();
			$tmpl = __DIR__ . '/tmpl/' . $this->params->get('view_tmpl', 'default.php');

			ob_start();
			include $tmpl;
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		function onSystemStart()
		{
			if(!$this->params->get('toolbar'))
			{
				return;
			}

			$user = JFactory::getUser();
			if(!$user->get('id'))
			{
				return;
			}

			$toolbar = CFactory::getToolbar();
			$section = ItemsStore::getSection($this->params->get('section_id'));
			$types   = $this->params->get('type_id', $section->params->get('general.type'));

			$groupname = strtoupper(get_class($this));
			$toolbar->addGroup($groupname, JText::_($this->params->get('toolbar_main_lbl', 'Cobalt')), JRoute::_(Url::records($section)));

			if($this->params->get('tshow_new', 1) && $types)
			{
				foreach($types as $type)
				{
					$type = ItemsStore::getType($type);
					$toolbar->addItem($groupname, 'ITEM_NEW'.$type->id, $this->_icon('new') . JText::sprintf($this->params->get('tlbl_new', 'Add new %s'), $type->name), JRoute::_(Url::add($section, $type, new stdClass())));
				}
			}
			if($this->params->get('tshow_created', 1))
			{
				$toolbar->addItem($groupname, 'ITEM_CREATED', $this->_icon('created') . JText::_($this->params->get('tlbl_created', 'Created')), JRoute::_(Url::user('created', $user->get('id'), $section->id)));
			}
			if($this->params->get('tshow_favorited', 1))
			{
				$toolbar->addItem($groupname, 'ITEM_FAVORIT', $this->_icon('favorited') . JText::_($this->params->get('tlbl_favorited', 'Bookmarked')), JRoute::_(Url::user('favorited', $user->get('id'), $section->id)));
			}
			if($this->params->get('tshow_rated', 1))
			{
				$toolbar->addItem($groupname, 'ITEM_RATED', $this->_icon('rated') . JText::_($this->params->get('tlbl_rated', 'Rated')), JRoute::_(Url::user('rated', $user->get('id'), $section->id)));
			}
			if($this->params->get('tshow_commented', 1))
			{
				$toolbar->addItem($groupname, 'ITEM_COMMENTED', $this->_icon('commented') . JText::_($this->params->get('tlbl_commented', 'Commented')), JRoute::_(Url::user('commented', $user->get('id'), $section->id)));
			}
			if($this->params->get('tshow_visited', 1))
			{
				$toolbar->addItem($groupname, 'ITEM_VISITED', $this->_icon('visited') . JText::_($this->params->get('tlbl_visited', 'Visited')), JRoute::_(Url::user('visited', $user->get('id'), $section->id)));
			}
			if($this->params->get('tshow_follow', 1))
			{
				$toolbar->addItem($groupname, 'ITEM_FOLLOW', $this->_icon('follow') . JText::_($this->params->get('tlbl_follow', 'Follow')), JRoute::_(Url::user('follow', $user->get('id'), $section->id)));
			}

		}

		function onWallDisplay($rows)
		{
			//var_dump($rows);
			return 'on wall';
		}

		function onAppDisplay()
		{
			return 'on app';
		}

		private function _icon($type)
		{
			if(!$this->params->get('icon_' . $type))
			{
				return;
			}

			return '<img align="absmiddle" src="' . JUri::root(TRUE) . '/media/mint/icons/16/' . $this->params->get('icon_' . $type) . '" > ';
		}
	}

}


