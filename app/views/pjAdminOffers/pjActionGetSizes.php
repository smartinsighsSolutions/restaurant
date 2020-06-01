<select name="size_id[<?php echo $_GET['index']?>]" class="pj-form-field w150 required pjMbSize">
	<option value="">-- <?php __('lblSelectSize');?> --</option>
	<?php
	foreach($tpl['size_arr'] as $size)
	{
		?><option value="<?php echo $size['id']?>"><?php echo $size['size'];?> - <?php echo pjUtil::formatCurrencySign($size['price'], $tpl['option_arr']['o_currency'])?></option><?php
	} 
	?>
</select>