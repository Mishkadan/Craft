<?php
/**
 * @copyright Copyright (C) 2022 Craft All rights reserved.
 */
defined('_JEXEC') or die();

//$valuq = new JRegistry($this->value);
$doc        = JFactory::getDocument();
$directory  = $this->directory;
$jinput     = JFactory::getApplication()->input;
$viewName   = $jinput->get('view');
$taskName   = $jinput->get('task');
$valueq = $this->value;
$list       = $this->getAlluser();
//$moders     = $this->getModers(); // TODO Модераторы
$jomuser = JFactory::getUser();
$moders_list = array();
$rid = $this->record->id;
echo $rid;
//var_dump($moders);
//foreach ($moders as $k=>$vl) // TODO Модераторы
//{
//	$moders_list[$k] = $vl->cuser_id;
//}


$links  = array_filter($valueq, function ($value, $key) {
	if (strpos($key, 'link') !== false)
	{
		return $value;
	}
}, ARRAY_FILTER_USE_BOTH);
$links1 = array_values($links);
$names  = array_filter($valueq, function ($vl, $kly) {
	if (strpos($kly, 'name') !== false)
	{
		return $vl;
	}
}, ARRAY_FILTER_USE_BOTH);
$names1 = array_values($names);
$juser =JFactory::getUser();
?>

<label class="admins"><?php echo 'Профили'; ?></label>
<ul id="profilss" class="joms-list__item c-raft" style="list-style:none; margin-left:0;">
	<?php
	$iii = 0;
	$zzz = 0;
	foreach ($valueq as $k => $v)
	{ ?>
		<?php
		if (strpos($k, 'user') !== false)
		{
			$ln = $v;
		}
		if (strpos($k, 'thumb') !== false)
		{
			$th = $v; ?>
            <li class="joms-list__item c-raft">

                <!-- avatarka :) -->
                <div class="joms-list__avatar joms-avatar  craft">
					<?php if (isset($th) && !empty($th)) {
						; ?>
                        <a href="<?php
						echo $links1[$iii++]; ?> "><img src="<?php echo $th; ?>" title="<?php echo $user->name; ?>"
                                                        alt="<?php echo $user->name; ?>"
                                                        data-author="<?php echo $user->userid; ?>"/></a>
					<?php } else { ?>
                        <a href="<?php
						echo $links1[$iii++]; ?> "><img src="/components/com_community/assets/user-Male-thumb.png"
                                                        title="<?php echo $user->name; ?>"
                                                        alt="<?php echo $user->name; ?>"
                                                        data-author="<?php echo $user->id; ?>"></a>
						<?php
					} ?>
                </div>

                <div class="joms-list__body craft">
                    <!-- name -->
                    <h4 class="joms-text--username craft"><?php echo $names1[$zzz++]; ?></h4>
                </div>




            </li>
		<?php }

	};
	?>

<!--	--><?// if (in_array('cuser_id', $moders_list)) { ?>
<!--        <i class="moder fas fa-user-edit"></i>-->
<!--	--><?// }// echo $user->id;
//	foreach ($list  as $cuser)
//	{
//		$newuser = $cuser->id;
//
//	}
//	echo $newuser;
//	?>


</ul>





<script>
    document.addEventListener('DOMContentLoaded', function adminmove() {
        var admin = document.getElementById('admininfo');
        document.querySelector("#profilss").prepend(admin);
    });
    //adminmove();
</script>
