<?php if(count($list > 1)): ?>
	<ul class="nav nav-tabs" id="js-cobalt-tab<?php echo $this->params->get('section_id') ?>">
		<?php foreach($list AS $vw): ?>
			<li><a href="#tab-<?php echo $vw . $this->params->get('section_id') ?>" data-toggle="tab">
					<?php echo JText::_($this->params->get('vw_' . $vw, 'vw_' . $vw)); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="tab-content">
		<?php foreach($list AS $vw): ?>
			<?php
			$api    = new CobaltApi();
			$result = $api->records($this->params->get('section_id'), $vw, $this->params->get('orderby'), $this->params->get('types', 0), $userid, 0, $this->params->get('limit', 5), $this->params->get('tmpl'));
			?>
			<div class="tab-pane" id="tab-<?php echo $vw . $this->params->get('section_id') ?>">
				<?php echo $result['html']; ?>
			</div>
		<?php endforeach; ?>
	</div>

	<script type="text/javascript">
		jQuery('#js-cobalt-tab<?php echo $this->params->get('section_id') ?> a:first').tab('show');
	</script>
<?php else: ?>
	<?php
	$api    = new CobaltApi();
	$result = $api->records($this->params->get('section_id'), $list[0], $this->params->get('orderby'), $this->params->get('types', 0), $userid, 0, $this->params->get('limit', 5), $this->params->get('tmpl'));
	echo $result['html'];
	?>
<?php endif; ?>

<?php if($this->params->get('btn_section_enable')): ?>
	<a class="btn" href="<?php echo JRoute::_(Url::records($this->params->get('section_id'))) ?>">
		<?php echo JText::_($this->params->get('btn_section')); ?>
	</a>
<?php endif; ?>

<?php if($this->params->get('btn_user_enable')): ?>
	<a class="btn" href="<?php echo JRoute::_(Url::user('created', $userid, $this->params->get('section_id'))) ?>">
		<?php echo JText::_($this->params->get('btn_user')); ?>
	</a>
<?php endif; ?>