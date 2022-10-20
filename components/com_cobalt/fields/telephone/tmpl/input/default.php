<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$params = $this->params;
$class = ' class="' . $this->params->get('core.field_class', 'inputbox') . ($this->required ? ' required' : NULL) .'"';
$required = $this->required ? 'required="true" ' : NULL;

$value = $this->value ? $this->value : null;
?>

<div class="input text">
        	<div class="tells">
			<div>
			<div class="telmini" id="field_<?php echo $this->id;?>_cntname"><?php echo JText::_('T_TEL');?>&nbsp;1</div>      	    
        	  	<input class="input-small" id="field_<?php echo $this->id;?>_tel" type="text" name="jform[fields][<?php echo $this->id;?>][tel]"  onkeyup="Cobalt.formatInt(this)"
        	   		size="22" maxlength="22" value="<?php echo (isset($value['tel']) ? $value['tel'] : '');?>" />
        	   	</div>
				
				<div>
			<div class="telmini" id="field_<?php echo $this->id;?>_cntname">Телефон 2 (Не обязательно)</div>
			  	<input class="input-mini" id="field_<?php echo $this->id;?>_ext" type="text" name="jform[fields][<?php echo $this->id;?>][ext]"  onkeyup="Cobalt.formatInt(this)"
        	    	size="3"  value="<?php echo (isset($value['ext']) ? $value['ext'] : '');?>" />  
        	    </div>
			<div>
</div>