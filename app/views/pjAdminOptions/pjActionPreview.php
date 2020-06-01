<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	
	pjUtil::printNotice(__('infoPreviewTitle', true), __('infoPreviewDesc', true), false, false)
	?>
	<div class="pj-loader-outer">
		<fieldset class="fieldset white">
			<legend><?php __('lblChooseTheme'); ?></legend>
			<div class="theme-holder">
				<?php include PJ_VIEWS_PATH . 'pjAdminOptions/elements/theme.php'; ?>
			</div>
		</fieldset>
	</div>
	<?php
}
?>