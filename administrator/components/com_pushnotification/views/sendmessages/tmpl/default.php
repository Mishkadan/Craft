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
	echo JText::_('COM_PUSHNOTIFICATION_APPINFOSENDMSG');
	echo '</div><div class="row"style="margin-left:0 !important;">';
		$i=0; foreach ( $allapps as $singleapp ){ ?>
		<a class="dashboardlinks" href="index.php?option=com_pushnotification&view=sendmessages&appid=<?php echo $singleapp['id']; ?>">	
			<div id="fbcats" class="span2" style="min-height: 130px; <?php if ( !empty($appid) && $singleapp['id'] == $appid ) { ?>border: 2px solid #ddd; background:#fbfbf7; <?php } ?><?php if ( $i > 0 ): echo 'margin-left:10px !important;'; else: echo 'margin-left:0px !important;'; endif;?>">
				<img src="<?php echo $singleapp['chrome_web_default_notification_icon']; ?>">
				<br />
				<?php echo ucwords($singleapp['name']); ?>
			</div>
		</a>	
<?php $i++; } 	
echo '</div>';	

if ( !empty($appid) ) 
{
	
// check if app REST API Key exist
$rest_api_exist	= $this->isRestApiExist($appid);
if(is_null($rest_api_exist) || empty($rest_api_exist))
{
?>	
	<div class="alert alert-error alert-danger">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<h4 class="alert-heading">
	<?php echo JText::_( 'COM_PUSHNOTIFICATION_RESTAPIKEYNOPTEXIST'); ?> 
	</h4>
	<div>
	<a href="index.php?option=com_pushnotification&view=apps&appid=<?php echo $appid;?>">
	<?php echo JText::_( 'COM_PUSHNOTIFICATION_CLICKTOSUBMITRESTAPIKEY'); ?> 
	</a>
	</div>
	</div>

<?php
} else 
{ 

$total_users = 0;
$total_musers = 0;

if ( isset($_POST['sendmsgbut'] ) )
{
	$mtitle = $_POST['mtitle'];
	$mbody = $_POST['mbody'];
	$urlback = $_POST['murlback'];
	$sendtype = $_POST['sendtype']; // slater, simm
	
	if ( $sendtype == 'simm' ){
		$wuntill = 'imm';
	} else if ( $sendtype == 'slater' ){
		$wuntill = $_POST['wuntill'];
	}
	$this->submitNewNotification( $mtitle, $mbody, $wuntill, $urlback, $appid, $rest_api_exist );	
}


foreach ( $allapps as $singleapp ){
$total_users = $total_users + $singleapp['players'];
$total_musers = $total_musers + $singleapp['messageable_players'];
if ( !empty($appid) && $singleapp['id'] == $appid ) {
$currentapptotal_users = $singleapp['players'];
$currentapptotal_musers = $singleapp['messageable_players'];
$currentappname = $singleapp['name'];
}
}

?>
<b><?php echo JText::_('COM_PUSHNOTIFICATION_TOTALSUBS');?></b> <?php echo $currentapptotal_users; ?> 
&nbsp;&nbsp;
<b><?php echo JText::_('COM_PUSHNOTIFICATION_AVAILABLESUBS');?></b> <?php echo $currentapptotal_musers; ?> 
 
 <div class="row"style="margin-left:0 !important;">
 <?php if ( !empty($appid) ) {
	 
$allmessages	=	$this->getAllMessages($appid,$rest_api_exist);
$allnots		=	$allmessages->notifications;
$msgcount		=	$allmessages->total_count;

echo '
	<div class="alert alert-info" style="margin-top:20px;">
		<button type="button" class="close" data-dismiss="alert">×</button>
		';
		if ( $msgcount < 1 )
		{
			echo '<h4 class="alert-heading">'.JText::sprintf('COM_PUSHNOTIFICATION_MSGTOTAL',$allmessages->total_count).'</h4>';
			echo '<p>'.JText::_('COM_PUSHNOTIFICATION_SENDMESSAGENOTE').'</p>';
			echo '<p><a data-toggle="modal" data-target="#notmsgmodal" class="btn btn-success btn-medium" href="#">'.JText::_('COM_PUSHNOTIFICATION_SENDMESSAGE').'</a>';
		} 
		else 
		{
			echo '<h4 class="alert-heading">'.JText::sprintf('COM_PUSHNOTIFICATION_MSGTOTAL',$allmessages->total_count).'</h4>';
			echo '<p>'.JText::_('COM_PUSHNOTIFICATION_SENDMESSAGENOTE').'</p>';
			echo '<p><a data-toggle="modal" data-target="#notmsgmodal" class="btn btn-success btn-medium" href="#">'.JText::_('COM_PUSHNOTIFICATION_SENDMESSAGE').'</a>';
		}
echo '</div>';
		if ( $msgcount > 0 )
		{
echo '
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.css">  
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>
<script>
jQuery(document).ready( function () {
    jQuery("#msgtable").DataTable( {
        "lengthMenu": [[ 10, 20, 40, 80, 100, -1 ],[10, 20, 40, 80, 100, "All"]],
		"pageLength": 20,
		
    } );
} );
</script>
';
			
		}
echo '<table id="msgtable" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
			<th>#</th>
			<th>'.JText::_('COM_PUSHNOTIFICATION_MSGTYPE').'</th>';
			//<th>Sent At</th>
			echo '<th>'.JText::_('COM_PUSHNOTIFICATION_MSGMESSAGE').'</th>
			<th>'.JText::_('COM_PUSHNOTIFICATION_MSGSUCCESFULL').'</th>
			<th>'.JText::_('COM_PUSHNOTIFICATION_MSGFAILED').'</th>
			<th>'.JText::_('COM_PUSHNOTIFICATION_MSGREMAINING').'</th>
			<th>'.JText::_('COM_PUSHNOTIFICATION_MSGCONVERTED').'</th>
			<th>'.JText::_('COM_PUSHNOTIFICATION_MSGTIME').'</th>			 
        </tr>
    </thead>
    <tbody>';
        $noc = 1;
		foreach ( $allnots as $singlenot )
		{
			
			$notcontent = $singlenot->headings;
			$notdata = $singlenot->data;
			$shownot= '1';
					
					
					if (is_object($notdata) ){
						foreach ( $notdata as $dkey=>$dval ){
							if ( $dkey == '__isOneSignalWelcomeNotification' ): $shownot='0'; endif;
						}					
					}
							foreach ($notcontent as $conkey=>$conval){
							if ( !$conkey ): $shownot='0'; endif;
							};						

			if($shownot !== '0'){
				echo '
				<tr>';
					echo '<td style="text-align:center;">'.$noc.'</td>';
					if ( $singlenot->queued_at < $singlenot->send_after ): 
					echo '<td>Scheduled</td>';
					else :
					echo '<td>Immediate Deliver</td>';
					endif;			
				//echo '<td>'.( $singlenot->send_after - $singlenot->queued_at ).'</td>';
					echo '<td>';
					foreach ($notcontent as $conkey=>$conval){
					echo $conval.'<br/>';
					};
					echo '</td>';
				echo '<td style="text-align:center;">'.$singlenot->successful.'</td>
				<td style="text-align:center;">'.$singlenot->failed.'</td>
				<td style="text-align:center;">'.$singlenot->remaining.'</td>
				<td style="text-align:center;">'.$singlenot->converted.'</td>
				<td>'.date('m-d-Y H:i:s', $singlenot->send_after).'</td>
				</tr>';
		$noc++;	
			}
		
		}
    echo '</tbody>
</table>';

} ?>
 </div>
<?php 
}	

}

}else{ 
	echo JText::_('COM_PUSHNOTIFICATION_CREATEAPPINFO');
	echo '</div>';
}
	?>
		
	<?php 
	}

?>
</div>
<style>
div#fbcats > img { width:72px !important; margin-bottom: 10px;}
div#fbcats > span { color:#333 !important;}
.modal-body .container-fluid {
    padding:0!important;
}
.modal-footer {
    padding: 34px 20px 20px !important;
}
#notmsgmodal{z-index:-1}
</style> 
<!-- Modal -->
<div class="modal fade" id="notmsgmodal" tabindex="-1" role="dialog" aria-labelledby="notmsgmodalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="notmsgmodalLabel"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGCREATENEW');?></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<div class="container-fluid">
			<div class="row enterauthdiv">
				
				<form class="span5" style="margin-left:0% !important;" id="" name="" action="" method="POST">
					<div class="control-group">
						<div class="controls">
							<label style="font-size: 16px; font-weight: bold;" id="" for="mtitle"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGTITLE');?></label>
							<textarea required="true" style="font-size: 13px; font-weight: bold; width: 90%;" id="mtitle" name="mtitle" placeholder="<?php echo JText::_('COM_PUSHNOTIFICATION_MSGTITLEPL');?>"></textarea>
						</div>
					</div>
					<div class="control-group">
						<div class="controls">					
							<label style="font-size: 16px; font-weight: bold;" id="" for="mbody"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGMESSAGE');?></label>
							<textarea required="true" style="font-size: 13px; font-weight: bold; width: 90%;" id="mbody" name="mbody" placeholder="<?php echo JText::_('COM_PUSHNOTIFICATION_MSGMESSAGEPL');?>"></textarea>
						</div>
					</div>
					<div class="control-group">
						<div class="controls">					
							<label style="font-size: 16px; font-weight: bold;" id="" for="murlback"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGMURLBACK');?></label>
							<input required="true" style="margin-bottom: 9px; font-size: 13px; font-weight: bold; width: 90%; padding: 4px;" id="murlback" name="murlback" placeholder="<?php echo JText::_('COM_PUSHNOTIFICATION_MSGMURLBACKPL');?>"/>
						</div>
					</div>					
					<div class="control-group">
						<div class="controls">						
							<label style="font-size: 16px; font-weight: bold; display: inline !important;" for="sendtypei"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGSENDIMM');?></label>
							<input checked="checked" style="margin-top: -3px;" id="sendtypei" type="radio" name="sendtype" value="simm">
							<label style="margin-left: 10px; font-size: 16px; font-weight: bold; display: inline !important;" for="sendtypel"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGSENDLATER');?></label>
							<input style="margin-top: -3px;" id="sendtypel" type="radio" name="sendtype" value="slater">
						</div>
					</div>
					<div style="display:none" class="schgroup control-group">
						<div class="controls">
							<label style="font-size: 16px; font-weight: bold;" id="" for="wuntill"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGSCHON');?></label>
							<input maxlength="25" style="margin-bottom: 9px; font-size: 13px; font-weight: bold; width: 50%; padding: 4px;" id="wuntill" name="wuntill" placeholder="<?php echo JText::_('COM_PUSHNOTIFICATION_MSGSCHONPL');?>" value="<?php echo date('d-m-Y H:i:s');?> GMT+3"/>
						</div>
					</div>
					<div class="control-group">
						<div class="controls">						
							<input style="margin-top: -3px;" id="sendconf" type="checkbox" name="sendconf">
							<label style="font-size: 13px; font-weight: bold; display: inline !important;" for="sendconf"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGICONFIRM');?></label>
						</div>
					</div>					
					<label>&nbsp;</label>
					<button type="submit" name="sendmsgbut" id="sendmsgbut" class="btn btn-primary"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGSENDMSG');?></button>
				</form>				
				<div class="span7" style="background:  #fff; padding: 1%; margin: 0 1%;">
					<label style="font-size: 16px; font-weight: bold;" id="" for="msgtext"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGMESSAGE');?></label>
					<div id="msgtext">
					<h4 style="font-size: 16px;"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGTITLE');?></h4>
					<p id="msgtitle" style="background:  #efefef; padding: 1%;"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGTITLEPL');?></p>
					</div>
					<div id="msgtext">
					<h4 style="font-size: 16px;"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGMESSAGE');?></h4>
					<p id="msgmessage" style="background:  #efefef; padding: 1%;"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGMESSAGEPL');?></p>
					</div>
					<div id="msgurlb">
					<h4 style="font-size: 16px;"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGMURLBACK');?></h4>
					<p id="msgurl" style="background:  #efefef; padding: 1%;"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGMURLBACKPL');?></p>
					</div>					
					<label style="font-size: 16px; font-weight: bold;" id="" for="msgsch"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGSCHEDULE');?></label>
					<div id="msgsch">
					<p id="schedule" style="background:  #efefef;padding: 1%;font-size: 14px;font-weight: bold;">
					<i class="icon-ok">&nbsp;</i><?php echo JText::_('COM_PUSHNOTIFICATION_MSGRAWAY');?>
					</p>
					</div>					
				</div>				
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo JText::_('COM_PUSHNOTIFICATION_MSGCLOSE');?></button>
        
      </div>
    </div>
  </div>
</div>
<script>
jQuery("document").ready(function(){
		jQuery("#notmsgmodal").on('show.bs.modal', function () {
		jQuery(this).css("z-index","10000");
		});
		jQuery("#notmsgmodal").on('hidden.bs.modal', function () {
		jQuery(this).css("z-index","-10000");
		});
	if (!jQuery('input#sendconf').is(':checked')) { 
		jQuery('#sendmsgbut').attr('disabled', 'disabled'); 
	}
	jQuery('#wuntill').click(function(){;
		jQuery('input#sendconf').prop('checked', false);
		jQuery('#sendmsgbut').attr('disabled', 'disabled'); 
		jQuery(this).css("border","1px solid #ccc");
	});
	
	jQuery("#wuntill").focus(function(){
		jQuery('input#sendconf').prop('checked', false);
		jQuery('#sendmsgbut').attr('disabled', 'disabled'); 
		jQuery(this).css("border","1px solid #ccc");
	});	
	//     border: 1px solid #ccc;
	jQuery('input#sendconf').click(function(){
		
		// check dates fix it and
		datearr = jQuery('#wuntill').val().split(' ');
		daysarr = datearr[0].split('/');
		
			ampmtext = datearr[2];
			//alert(ampmtext);		
		
		//alert(datearr[0]+'\n'+datearr[1]+'\n'+datearr[2]);
		if ( daysarr[0] > 31 ){
			alert('<?php echo JText::_('COM_PUSHNOTIFICATION_MSGFIXDAYS');?>');
			jQuery('#wuntill').css("border", "2px solid red");
			jQuery('input#sendconf').prop('checked', false);
			jQuery('#sendmsgbut').attr('disabled', 'disabled'); 
		}
		
		else if ( daysarr[1] < 1 || daysarr[1] > 12 ){
			alert('<?php echo JText::_('COM_PUSHNOTIFICATION_MSGFIXMONTHS');?>');
			jQuery('#wuntill').css("border", "2px solid red");
			jQuery('input#sendconf').prop('checked', false);
			jQuery('#sendmsgbut').attr('disabled', 'disabled'); 
		}	
		
		hoursarr = datearr[1].split(':');
		//alert(hoursarr [0]+'\n'+hoursarr [1]);
		if ( hoursarr[0] > 24 ){
			alert('<?php echo JText::_('COM_PUSHNOTIFICATION_MSGFIXHOURS');?>');
			jQuery('#wuntill').css("border", "2px solid red");
			jQuery('input#sendconf').prop('checked', false);
			jQuery('#sendmsgbut').attr('disabled', 'disabled'); 
		}
		
		else if ( hoursarr[1] > 59 ){
			alert('<?php echo JText::_('COM_PUSHNOTIFICATION_MSGFIXMINUTES');?>');
			jQuery('#wuntill').css("border", "2px solid red");
			jQuery('input#sendconf').prop('checked', false);
			jQuery('#sendmsgbut').attr('disabled', 'disabled'); 
		} 

		if (jQuery('input#sendconf').is(':checked')) {
			jQuery('#sendmsgbut').removeAttr("disabled");
		} else {
			jQuery('#sendmsgbut').attr('disabled', 'disabled'); 
		}
		
	});
	
	
	
	
	jQuery('input[name="sendtype"]').change(function(){
		sendtypeval = jQuery('input[name="sendtype"]:checked').val();
		//alert(sendtypeval);
		if(sendtypeval == 'simm'){
			jQuery('#schedule').html('<i class="icon-ok">&nbsp;</i><?php echo JText::_('COM_PUSHNOTIFICATION_MSGRAWAY');?>');
			jQuery('.schgroup').slideUp();
		} else if(sendtypeval == 'slater') {
			laterval = jQuery('#wuntill').val();
			jQuery('#schedule').html('<i class="icon-ok">&nbsp;</i>'+laterval+'');
			jQuery('.schgroup').slideDown();
		}
	});
    jQuery('#mtitle').live('keyup', function(e) {
        jQuery('#msgtitle').text(jQuery(this).val());
    });
    jQuery('#mbody').live('keyup', function(e) {
        jQuery('#msgmessage').text(jQuery(this).val());
    });
    jQuery('#murlback').live('keyup', function(e) {
        jQuery('#msgurl').text(jQuery(this).val());
    });	
    jQuery('#wuntill').live('keyup', function(e) {

		jQuery('#schedule').html('<i class="icon-ok">&nbsp;</i>'+jQuery('#wuntill').val());
    });	
});
</script>
