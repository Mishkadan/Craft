<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_joompush
 * @copyright  Copyright (c) 2017 Weppsol Technologies. All rights reserved.
 * @license    GNU GENERAL PUBLIC LICENSE V2 OR LATER
 * Joompush is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.controller');


/**
 * Weppsol Installer
 *
 * @since  1.0.0
 */
class Pkg_JoompushInstallerScript
{
	/** @var array The list of extra modules and plugins to install */
	private $oldversion = "";

	private $installation_queue = array('modules' => array(),
										'plugins' => array('system' => array('joompush' => 1)),
										'libraries' => array());

	private $uninstall_queue = array('modules' => array(),
									 'plugins' => array('system' => array('joompush' => 1)),
									 'libraries' => array());

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   JInstaller  $type    type
	 * @param   JInstaller  $parent  parent
	 *
	 * @return void
	 */
	public function preflight($type, $parent)
	{
	}

	/**
	 * method to install the component
	 *
	 * @param   JInstaller  $parent  parent
	 *
	 * @return  void
	 */
	public function install($parent)
	{
		// $parent is the class calling this method
	}

	/**
	 * Runs after install, update or discover_update
	 *
	 * @param   string      $type    install, update or discover_update
	 * @param   JInstaller  $parent  parent
	 *
	 * @return  void
	 */
	public function postflight($type, $parent)
	{
		// Install subextensions
		$status = $this->_installSubextensions($parent);

		// Show the post-installation page
		$this->_renderPostInstallation($status, $parent);
	}

	/**
	 * Renders the post-installation message
	 *
	 * @param   JInstaller  $status         parent
	 * @param   JInstaller  $parent         parent
	 *
	 * @return  void
	 */
	private function _renderPostInstallation($status, $parent)
	{
		$document = JFactory::getDocument();
?>
	   <?php
		$rows = 1;
?>
	   <link rel="stylesheet" type="text/css" href="<?php
		echo JURI::root() . 'media/jui/css/bootstrap.min.css';
?>"/>
		<div class="" >

		<table class="table-condensed table" width="100%">
			<thead>
				<tr class="row1">
					<th class="title" colspan="2">Extension</th>
					<th>Status</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tfoot>
			<tbody>
				<tr class="row2">
					<td class="key" colspan="2"><strong>JoomPush component</strong></td>
					<td><strong style="color: green">Installed</strong></td>
				</tr>

<?php
		if (count($status->modules))
		{
?>
			   <tr class="row1">
					<th>Module</th>
					<th>Client</th>
					<th></th>
					</tr>
			<?php
			foreach ($status->modules as $module)
			{
?>
			   <tr class="row2">
					<td class="key"><?php
				echo ucfirst($module['name']);
?></td>
					<td class="key"><?php
				echo ucfirst($module['client']);
?></td>
					<td><strong style="color: <?php
				echo $res = ($module['result']) ? "green" : "red";
?>"><?php
				echo $res = ($module['result']) ? 'Installed' : 'Not installed';
?></strong>
				<?php
				// If installed then only show msg
				if (!empty($module['result']))
				{
					echo $mstat = ($module['status']?"<span class=\"label label-success\">Enabled</span>":"<span class=\"label label-important\">Disabled</span>");
				}
?>

					</td>
				</tr>
				<?php
			}
?>
			   <?php
		}
?>
			   <?php
		if (count($status->plugins))
		{
?>
			   <tr class="row1">
					<th colspan="2">Plugin</th>
			<!--        <th>Group</th> -->
					<th></th>
				</tr>
				<?php
			$oldplugingroup = "";

			foreach ($status->plugins as $plugin)
			{
				if ($oldplugingroup != $plugin['group'])
				{
					$oldplugingroup = $plugin['group'];
?>
				   <tr class="row0">
						<th colspan="2"><strong><?php
					echo ucfirst($oldplugingroup) . " Plugins";
?></strong></th>
						<th></th>
				<!--        <td></td> -->
					</tr>
				<?php
				}

?>
			   <tr class="row2">
					<td colspan="2" class="key"><?php
				echo ucfirst($plugin['name']);
?></td>

				<td><strong style="color: <?php
				echo $tdcolor = ($plugin['result']) ? "green" : "red";
?>"><?php
				echo $tdresult = ($plugin['result']) ? 'Installed' : 'Not installed';
?></strong>
					<?php
				if (!empty($plugin['result']))
				{
					echo $pstat = ($plugin['status']?"<span class=\"label label-success\">Enabled</span>":"<span class=\"label label-important\">Disabled</span>");
				}
?>
				   </td>
				</tr>
				<?php
			}
?>
			   <?php
		}
?>

				<!-- LIB INSTALL-->
				<?php
		if (count($status->libraries))
		{
?>
			   <tr class="row1">
					<th>Library</th>
					<th></th>
					<th></th>
					</tr>
				<?php
			foreach ($status->libraries as $libraries)
			{
?>
			   <tr class="row2">
					<td class="key"><?php
				echo ucfirst($libraries['name']);
?></td>
					<td class="key"></td>
					<td><strong style="color: <?php
				echo $libraries['result'] ? "green" : "red";
?>"><?php
				echo $libraries['result'] ? 'Installed' : 'Not installed';
?></strong>
					<?php

				// If installed then only show msg
				if (!empty($libraries['result']))
				{

				}
?>

					</td>
				</tr>
				<?php
			}
		}
?>

			</tbody>
		</table>
		</div> <!-- end akeeba bootstrap -->
		<hr>
		<div>
			<div class="alert alert-success">
				Please refer to the JoomPush <a href="https://weppsol.com/support/documentation/joompush" target="_blank">documentation</a> for setup and configuration instructions. 
			</div>
		</div>

		<?php
	}

	/**
	 * Installs subextensions (modules, plugins) bundled with the main extension
	 *
	 * @param   JInstaller  $parent  parent
	 *
	 * @return  JObject The subextension installation status
	 */
	private function _installSubextensions($parent)
	{
		$src = $parent->getParent()->getPath('source');
		$db  = JFactory::getDbo();

		$status          = new JObject;
		$status->modules = array();
		$status->plugins = array();

		// Modules installation

		if (count($this->installation_queue['modules']))
		{
			foreach ($this->installation_queue['modules'] as $folder => $modules)
			{
				if (count($modules))
				{
					foreach ($modules as $module => $modulePreferences)
					{
						// Install the module
						if (empty($folder))
						{
							$folder = 'site';
						}

						$path = "$src/modules/$folder/$module";

						// If not dir
						if (!is_dir($path))
						{
							$path = "$src/modules/$folder/mod_$module";
						}

						if (!is_dir($path))
						{
							$path = "$src/modules/$module";
						}

						if (!is_dir($path))
						{
							$path = "$src/modules/mod_$module";
						}

						if (!is_dir($path))
						{
							$fortest = '';

							// Continue;
						}

						// Was the module already installed?
						$sql = $db->getQuery(true)->select('COUNT(*)')->from('#__modules')->where($db->qn('module') . ' = ' . $db->q('mod_' . $module));
						$db->setQuery($sql);

						$count = $db->loadResult();

						$installer         = new JInstaller;
						$result            = $installer->install($path);

						if ($count)
						{
							$query = $db->getQuery(true);
							$query->select('published')->from($db->qn('#__modules'))->where($db->qn('module') . ' = ' . $db->q('mod_' . $module));
							$db->setQuery($query);
							$checkifpublished = $db->loadColumn();

							$mod_published  = 0;

							if (in_array('1', $checkifpublished))
							{
								$mod_published = 1;
							}

							$status->modules[] = array(
							'name' => $module,
							'client' => $folder,
							'result' => $result,
							'status' => $mod_published
							);
						}
						else
						{
							$status->modules[] = array(
							'name' => $module,
							'client' => $folder,
							'result' => $result,
							'status' => $modulePreferences[1]
							);
						}

						// Modify where it's published and its published state
						if (!$count)
						{
							// A. Position and state
							list($modulePosition, $modulePublished, $moduleshowtitle) = $modulePreferences;

							if ($modulePosition == 'cpanel')
							{
								$modulePosition = 'icon';
							}

							$sql = $db->getQuery(true)->update($db->qn('#__modules'))
							->set($db->qn('position') . ' = ' . $db->q($modulePosition))
							->set($db->qn('showtitle') . ' = ' . $db->q($moduleshowtitle))
							->where($db->qn('module') . ' = ' . $db->q('mod_' . $module));

							if ($modulePublished)
							{
								$sql->set($db->qn('published') . ' = ' . $db->q('1'));
							}

							$db->setQuery($sql);
							$db->query();

							// B. Change the ordering of back-end modules to 1 + max ordering
							if ($folder == 'admin')
							{
								$query = $db->getQuery(true);
								$query->select('MAX(' . $db->qn('ordering') . ')')->from($db->qn('#__modules'))->where($db->qn('position') . '=' . $db->q($modulePosition));
								$db->setQuery($query);
								$position = $db->loadResult();
								$position++;

								$query = $db->getQuery(true);
								$query->update($db->qn('#__modules'))
								->set($db->qn('ordering') . ' = ' . $db->q($position))
								->where($db->qn('module') . ' = ' . $db->q('mod_' . $module));
								$db->setQuery($query);
								$db->query();
							}

							// C. Link to all pages
							$query = $db->getQuery(true);
							$query->select('id')->from($db->qn('#__modules'))->where($db->qn('module') . ' = ' . $db->q('mod_' . $module));
							$db->setQuery($query);
							$moduleid = $db->loadResult();

							$query = $db->getQuery(true);
							$query->select('*')->from($db->qn('#__modules_menu'))->where($db->qn('moduleid') . ' = ' . $db->q($moduleid));
							$db->setQuery($query);
							$assignments = $db->loadObjectList();
							$isAssigned  = !empty($assignments);

							if (!$isAssigned)
							{
								$o = (object) array(
									'moduleid' => $moduleid,
									'menuid' => 0
								);
								$db->insertObject('#__modules_menu', $o);
							}
						}
					}
				}
			}
		}

		// Plugins installation
		if (count($this->installation_queue['plugins']))
		{
			foreach ($this->installation_queue['plugins'] as $folder => $plugins)
			{
				if (count($plugins))
				{
					foreach ($plugins as $plugin => $published)
					{
						$path = "$src/plugins/$folder/$plugin";

						if (!is_dir($path))
						{
							$path = "$src/plugins/$folder/plg_$plugin";
						}

						if (!is_dir($path))
						{
							$path = "$src/plugins/$plugin";
						}

						if (!is_dir($path))
						{
							$path = "$src/plugins/plg_$plugin";
						}

						if (!is_dir($path))
						{
							continue;
						}

						// Was the plugin already installed?
						$query = $db->getQuery(true)->select('COUNT(*)')
						->from($db->qn('#__extensions'))
						->where('( ' . ($db->qn('name') . ' = ' . $db->q($plugin)) . ' OR ' . ($db->qn('element') . ' = ' . $db->q($plugin)) . ' )')
						->where($db->qn('folder') . ' = ' . $db->q($folder));
						$db->setQuery($query);
						$count = $db->loadResult();

						$installer = new JInstaller;
						$result    = $installer->install($path);

						if ($count)
						{
							// Was the plugin already installed?
							$query = $db->getQuery(true)->select('enabled')
							->from($db->qn('#__extensions'))
							->where('( ' . ($db->qn('name') . ' = ' . $db->q($plugin)) . ' OR ' . ($db->qn('element') . ' = ' . $db->q($plugin)) . ' )')
							->where($db->qn('folder') . ' = ' . $db->q($folder));
							$db->setQuery($query);
							$enabled = $db->loadResult();

							$status->plugins[] = array(
								'name' => $plugin,
								'group' => $folder,
								'result' => $result,
								'status' => $enabled
							);
						}
						else
						{
							$status->plugins[] = array(
								'name' => $plugin,
								'group' => $folder,
								'result' => $result,
								'status' => $published
							);
						}

						if ($published && !$count)
						{
							$query = $db->getQuery(true)
							->update($db->qn('#__extensions'))
							->set($db->qn('enabled') . ' = ' . $db->q('1'))
							->where('( ' . ($db->qn('name') . ' = ' . $db->q($plugin)) . ' OR ' . ($db->qn('element') . ' = ' . $db->q($plugin)) . ' )')
							->where($db->qn('folder') . ' = ' . $db->q($folder));
							$db->setQuery($query);
							$db->query();
						}
					}
				}
			}
		}

		// Library installation
		if (count($this->installation_queue['libraries']))
		{
			foreach ($this->installation_queue['libraries'] as $folder => $status1)
			{
				$path = "$src/libraries/$folder";

				$query = $db->getQuery(true)->select('COUNT(*)')
				->from($db->qn('#__extensions'))
				->where('( ' . ($db->qn('name') . ' = ' . $db->q($folder)) . ' OR ' . ($db->qn('element') . ' = ' . $db->q($folder)) . ' )')
				->where($db->qn('folder') . ' = ' . $db->q($folder));
				$db->setQuery($query);
				$count = $db->loadResult();

				$installer = new JInstaller;
				$result    = $installer->install($path);

				$status->libraries[] = array(
					'name' => $folder,
					'group' => $folder,
					'result' => $result,
					'status' => $status1
				);

				if ($published && !$count)
				{
					$query = $db->getQuery(true)
					->update($db->qn('#__extensions'))
					->set($db->qn('enabled') . ' = ' . $db->q('1'))
					->where('( ' . ($db->qn('name') . ' = ' . $db->q($folder)) . ' OR ' . ($db->qn('element') . ' = ' . $db->q($folder)) . ' )')
					->where($db->qn('folder') . ' = ' . $db->q($folder));
					$db->setQuery($query);
					$db->query();
				}
			}
		}

		return $status;
	}

	/**
	 * Uninstalls subextensions (modules, plugins) bundled with the main extension
	 *
	 * @param   JInstaller  $parent  parent
	 *
	 * @return  JObject The subextension uninstallation status
	 */
	private function _uninstallSubextensions($parent)
	{
		jimport('joomla.installer.installer');

		$db = JFactory::getDBO();

		$status          = new JObject;
		$status->modules = array();
		$status->plugins = array();

		$src = $parent->getParent()->getPath('source');

		// Modules uninstallation
		if (count($this->uninstall_queue['modules']))
		{
			foreach ($this->uninstall_queue['modules'] as $folder => $modules)
			{
				if (count($modules))
				{
					foreach ($modules as $module => $modulePreferences)
					{
						// Find the module ID
						$sql = $db->getQuery(true)->select($db->qn('extension_id'))
						->from($db->qn('#__extensions'))
						->where($db->qn('element') . ' = ' . $db->q('mod_' . $module))
						->where($db->qn('type') . ' = ' . $db->q('module'));
						$db->setQuery($sql);
						$id = $db->loadResult();

						// Uninstall the module
						if ($id)
						{
							$installer         = new JInstaller;
							$result            = $installer->uninstall('module', $id, 1);
							$status->modules[] = array(
								'name' => 'mod_' . $module,
								'client' => $folder,
								'result' => $result
							);
						}
					}
				}
			}
		}

		// Plugins uninstallation
		if (count($this->uninstall_queue['plugins']))
		{
			foreach ($this->uninstall_queue['plugins'] as $folder => $plugins)
			{
				if (count($plugins))
				{
					foreach ($plugins as $plugin => $published)
					{
						$sql = $db->getQuery(true)->select($db->qn('extension_id'))
						->from($db->qn('#__extensions'))
						->where($db->qn('type') . ' = ' . $db->q('plugin'))
						->where($db->qn('element') . ' = ' . $db->q($plugin))
						->where($db->qn('folder') . ' = ' . $db->q($folder));
						$db->setQuery($sql);

						$id = $db->loadResult();

						if ($id)
						{
							$installer         = new JInstaller;
							$result            = $installer->uninstall('plugin', $id);
							$status->plugins[] = array(
								'name' => 'plg_' . $plugin,
								'group' => $folder,
								'result' => $result
							);
						}
					}
				}
			}
		}

		return $status;
	}

	/**
	 * _renderPostUninstallation
	 *
	 * @param   STRING  $status  status of installed extensions
	 * @param   ARRAY   $parent  parent item
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	private function _renderPostUninstallation($status, $parent)
	{
?>
	   <?php
		$rows = 0;
?>
	   <h2><?php
		echo JText::_('Joompush Uninstallation Status');
?></h2>
		<table class="adminlist">
			<thead>
				<tr>
					<th class="title" colspan="2"><?php
		echo JText::_('Extension');
?></th>
					<th width="30%"><?php
		echo JText::_('Status');
?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tfoot>
			<tbody>
				<tr class="row0">
					<td class="key" colspan="2"><?php
		echo 'JoomPush ' . JText::_('Component');
?></td>
					<td><strong style="color: green"><?php
		echo JText::_('Removed');
?></strong></td>
				</tr>
				<?php
		if (count($status->modules))
		{
?>
			   <tr>
					<th><?php
			echo JText::_('Module');
?></th>
					<th><?php
			echo JText::_('Client');
?></th>
					<th></th>
				</tr>
				<?php
			foreach ($status->modules as $module)
			{
?>
			   <tr class="row<?php echo ++$rows % 2;?>">
					<td class="key"><?php echo $module['name'];?></td>
					<td class="key"><?php echo ucfirst($module['client']);?></td>
					<td>
						<strong style="color: <?php echo $module['result'] ? "green" : "red";?>">
							<?php echo $module['result'] ? JText::_('Removed') : JText::_('Not removed');?>
						</strong>
					</td>
				</tr>
				<?php
			}
?>
			   <?php
		}
?>
			   <?php
		if (count($status->plugins))
		{
?>
			   <tr>
					<th><?php
			echo JText::_('Plugin');
?></th>
					<th><?php
			echo JText::_('Group');
?></th>
					<th></th>
				</tr>
				<?php
			foreach ($status->plugins as $plugin)
			{
?>
			   <tr class="row<?php
				echo ++$rows % 2;
?>">
					<td class="key"><?php
				echo ucfirst($plugin['name']);
?></td>
					<td class="key"><?php
				echo ucfirst($plugin['group']);
?></td>
					<td><strong style="color: <?php
				echo $plugin['result'] ? "green" : "red";
?>"><?php
				echo $plugin['result'] ? JText::_('Removed') : JText::_('Not removed');
?></strong></td>
				</tr>

	<?php
			}
		}

		?>
		   </tbody>
		</table>
		<?php
	}

	/**
	 * Runs on uninstallation
	 *
	 * @param   JInstaller  $parent  Parent
	 *
	 * @return void
	 *
	 * @since   1.0.0
	 */
	public function uninstall($parent)
	{
		// Uninstall subextensions
		$status = $this->_uninstallSubextensions($parent);

		// Show the post-uninstallation page
		$this->_renderPostUninstallation($status, $parent);
	}

	/**
	 * method to update the component
	 *
	 * @param   JInstaller  $parent  Parent
	 *
	 * @return void
	 */
	public function update($parent)
	{
		$db     = JFactory::getDBO();
		$config = JFactory::getConfig();

		if (JVERSION >= 3.0)
		{
			$configdb = $config->get('db');
		}
		else
		{
			$configdb = $config->getValue('config.db');
		}
		// Get dbprefix
		if (JVERSION >= 3.0)
		{
			$dbprefix = $config->get('dbprefix');
		}
		else
		{
			$dbprefix = $config->getValue('config.dbprefix');
		}
	}
}
