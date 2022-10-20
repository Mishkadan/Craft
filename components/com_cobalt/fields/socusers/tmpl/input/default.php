<?php
defined('_JEXEC') or die();
$doc        = JFactory::getDocument();
$directory  = $this->directory;
$jinput     = JFactory::getApplication()->input;
$viewName   = $jinput->get('view');
$taskName   = $jinput->get('task');
$list       = $this->getAlluser();
$valueq     = $this->value;
$moders     = $this->getModers();
$moder_list = array();
foreach ($moders as $k=>$vl)
{
	$moder_list[$k] = $vl->cuser_id;
}
?>
<ul id="chosen-u" class="joms-list--friend chosen">
	<?php foreach ($list as $user)
	{
		$link = CRoute::_('index.php?option=com_community&view=profile&userid=' . $user->userid); ?>

        <li class="joms-list__item craft">

			<? if (in_array($user->id, $moder_list)) { ?>
                <i class="moder fas fa-user-edit"></i>
			<? } ?>



            <input style="display:none" type="checkbox" class="myCheckbox" name="jform[fields][<?php echo $this->id; ?>][cr_id-<?php echo $user->id; ?>]"
                   name="jform[fields][<?php echo $this->id; ?>][cr_id-<?php echo $user->id; ?>]"
                   value="<?php echo(isset($this->value['cr_id-' . $user->id]) ? stripslashes($this->value['cr_id' . $user->id]) : ''); ?>"
		        <?php echo(!isset($this->value['cr_id-' . $user->id]) ? stripslashes($this->value['cr_id-' . $user->id]) : 'checked="checked"'); ?>>



            <input onclick="alert1(this); return myfun2(this)" style="display:" type="checkbox" class="myCheckbox2"
                   id="user-<?php echo $user->userid; ?>"
                   name="jform[fields][<?php echo $this->id; ?>][user-name-<?php echo $user->id; ?>]"
                   value="<?php echo(isset($this->value['user-name-' . $user->id]) ? stripslashes($this->value['user-name-' . $user->id]) : ''); ?>"
				<?php echo(!isset($this->value['user-name-' . $user->id]) ? stripslashes($this->value['user-name-' . $user->id]) : 'checked="checked"'); ?>>


            <input style="display:none" type="checkbox" class="myCheckbox" id="user-<?php echo $user->userid; ?>"
                   name="jform[fields][<?php echo $this->id; ?>][user-thumb-<?php echo $user->id; ?>]"
                   value="<?php echo(isset($this->value['user-thumb-' . $user->id]) ? stripslashes($this->value['user-thumb-' . $user->id]) : ''); ?>"
				<?php echo(!isset($this->value['user-name-' . $user->id]) ? stripslashes($this->value['user-name-' . $user->id]) : 'checked="checked"'); ?>>


            <input style="display:none" type="checkbox" class="myCheckbox" id="user-<?php echo $user->userid; ?>"
                   name="jform[fields][<?php echo $this->id; ?>][user-link-<?php echo $user->id; ?>]"
                   value="<?php echo(isset($this->value['user-link-' . $user->id]) ? stripslashes($this->value['user-link-' . $user->id]) : ''); ?>"
				<?php echo(!isset($this->value['user-name-' . $user->id]) ? stripslashes($this->value['user-name-' . $user->id]) : 'checked="checked"'); ?>>




            <!-- avatarka :) -->
            <div class="joms-list__avatar joms-avatar  craft">
				<?php if (isset($user->thumb) && !empty($user->thumb)) { ?>
                    <img src="<?php echo $user->thumb; ?>" title="<?php echo $user->name; ?>"
                         alt="<?php echo $user->name; ?>" data-author="<?php echo $user->userid; ?>"/>
				<? } else { ?>
                    <img src="/components/com_community/assets/user-Male-thumb.png" title="<?php echo $user->name; ?>"
                         alt="<?php echo $user->name; ?>" data-author="<?php echo $user->userid; ?>">
				<? } ?>
            </div>
            <div class="joms-list__body craft">

                <!-- name -->
                <h4 class="joms-text--username craft"><?php echo $user->name; ?></h4>
            </div>
			<? if (in_array($user->id, $moder_list)) { ?>
                <button class='moder-btn js-moder' onclick="moders(this); return false" name="moder"
                        value="<?= $user->id ?>">Запретить<br>модерировать
                </button>
			<? } else { ?>
            <button class='moder-btn' onclick="moders(this); return false" name="moder" value="<?= $user->id ?>">Сделать<br>модератором
                </button><? } ?>


        </li>
	<?php } ?>
</ul>

<button onclick="searchdrop(); return false" id="btbtn" class="button-full">Выбрать пользователей</button>
<input style="display:none;" type="text" placeholder="Поиск.." id="s-craft-s" onkeyup="filterFunction()">
<ul id="u-chose" class="joms-list--friend craft" style="display:none">
	<?php foreach ($list as $user)
	{
		$link = CRoute::_('index.php?option=com_community&view=profile&userid=' . $user->userid); ?>
        <li onclick="return myfun(this);"
            class="joms-list__item craft" <?php echo(!isset($this->value['user-name-' . $user->id]) ? stripslashes($this->value['user-name-' . $user->id]) : 'style="display:none"'); ?>>
			<? if (in_array($user->id, $moder_list)) { ?>
                <i class="moder fas fa-user-edit"></i>
			<? } ?>

            <input style="display:none" type="checkbox" class="myCheckbox" id="user-<?php echo $user->userid; ?>"
                   name="jform[fields][<?php echo $this->id; ?>][user-thumb-<?php echo $user->id; ?>]"
                   value="<?php echo $user->thumb ?>">

            <input style="display:none" type="checkbox" class="myCheckbox2" id="user-<?php echo $user->userid; ?>"
                   name="jform[fields][<?php echo $this->id; ?>][user-name-<?php echo $user->id; ?>]"
                   value="<?php echo $user->name ?>">

            <input style="display:none" type="checkbox" class="myCheckbox" id="user-link-<?php echo $user->userid; ?>"
                   name="jform[fields][<?php echo $this->id; ?>][user-link-<?php echo $user->id; ?>]"
                   value="<?php echo $link ?>">

            <input type="hidden" type="checkbox" name="jform[fields][<?php echo $this->id; ?>][cr_id-<?php echo $user->id; ?>]"
                   name="jform[fields][<?php echo $this->id; ?>][cr_id-<?php echo $user->id; ?>]"
                   value="<?php echo $user->id ?>">

            <!-- avatarka :) -->
            <div class="joms-list__avatar joms-avatar  craft">
				<?php if (isset($user->thumb) && !empty($user->thumb)) { ?>
                    <img src="<?php echo $user->thumb; ?>" title="<?php echo $user->name; ?>"
                         alt="<?php echo $user->name; ?>" data-author="<?php echo $user->userid; ?>"/>
				<? } else { ?>
                    <img src="/components/com_community/assets/user-Male-thumb.png" title="<?php echo $user->name; ?>"
                         alt="<?php echo $user->name; ?>" data-author="<?php echo $user->userid; ?>">
				<? } ?>
            </div>
            <div class="joms-list__body craft">
                <!-- name -->
                <h4 class="joms-text--username craft"><?php echo $user->name; ?></h4>
            </div>
			<? if (in_array($user->id, $moder_list)) { ?>
                <button class='moder-btn js-moder' onclick="moders(this); event.stopPropagation(); return false" name="moder" value="<?= $user->id ?>">
                    Запретить<br>модерировать
                </button>
			<? } else { ?>
            <button class='moder-btn' onclick="moders(this); event.stopPropagation(); return false" name="moder"
                    value="<?= $user->id ?>">Сделать<br>модератором</button><? } ?>

        </li>
	<?php } ?>
</ul>
<script type="text/javascript">
    function myfun(element) { // Выбор юзеров, скрытие и копирование их вверх вниз
        var inputs = element.getElementsByTagName('input');
        for (var i = 0, inputs_len = inputs.length; i < inputs_len; i++) {
            inputs[i].checked = inputs[i].checked ? false : true;
            /***if (inputs[i].checked) {
		element.classList.add('chosen-us');
		} else {
		element.classList.remove('chosen-us');
		}***/


        }
        if (element.parentNode == document.querySelector("#u-chose")) {
            document.querySelector("#chosen-u").appendChild(element);
        } else {
            for (var i = 0, inputs_len = inputs.length; i < inputs_len; i++) {
                inputs[i].checked = inputs[i].checked ? false : true;
            }
            document.querySelector("#u-chose").prepend(element);
            const icon2 = element.querySelector('.svg-inline--fa');// todo сделать удаление записи модерации юзеров из базы при полном удалении
            let btnn = element.querySelector('.js-moder');
            let params = new FormData();
            params.set('delete', 'true');
            params.set('cuser_id', btnn.value);
            params.set('moder_record', '<?= $this->record->id; ?>');
            fetch('/components/com_cobalt/fields/socusers/tmpl/input/moderators.php', {
                method: 'POST',
                body: params
            }).then(function (response) {
                btnn.removeClass("js-moder");
                btnn.innerHTML = 'Сделать<br>модератором';
                console.log(response);
                icon2.remove();
            }).catch(function (error) {
                //console.log('Произошла ошибка, обратитесь к администратору');
                console.log(error);
            });






        }
    }

    function alert1(element) {
       // alert('Это осознанное решение :) ?')
    }

    function myfun2(element) { // Удаление сохраненных юзеров
        var inputs = element.parentNode.getElementsByTagName('input');
        for (var i = 0, inputs_len = inputs.length; i < inputs_len; i++) {
            inputs[i].checked = inputs[i].checked ? false : false;
        }
        element.parentNode.style.display = "none";
        //document.querySelector("#u-chose").prepend(element.parentNode); // todo
    }

    document.addEventListener('DOMContentLoaded', function () { // Прячем отмеченных юзеров
        var contr = document.getElementById('chosen-u');
        var inputs = contr.getElementsByTagName('input');
        for (var i = 0; i < inputs.length; i++)
            if (inputs[i].checked) {
                inputs[i].parentNode.style.display = 'block';
            } else {
                inputs[i].parentNode.style.display = 'none';
            }
    });

    /******** Craft-поиск-search  *********/
    function searchdrop() { // Кнопка всех пользователей
        var opbtn = document.getElementById("u-chose");
        var btbtn = document.getElementById("btbtn");
        opbtn.classList.toggle("flex");
        //document.getElementById("s-craft-s").value = "";
        if (btbtn.innerHTML === "Выбрать пользователей") {
            btbtn.innerHTML = "Скрыть пользователей";
            document.getElementById("s-craft-s").style.display = "block";
        } else {
            btbtn.innerHTML = "Выбрать пользователей";
            document.getElementById("s-craft-s").style.display = "none";
        }
    }

    function filterFunction() { // Фильтр по пользователям
        document.querySelector('#u-chose').classList.add("flex");
        var input, filter, ul, li, a, i;
        input = document.getElementById("s-craft-s");
        filter = input.value.toUpperCase();
        div = document.getElementById("u-chose");
        a = div.getElementsByTagName("li");
        for (i = 0; i < a.length; i++) {
            txtValue = a[i].textContent || a[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                a[i].style.display = "";
            } else {
                a[i].style.display = "none";
            }
        }
    }
    // Запрос к БД добавление и удаление администраторов @TODO ДОДДЕЛАТЬ ПОКАЗ СКРЫТИЕ ВЫБРАННЫХ ЮЗЕРОВ С УЧЕТОМ КНОПКИ МОДЕРАЦИИ
    function moders(element) {
        event.preventDefault();
        const icon = document.createElement('i');
        icon.className = 'moder fas fa-user-edit';
        let params = new FormData();
        if (element.classList.contains("js-moder")) {
             params.set('delete', 'true');
         }
        else {
            params.set('save', 'true');
        }
        params.set('cuser_id', element.value);
        params.set('moder_record', '<?= $this->record->id; ?>');
        fetch('/components/com_cobalt/fields/socusers/tmpl/input/moderators.php', {
            method: 'POST',
            body: params
        }).then(function (response) {
            console.log(response);
            if (element.className === ("moder-btn js-moder")) {
                const icon2 = element.parentNode.querySelector('.svg-inline--fa');
                element.removeClass("js-moder");
                element.innerHTML = 'Сделать<br>модератором';
                icon2.parentNode.removeChild(icon2);
            } else {
                element.addClass("js-moder");
                element.innerHTML = 'Запретить<br>модерировать';
                element.parentNode.prepend(icon);
            }
        }).catch(function (error) {
            console.log('Произошла ошибка, обратитесь к администратору');
            console.log(error)
        });

    }

</script>






		
		
		
		
		
		
		
