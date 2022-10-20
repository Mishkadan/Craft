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
        	
        	  	<input class="maskphone" id="field_<?php echo $this->id;?>_tel" type="text" name="jform[fields][<?php echo $this->id;?>][tel]"
        	   		size="22" maxlength="22" value="<?php echo (isset($value['tel']) ? $value['tel'] : '');?>" />
        	   	</div>
				<div>
				<i><b>Пояснение к номеру.</b> Например: "Иван Иванович" или "прием заказов"</i>
				<input id="field_<?php echo $this->id;?>_tinfo_1" type="text" name="jform[fields][<?php echo $this->id;?>][tinfo_1]"
        	   		size="22" maxlength="40" value="<?php echo (isset($value['tinfo_1']) ? htmlspecialchars($value['tinfo_1']) : '');?>" />
				</div>
				
			
			<div>
			<div class="telmini" id="field_<?php echo $this->id;?>_cntname">Телефон1 2 (Не обязательно)</div>
			  	<input class="maskphone" id="field_<?php echo $this->id;?>_ext" type="text" name="jform[fields][<?php echo $this->id;?>][ext]"
        	    	size="22" maxlength="22" value="<?php echo (isset($value['ext']) ? $value['ext'] : '');?>" />  
        	    </div>
			<div>
      <div class="telmini" id="field_<?php echo $this->id;?>_cntname">Телефон1 2 (Не обязательно)</div>
			  	<input class="maskphone" id="field_<?php echo $this->id;?>_ext" type="text" name="jform[fields][<?php echo $this->id;?>][ext]"
        	    	size="22" maxlength="22" value="<?php echo (isset($value['ext']) ? $value['ext'] : '');?>" />  
        	    </div>
			<div>
				<i><b>Пояснение к номеру.</b> Например: "Иван Иванович" или "прием заказов"</i>
				<input id="field_<?php echo $this->id;?>_tinfo_2" type="text" name="jform[fields][<?php echo $this->id;?>][tinfo_2]"
        	   		size="22" maxlength="40" value="<?php echo (isset($value['tinfo_2']) ? htmlspecialchars($value['tinfo_2']) : '');?>" />
				</div>
					
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
  var input = document.querySelectorAll(".maskphone");
  input[0].placeholder = '+_ (___) ___-__-__';
  input[1].placeholder = '+_ (___) ___-__-__';

  input[0].addEventListener("input", mask);
  input[0].addEventListener("focus", mask);
  input[0].addEventListener("blur", mask);
  input[1].addEventListener("input", mask);
  input[1].addEventListener("focus", mask);
  input[1].addEventListener("blur", mask);
  
  /***/
  function mask(event) {
    var blank = "+_ (___) ___-__-__";
    
    var i = 0;
    var val = this.value.replace(/\D/g, "").replace(/^8/, "7"); // <---
    
    this.value = blank.replace(/./g, function(char) {
      if (/[_\d]/.test(char) && i < val.length) return val.charAt(i++);
      
      return i >= val.length ? "" : char;
    });
    
    if (event.type == "blur") {
      if (this.value.length == 2) this.value = "";
    } else {
      setCursorPosition(this, this.value.length);
    }
  };
  
  /***/
  function setCursorPosition(elem, pos) {
    elem.focus();
    
    if (elem.setSelectionRange) {    
      elem.setSelectionRange(pos, pos);
      return;
    }
    
    if (elem.createTextRange) {    
      var range = elem.createTextRange();
      range.collapse(true);
      range.moveEnd("character", pos);
      range.moveStart("character", pos);
      range.select();      
      return;
    }
  }
});
</script>