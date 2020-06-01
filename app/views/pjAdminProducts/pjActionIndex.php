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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		$bodies_text = str_replace("{SIZE}", ini_get('post_max_size'), @$bodies[$_GET['err']]);
		pjUtil::printNotice(@$titles[$_GET['err']], $bodies_text);
	}
	$_yesno = __('_yesno', true);
	$u_statarr = __('u_statarr', true);
	pjUtil::printNotice(__('infoProductsTitle', true, false), __('infoProductsDesc', true, false)); 
	?>
	
	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminProducts" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnPlusAddProduct'); ?>" />
		</form>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w200" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		
		<div class="float_right pj-form">
			<select id="filter_category_id" name="filter_category_id" class="pj-form-field w200 float_right">
				<option value="">-- <?php __('lblAllCategories');?> --</option>
				<?php
				foreach($tpl['category_arr'] as $v)
				{
					?><option value="<?php echo $v['id']?>"<?php echo isset($_GET['category_id']) ? ($_GET['category_id'] == $v['id'] ? ' selected="selected"' : NULL) : NULL;?>><?php echo !empty($v['parent_id']) ? '----' . $v['name'] : $v['name']; ?></option><?php
				} 
				?>
			</select>
			<label class="block float_right r10 t6"><?php __('lblFilterBy');?></label>
		</div>
		
		<br class="clear_both" />
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['category_id']) && (int) $_GET['category_id'] > 0)
	{
		?>pjGrid.queryString += "&category_id=<?php echo (int) $_GET['category_id']; ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.image = "<?php __('lblImage'); ?>";
	myLabel.name = "<?php __('lblName'); ?>";
	myLabel.category = "<?php __('lblCategory'); ?>";
	myLabel.price = "<?php __('lblPrice'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.yes = "<?php echo $_yesno['T']; ?>";
	myLabel.no = "<?php echo $_yesno['F']; ?>";
	myLabel.active = "<?php echo $u_statarr['T']; ?>";
	myLabel.inactive = "<?php echo $u_statarr['F']; ?>";	
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	myLabel.status = "<?php __('lblStatus', false, true); ?>";
	</script>
	<?php
}
?>