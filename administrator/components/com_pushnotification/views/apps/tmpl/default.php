<?php
/*
* @package 		com_pushnotification - Push Notification
* @version		V1.0.2
* @created		Jan 2020
* @author		ExtensionCoder.com
* @email		developer@extensioncoder.com
* @website		https://www.extensioncoder.com
* @support		http://www.extensioncoder.com/support.html
* @copyright	Copyright (C) 2019-2020 ExtensionCoder. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
// No direct access
defined('_JEXEC') or die;
	$user      = JFactory::getUser();
	$userId    = $user->get('id');	
	
	$app = JFactory::getApplication();
	$jInput = $app->input;
	$submitrestapi = $jInput->get('submitrestapi','','string');
	$submitappid = $jInput->get('submitappid','','string');
	$submitupdate = $jInput->get('submitupdate','','string');
	if( isset($submitrestapi) && !empty($submitrestapi) ){
		$this->saveUpdateRestApi($submitrestapi,$submitappid,$submitupdate,$userId);
	}
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_pushnotification/assets/css/pushnotification.css');

if (!empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}
if (!empty($this->sidebar)){ ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php } else { ?>
	<div id="j-main-container">
<?php }
$plugin_installed = $this->isPluginInstalled('PLG_SYSTEM_PUSHNOTIFICATION');
if ( is_null($plugin_installed) ||  !$plugin_installed )
{
?>
	<div class="alert alert-error alert-danger" style="0 0 15px 0">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<h4 class="alert-heading">
	<?php echo JText::_( 'COM_PUSHNOTIFICATION_PLUGINNOTINSTALLED'); ?> 
	</h4>
	<?php echo JText::_('COM_PUSHNOTIFICATION_PLUGININSTALLINFO'); ?>
	</div>
<?php	
}
$params = JComponentHelper::getParams('com_pushnotification');
if( empty( $params->get('uakey') )  )
{
?>
	<div id="system-message-container" class="j-toggle-main span12" style="margin-top:20px;">
		<div class="alert alert-error alert-danger" style="0 0 15px 0">
			<div style="margin:10px 0;">
					<a href="index.php?option=com_config&view=component&component=com_pushnotification" class="btn btn-small">
					<span class="icon-options" aria-hidden="true"></span>
					<?php echo JText::_('COM_PUSHNOTIFICATION_COMPLETEMAINCONFIG');?>
					</a>
			</div>	
			<h4 class="alert-heading"><?php echo JText::_('COM_PUSHNOTIFICATION_CONFIG_NOTCOLMPLETED'); ?></h4>
			<div><?php echo JText::_('COM_PUSHNOTIFICATION_CONFIG_UAERRORDESC'); ?>&nbsp;</div>
		</div>
	</div>
<?php 	
} 
else {
	$allapps = $this->checkApps();
	$totalapps = count($allapps);
	$appid = $jInput->get('appid');	

	?>
	<div class="alert alert-info">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<h4 class="alert-heading">
	<?php echo JText::sprintf( 'COM_PUSHNOTIFICATION_APPSTOTAL', count($allapps) ); ?> 
	</h4>
	<?php 
	if ( $totalapps > 0 ){
	echo JText::_('COM_PUSHNOTIFICATION_APPINFO');
	echo '</div><div class="row"style="margin-left:0 !important;">';
		$i=0; foreach ( $allapps as $singleapp ){ ?>
		<a class="dashboardlinks" href="index.php?option=com_pushnotification&view=apps&appid=<?php echo $singleapp['id']; ?>">	
			<div id="fbcats" class="span2" style="min-height: 130px; <?php if ( !empty($appid) && $singleapp['id'] == $appid ) { ?>border: 2px solid #ddd; background:#fbfbf7; <?php } ?><?php if ( $i > 0 ): echo 'margin-left:10px !important;'; else: echo 'margin-left:0px !important;'; endif;?>">
				<img src="<?php echo $singleapp['chrome_web_default_notification_icon']; ?>">
				<br />
				<?php echo ucwords($singleapp['name']); ?>
			</div>
		</a>	
<?php $i++; } 	
echo '</div>';	

if ( !empty($appid) ) {
	
// check if app REST API Key exist
$rest_api_exist	= $this->isRestApiExist($appid);
if(is_null($rest_api_exist) || empty($rest_api_exist)){
?>	
	<div class="alert alert-error alert-danger">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<h4 class="alert-heading">
	<?php echo JText::_( 'COM_PUSHNOTIFICATION_RESTAPIKEYNOPTEXIST'); ?> 
	</h4>
	<div><?php echo JText::_( 'COM_PUSHNOTIFICATION_RESTAPIKEYNOPTEXISTINFO'); ?> </div>
	</div>
	<form name="submitrestapi" method="POST" action="index.php?option=com_pushnotification&view=apps&appid=<?php echo $appid; ?>">
	<input type="text" required value="<?php echo $rest_api_exist; ?>" style="width:400px; padding:10px; font-size:14px;" name="submitrestapi" placeholder="Enter Rest API Key" />
	<input type="hidden" name="submitappid" value="<?php echo $appid; ?>" />
	<input type="hidden" name="submitupdate" value="save" />
	<br />
	<button style="width:250px; padding:10px; font-size:14px;" type="submit"><?php echo JText::_('COM_PUSHNOTIFICATION_RESTAPISAVE');?></button>
	</form>
<?php
} else 
{ ?>
	<form name="submitrestapi" method="POST" action="index.php?option=com_pushnotification&view=apps&appid=<?php echo $appid; ?>">
	<input type="text" required value="<?php echo $rest_api_exist; ?>" style="width:400px; padding:10px; font-size:14px;" name="submitrestapi" placeholder="Enter Rest API Key" />
	<input type="hidden" name="submitappid" value="<?php echo $appid; ?>" />
	<input type="hidden" name="submitupdate" value="update" />
	<br />
	<button style="width:250px; padding:10px; font-size:14px;" type="submit"><?php echo JText::_('COM_PUSHNOTIFICATION_RESTAPIUPDATE');?></button>
	</form>	
<?php 
}	
$j=0; foreach ( $allapps as $singleapp ){	
	if ( $singleapp['id'] == $appid ){ ?>
  <div class="table table-responsive table-striped">          
  <table class="table">
    <thead>
      <tr>
        <th style="font-weight:normal !important;background: #fbfbfb;border-top: 1px solid #efefef;">
			<b><?php echo ucwords($singleapp['name']); ?> - <?php echo JText::_('COM_PUSHNOTIFICATION_APPDETAILS'); ?></b>
		</th>
		<th style="font-weight:normal !important;background: #fbfbfb;border-top: 1px solid #efefef;">
&nbsp;
		</th>
      </tr>
    </thead>
    <tbody>
	  <tr>
			<td>App ID</td>
			<td><?php echo $singleapp['id']; ?></td>
      </tr>	
	  <tr>
			<td>App Name</td>
			<td><?php echo $singleapp['name']; ?></td>
      </tr>
	  <tr>
			<td>Default Chrome Icon</td>
			<td><img style="width:36px; height:36px; float:left;" src="<?php echo $singleapp['chrome_web_default_notification_icon']; ?>"></td>
      </tr>		  
	  <tr>
			<td>Site Name</td>
			<td><?php echo $singleapp['site_name']; ?></td>
      </tr>	
      <tr>
			<td>Created at</td>
			<td><?php echo $singleapp['created_at']; ?></td>
      </tr>	
      <tr>
			<td>Updated at</td>
			<td><?php echo $singleapp['updated_at']; ?></td>
      </tr>
      <tr>
			<td><b>Total Users</b></td>
			<td><?php echo $singleapp['players']; ?></td>
      </tr>
      <tr>
			<td><b>Messageable User</b></td>
			<td><?php echo $singleapp['messageable_players']; ?></td>
      </tr>	
      <tr>
			<td>App Basic Auth Key</td>
			<td><?php echo $singleapp['basic_auth_key']; ?></td>
      </tr>
      <tr>
			<td>Chrome Web Key</td>
			<td><?php echo $singleapp['chrome_web_key']; ?></td>
      </tr>	 
      <tr>
			<td>Chrome Web Origin</td>
			<td><?php echo $singleapp['chrome_web_origin']; ?></td>
      </tr>	 
      <tr>
			<td>Chrome Web GCM Sender ID</td>
			<td><?php echo $singleapp['chrome_web_gcm_sender_id']; ?></td>
      </tr>	 
		<tr>
			<td>GCM Key</td>
			<td><?php echo $singleapp['gcm_key']; ?></td>
      </tr>
      <tr>
			<td>Safari Site Origin</td>
			<td><?php echo $singleapp['safari_site_origin']; ?></td>
      </tr>
      <tr>
			<td>Safari Push ID</td>
			<td><?php echo $singleapp['safari_push_id']; ?></td>
      </tr>		  
    </tbody>
  </table>
  </div>		
		
<?php	}
}

$j++; }

}else{ 
	echo JText::_('COM_PUSHNOTIFICATION_CREATEAPPINFO');
	echo '</div>';
}
	?>
		
	<?php 
	}

?>
</div>
