<?php
/**
* @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
/**
 * Configuration view for JomSocial
 */
class CommunityViewConfiguration extends JViewLegacy
{
	/**
	 * The default method that will display the output of this view which is called by
	 * Joomla
	 *
	 * @param	string template	Template file name
	 **/
	public function display( $tpl = null )
	{
		$document	= JFactory::getDocument();

		$params	= $this->get( 'Params' );
		//user's email privacy setting
		//CFactory::load( 'libraries' , 'notificationtypes' );
		$notificationTypes = new CNotificationTypes();

		$lists = array();

		for ($i=1; $i<=31; $i++) {
			$qscale[]	= JHTML::_('select.option', $i, $i);
		}

		$lists['qscale'] = JHTML::_('select.genericlist',  $qscale, 'qscale', 'class="inputbox" size="1"', 'value', 'text', $params->get('qscale', '11'));

		$videosSize = array
		(
			JHTML::_('select.option', '320x240', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION1')),
			JHTML::_('select.option', '320x???', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION2')),
			JHTML::_('select.option', '400x240', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION3')),
			JHTML::_('select.option', '400x300', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION4')),
			JHTML::_('select.option', '400x???', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION5')),
			JHTML::_('select.option', '480x272', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION6')),
			JHTML::_('select.option', '480x320', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION7')),
			JHTML::_('select.option', '480x360', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION8')),
			JHTML::_('select.option', '480x???', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION9')),
			JHTML::_('select.option', '512x384', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION10')),
			JHTML::_('select.option', '512x???', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION11')),
			JHTML::_('select.option', '600x480', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION12')),
			JHTML::_('select.option', '600x???', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION13')),
			JHTML::_('select.option', '640x360', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION14')),
			JHTML::_('select.option', '640x480', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION15')),
			JHTML::_('select.option', '640x???', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION16')),
			JHTML::_('select.option', '800x600', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION17')),
			JHTML::_('select.option', '800x???', JText::_('COM_COMMUNITY_VIDEO_SIZE_OPTION18')),
		);

		$lists['videosSize'] = JHTML::_('select.genericlist',  $videosSize, 'videosSize', 'class="inputbox" size="1"', 'value', 'text', $params->get('videosSize'));


		$imgQuality = array
		(
			JHTML::_('select.option', '60', JText::_('COM_COMMUNITY_PHOTO_QUALITY_OPTION1')),
			JHTML::_('select.option', '80', JText::_('COM_COMMUNITY_PHOTO_QUALITY_OPTION2')),
			JHTML::_('select.option', '90', JText::_('COM_COMMUNITY_PHOTO_QUALITY_OPTION3')),
			JHTML::_('select.option', '100', JText::_('COM_COMMUNITY_PHOTO_QUALITY_OPTION4')),
		);

		$lists['imgQuality'] = JHTML::_('select.genericlist',  $imgQuality, 'output_image_quality', 'class="inputbox" size="1"', 'value', 'text', $params->get('output_image_quality'));

        //album mode
        $albumMode = array
        (
            JHTML::_('select.option', '0', JText::_('COM_COMMUNITY_SAME_WINDOW') ), // same window
            JHTML::_('select.option', '1', JText::_('COM_COMMUNITY_MODAL_WINDOW')), // new window
        );

        $lists['albumMode'] = JHTML::_('select.genericlist',  $albumMode, 'album_mode', 'class="inputbox" size="1"', 'value', 'text', $params->get('album_mode'));

        //video mode
        $videoMode = array
        (
            JHTML::_('select.option', '0', JText::_('COM_COMMUNITY_SAME_WINDOW') ), // same window
            JHTML::_('select.option', '1', JText::_('COM_COMMUNITY_MODAL_WINDOW')), // new window
        );

        $lists['videoMode'] = JHTML::_('select.genericlist',  $videoMode, 'video_mode', 'class="inputbox" size="1"', 'value', 'text', $params->get('video_mode'));

        //video native
        $videoNative = array
        (
            JHTML::_('select.option', '0', JText::_('COM_COMMUNITY_STREAM_VIDEO_PLAYER_MEDIAELEMENT') ),
            JHTML::_('select.option', '1', JText::_('COM_COMMUNITY_STREAM_VIDEO_PLAYER_NATIVE')),
        );

        $lists['videoNative'] = JHTML::_('select.genericlist',  $videoNative, 'video_native', 'class="inputbox" size="1"', 'value', 'text', $params->get('video_native'));

        // Group discussion order option
		$groupDiscussionOrder = array(
			JHTML::_('select.option', 'ASC', JText::_('COM_COMMUNITY_SORT_OLDER_FRIST')),
			JHTML::_('select.option', 'DESC', JText::_('COM_COMMUNITY_SORT_NEWER_FRIST')),
		);
		$lists['groupDicussOrder'] = JHTML::_('select.genericlist',  $groupDiscussionOrder, 'group_discuss_order', 'class="inputbox" size="1"', 'value', 'text', $params->get('group_discuss_order'));

		$videoThumbSize = array(
				JHTML::_('select.option','???x???','Исходный размер - авто наверное)'),
				JHTML::_('select.option','320x180','320x180'),
				JHTML::_('select.option','400x400','400x400'),
				JHTML::_('select.option','320x???','320px ширина пропорционально по ширине'),
				JHTML::_('select.option', '400x???','400px ширина пропорционально по ширине'),
				JHTML::_('select.option', '480x???', '480px ширина пропорционально по ширине'),
				JHTML::_('select.option', '640x360', '640x360'),
				JHTML::_('select.option', '1280x720', '1280x720'),
		);

		$lists['videoThumbSize'] = JHTML::_('select.genericlist',  $videoThumbSize, 'videosThumbSize', 'class="inputbox" size="1"', 'value', 'text', $params->get('videosThumbSize'));
		$dstOffset	= array();
		$counter = -4;
		for($i=0; $i <= 8; $i++ ){
			$dstOffset[] = 	JHTML::_('select.option', $counter, $counter);
			$counter++;
		}

        $watermarkPosition = array(
            JHTML::_('select.option', 'left_top', JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_WATERMARK_POSITION_LFFT_TOP')),
            JHTML::_('select.option', 'left_bottom', JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_WATERMARK_POSITION_LFFT_BOTTOM')),
            JHTML::_('select.option', 'right_top', JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_WATERMARK_POSITION_RIGHT_TOP')),
            JHTML::_('select.option', 'right_bottom', JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_WATERMARK_POSITION_RIGHT_BOTTOM'))
        );
        $lists['watermarkPosition'] = JHTML::_('select.genericlist',  $watermarkPosition, 'watermark_position', 'class="inputbox" size="1"', 'value', 'text', $params->get('watermark_position'));

		$lists['dstOffset']	= JHTML::_('select.genericlist',  $dstOffset, 'daylightsavingoffset', 'class="inputbox" size="1"', 'value', 'text', $params->get('daylightsavingoffset'));
		$networkModel		= $this->getModel( 'network' , false );
		$JSNInfo			= $networkModel->getJSNInfo();
		$JSON_output		= $networkModel->getJSON();
		$lists['enable']	= JHTML::_('select.booleanlist',  'network_enable', 'class="inputbox"', $JSNInfo['network_enable'] );
		$uploadLimit		= ini_get('upload_max_filesize');
		$uploadLimit		= CString::str_ireplace('M', ' MB', $uploadLimit);

		require_once( JPATH_ROOT.'/administrator/components/com_community/libraries/autoupdate.php' );
		$isuptodate = CAutoUpdate::checkUpdate();

		$this->set( 'JSNInfo', $JSNInfo );
		$this->set( 'JSON_output', $JSON_output );
		$this->set( 'lists', $lists );
		$this->set( 'uploadLimit' , $uploadLimit );
		$this->set( 'config' , $params );
		$this->set( 'isuptodate' , $isuptodate );

		$this->set('notificationTypes',$notificationTypes);

		jimport('joomla.html.editor');
		$config = CFactory::getConfig();
		$editor = $editor = new CEditor('jomsocial');

		$this->set('editor', $editor);

		parent::display($tpl);
	}

	public function getTemplatesList( $name , $default = '' )
	{
		$path	= dirname(JPATH_BASE) . '/components/com_community/templates';

		if( $handle = @opendir($path) )
		{
			while( false !== ( $file = readdir( $handle ) ) )
			{
				// Do not get '.' or '..' or '.svn' since we only want folders.
				if( $file != '.' && $file != '..' && $file != '.svn' && JFolder::exists( $path .'/'. $file) )
					$templates[]	= $file;
			}
		}

		$html	= '<select name="' . $name . '">';

		foreach( $templates as $template )
		{
			if( $template )
			if( !empty( $default ) )
			{
				$selected	= ( $default == $template ) ? ' selected="true"' : '';
			}
			$html	.= '<option value="' . $template . '"' . $selected . '>' . $template . '</option>';
		}
		$html	.= '</select>';

		return $html;
	}

	public function getKarmaHTML( $name , $value, $readonly=false, $updateTarget='')
	{
		$isReadOnly	= ($readonly) ? ' readonly="readonly"' : '';
		$requiredTargetUpdate = (! empty($updateTarget)) ? 'onblur="azcommunity.updateField(\''.$name.'\', \''.$updateTarget.'\')"' : '';

		$html	= '<table>';
		$html	.= '<tr>';
		$html	.= '	<td>';
		if ($readonly) {
			$html .= '<span class="karma_readonly" id="' . $name . '">' . $value . '</span> ';
		} else {
			$html	.= '<input type="text" value="' . $value . '" name="' . $name . '" id="'.$name.'" '.$isReadOnly.' '.$requiredTargetUpdate.' /> ';
		}
		// $html	.= JText::_('COM_COMMUNITY_CONFIGURATION_KARMA_USE_IMAGE');
		$html	.= '	</td>';
		$html	.= '	<td>';
		$html	.= '	&nbsp;&nbsp;&nbsp;<img class="com_karmaImage" src="' . $this->_getKarmaImage( $name ) . '" />';
		$html	.= '	</td>';
		$html	.= '</tr>';
		$html	.= '</table>';
		return $html;
	}

	public function getNotifyTypeHTML( $selected )
	{
		$types	= array();

		$types[]	= array( 'key' => '1' , 'value' => JText::_('COM_COMMUNITY_EMAIL') );
		$types[]	= array( 'key' => '2' , 'value' => JText::_('COM_COMMUNITY_PRIVATE_MESSAGE') );

		$html		= '<select name="notifyby">';

		foreach( $types as $type => $option )
		{
			$selectedData	= '';
			if( $option['key'] == $selected )
			{
				$selectedData	= ' selected="true"';
			}
			$html	.= '<option value="' . $option['key'] . '"' . $selectedData . '>' . $option['value'] . '</option>';
		}
		$html	.= '</select>';

		return $html;
	}

	public function getPrivacyHTML( $name , $selected , $showSelf = false )
	{
		$public		= ( $selected == 0 ) ? 'btn-success' : 'btn-light';
		$members	= ( $selected == 20 ) ? 'btn-success' : 'btn-light';
		$friends	= ( $selected == 30 ) ? 'btn-success' : 'btn-light';
		$self		= ( $selected == 40 ) ? 'btn-success' : 'btn-light';

		$html = '<div class="btn-group" data-toggle-name="' . $name . '" data-toggle="buttons-radio">';

		$html .= '<button type="button" value="0" class="btn btn-small ' . $public . '" />' . JText::_('COM_COMMUNITY_PUBLIC').'</button>';
		$html .= '<button type="button" value="20" class="btn btn-small ' . $members . '" /> ' . JText::_( 'COM_COMMUNITY_MEMBERS').'</button>';
		$html .= '<button type="button" value="30" class="btn btn-small ' . $friends . '" /> ' . JText::_('COM_COMMUNITY_FRIENDS').'</button>';

		if( $showSelf )
		{
			$html .= '<button type="button" value="40" class="btn btn-small ' . $self . '" />' . JText::_('COM_COMMUNITY_SELF').'</button>';
		}

		$html .= '</div>';

		$html .= '<input type="hidden" name="' . $name . '" value="' . $selected . '" />';

		return $html;
	}

	/**
	 * Method to return the image path for specific elements
	 * @access	private
	 *
	 * @return	string	$image	The path to the image.
	 */
	public function _getKarmaImage( $name )
	{
		$image	= '';

		switch( $name )
		{
			case 'point0':
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-0.5-5.png';
				break;
			case 'point1':
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-1-5.png';
				break;
			case 'point2':
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-2-5.png';
				break;
			case 'point3':
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-3-5.png';
				break;
			case 'point4':
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-4-5.png';
				break;
			case 'point5':
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-5-5.png';
				break;
			default:
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-0-5.png';
				break;
		}
		return $image;
	}

	public function setToolBar()
	{
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
        $jinput = JFactory::getApplication()->input;
		// Set the titlebar text
		JToolBarHelper::title( JText::sprintf( 'COM_COMMUNITY_CONFIGURATION_PAGE_TITLE', JText::_('COM_COMMUNITY_CONFIG_TITLE_'.strtoupper(str_replace('-','_',$jinput->get( 'cfgSection' , 'site' )))) ), 'configuration');

		// Add the necessary buttons
		JToolBarHelper::save( 'saveconfig', JText::_('COM_COMMUNITY_SAVE') );
		JToolBarHelper::cancel();
	}

	public function getEditors()
	{
		$db		= JFactory::getDBO();

		// compile list of the editors
		$query = 'SELECT ' . $db->quoteName('element') . ' AS ' . $db->quoteName('value') . ', ' . $db->quoteName('name') . ' AS ' . $db->quoteName('text')
				. ' FROM ' . $db->quoteName(PLUGIN_TABLE_NAME)
				. ' WHERE ' . $db->quoteName('folder') . ' = ' . $db->Quote('editors')
				. ' AND ' . $db->quoteName(EXTENSION_ENABLE_COL_NAME) . ' = ' . $db->Quote(1)
				. ' ORDER BY ' . $db->quoteName('ordering') . ', ' . $db->quoteName('name');
		$db->setQuery( $query );
		$editors = $db->loadObjectList();

		// Add JomSocial's Editor
		$editor    =	new stdClass();
		$editor->value	=   'jomsocial';
		$editor->text	=   'plg_editor_jomsocial';

		array_push( $editors, $editor );

		return $editors;
	}

	public function getFieldCodes( $elementName , $selected = '' )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT DISTINCT ' . $db->quoteName('fieldcode') . ' FROM ' . $db->quoteName('#__community_fields');
		$db->setQuery( $query );
		$fieldcodes	= $db->loadObjectList();

		$html		= '<select name="'. $elementName . '">';

        $html .='<option></option>';

		foreach( $fieldcodes as $fieldcode )
		{
			if( !empty($fieldcode->fieldcode ) )
			{
				$selectedData	= '';

				if( $fieldcode->fieldcode == $selected )
				{
					$selectedData	= ' selected="true"';
				}
				$html	.= '<option value="' . $fieldcode->fieldcode . '"' . $selectedData . '>' . $fieldcode->fieldcode . '</option>';
			}
		}
		$html	.= '</select>';

		return $html;
	}

	public function getFolderPermissionsPhoto( $name , $selected )
	{
		$all		= ( $selected == '0777' ) ? 'checked="true" ' : '';
		$default	= ( $selected == '0755' ) ? 'checked="true" ' : '';

		$html	 = '<input type="radio" value="0777" name="' . $name . '" ' . $all . '/> ' . JText::_('COM_COMMUNITY_CHMOD777');
		$html	.= '<input type="radio" value="0755" name="' . $name . '" ' . $default . '/> ' . JText::_('COM_COMMUNITY_SYSTEM_DEFAULT');

		return $html;
	}

	public function getFolderPermissionsVideo( $name , $selected )
	{
		$all		= ( $selected == '0777' ) ? 'checked="true" ' : '';
		$default	= ( $selected == '0755' ) ? 'checked="true" ' : '';

		$html	 = '<input type="radio" value="0777" name="' . $name . '" ' . $all . '/> ' . JText::_('COM_COMMUNITY_CHMOD777');
		$html	.= '<input type="radio" value="0755" name="' . $name . '" ' . $default . '/> ' . JText::_('COM_COMMUNITY_SYSTEM_DEFAULT');

		return $html;
	}

	public function getLocationFieldCodes($elementName, $selected = '')
    {
        $db = JFactory::getDBO();
        $query = 'SELECT DISTINCT ' . $db->quoteName('fieldcode') . ' FROM ' . $db->quoteName('#__community_fields') . ' '
            . 'WHERE ' . $db->quoteName('type') . '=' . $db->Quote('location');

        $db->setQuery($query);
        $fieldcodes = $db->loadObjectList();
        
        $html = '<select name="'. $elementName . '">';
        $html .= '<option value=""></option>';

        foreach ($fieldcodes as $fieldcode) {
            if (!empty($fieldcode->fieldcode)) {
                $selectedData = '';

                if ($fieldcode->fieldcode == $selected) {
                    $selectedData = ' selected="true"';
                }
                
                $html .= '<option value="' . $fieldcode->fieldcode . '"' . $selectedData . '>' . $fieldcode->fieldcode . '</option>';
            }
        }
        
        $html .= '</select>';

        return $html;
    }

    public function getLicenseDetail($license = null)
    {	
		$return = array();
		$domain = $_SERVER['HTTP_HOST'];
		$domain = str_replace("https://", "", $domain);
		$domain = str_replace("http://", "", $domain);

    	$check_url = "https://www.jomsocial.com/index.php?option=com_digistore&controller=digistoreAutoinstaller&task=get_license_number_details&tmpl=component&format=raw&component=community&domain=".urlencode($domain)."&license=".trim($license);

    	$license_details = file_get_contents($check_url);
    	
		if($license_details === FALSE){
			$curl = curl_init();
			curl_setopt ($curl, CURLOPT_URL, $check_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$license_details = curl_exec ($curl);
			curl_close ($curl);
		}
		
    	if(isset($license_details) && trim($license_details) != ""){
			$license_details = json_decode($license_details, true);

			if(isset($license_details["0"])){
				$license_details = $license_details["0"];
			}

			if(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) == "0000-00-00 00:00:00"){
				$valid_license = true;
			}
			elseif(isset($license_details["expires"]) && trim($license_details["expires"]) != "" && trim($license_details["expires"]) != "0000-00-00 00:00:00"){
				$now = strtotime(date("Y-m-d H:i:s"));
				$license_expires = strtotime(trim($license_details["expires"]));

				if($license_expires >= $now){
					$valid_license = true;
				}
			}

			if(isset($license_details["domain"]) && isset($license_details["expires"])){
				$domain = $license_details["domain"];
				$date = $license_details["expires"];
				$status = JText::_("COM_COMMUNITY_LICENSE_INACTIVE");

				if($valid_license){
					$status = JText::_("COM_COMMUNITY_LICENSE_ACTIVE");
				}

				$return = array("domain"=>$domain, "date"=>$date, "status"=>$status);
			}
		}

        return $return;
    }
}
