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
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	$_yesno = __('_yesno', true);
	
	pjUtil::printNotice(__('infoUpdateProductTitle', true, false), __('infoUpdateProductDesc', true, false)); 
	?>
	
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionUpdate" method="post" id="frmUpdateProduct" class="form pj-form" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="product_update" value="1" />
		<input type="hidden" id="index_arr" name="index_arr" value="" />
		<input type="hidden" id="remove_arr" name="remove_arr" value="" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']?>" />
		<?php
		foreach ($tpl['lp_arr'] as $v)
		{
		?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('lblName'); ?></label>
				<span class="inline_block">
					<input type="text" id="i18n_name_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['name'])); ?>" />
					<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					<?php endif; ?>
				</span>
			</p>
			<?php
		}
		foreach ($tpl['lp_arr'] as $v)
		{
		?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('lblDescription'); ?></label>
				<span class="inline_block">
					<textarea id="i18n_description_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][description]" class="pj-form-field w500 h150<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>"><?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['description'])); ?></textarea>
					<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					<?php endif; ?>
				</span>
			</p>
			<?php
		}
		?>
		<p>
			<label class="title"><?php __('lblCategory'); ?></label>
			<span class="inline_block" id="boxCategory">
				<select name="category_id" id="category_id" class="pj-form-field required w300">
					<?php
					foreach ($tpl['category_arr'] as $v)
					{
						?><option value="<?php echo $v['id']; ?>"<?php echo $v['id'] == $tpl['arr']['category_id'] ? ' selected="selected"' : null;?>><?php echo (!empty($v['parent_id']) ? '------' : null) . stripslashes($v['name']); ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblSetDifferentSizes'); ?></label>
			<span class="inline_block">
				<span class="block float_left r10 t5">
					<input type="radio" id="set_yes" name="set_different_sizes" value="T"<?php echo $tpl['arr']['set_different_sizes'] == 'T' ? ' checked="checked"' : null;?>/><label for="set_yes"><?php echo $_yesno['T']; ?></label>
				</span>
				<span class="block float_left r10 t5">
					<input type="radio" id="set_no" name="set_different_sizes" value="F"<?php echo $tpl['arr']['set_different_sizes'] == 'F' ? ' checked="checked"' : null;?>/><label for="set_no"><?php echo $_yesno['F']; ?></label>
				</span>
			</span>
		</p>
		<p id="signle_price" style="display: <?php echo $tpl['arr']['set_different_sizes'] == 'F' ? 'block' : 'none';?>;">
			<label class="title"><?php __('lblPrice'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" id="price" name="price" class="pj-form-field pj-positive-number w80" value="<?php echo $tpl['arr']['price']?>"/>
			</span>
		</p>
		<div id="multiple_prices" style="display: <?php echo $tpl['arr']['set_different_sizes'] == 'T' ? 'block' : 'none';?>;">
			<p class="pj-size-count pj-size-title">
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<label class="content float_left r218"><?php __('lblSize');?></label>
					<label class="content float_left"><?php __('lblPrice');?></label>
				</span>
			</p>
			<div id="fd_size_list" class="fd-size-list">
				<?php
				if(isset($tpl['size_arr']))
				{
					foreach($tpl['size_arr'] as $k => $size)
					{
						?>
						<div class="fd-size-row" data-index="<?php echo $size['id'];?>">
							<?php
							foreach ($tpl['lp_arr'] as $v)
							{
								?>
								<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
									<label class="title fd-title-<?php echo $size['id'];?>"><?php __('lblSize'); ?> <?php echo $k + 1;?>:</label>
									<span class="inline_block">
										<input type="text" name="i18n[<?php echo $v['id']; ?>][price_name][<?php echo $size['id'];?>]" class="pj-form-field float_left r3 w200<?php echo (int) $v['is_default'] === 0 ? NULL : ' fdRequired'; ?>" lang="<?php echo $v['id']; ?>" value="<?php echo htmlspecialchars(stripslashes(@$size['i18n'][$v['id']]['price_name'])); ?>"/>
										<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
										<span class="pj-multilang-input float_left r10"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
										<?php endif;?>
									</span>
								</p>
								<?php
							}
							?>
							<div class="pj-size-count">
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" name="product_price[<?php echo $size['id'];?>]" class="pj-form-field pj-positive-number w80" value="<?php echo $size['price']; ?>"/>
								</span>
							</div>
							<?php
							if($k > 0)
							{ 
								?>
								<div class="size-icons">
									<input type="button" value="<?php __('btnRemove'); ?>" class="pj-button pj-remove-size" />
								</div>
								<?php
							} 
							?>
						</div>
						<?php
					}
				} else{
					$index = 'fd_' . rand(1, 999999); 
					?>
					<div class="fd-size-row" data-index="<?php echo $index;?>">
						<?php
						foreach ($tpl['lp_arr'] as $v)
						{
							?>
							<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
								<label class="title fd-title-<?php echo $index;?>"><?php __('lblSize'); ?> 1:</label>
								<span class="inline_block">
									<input type="text" name="i18n[<?php echo $v['id']; ?>][price_name][<?php echo $index;?>]" class="pj-form-field float_left r3 w200<?php echo (int) $v['is_default'] === 0 ? NULL : ' fdRequired'; ?>" lang="<?php echo $v['id']; ?>"/>
									<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
									<span class="pj-multilang-input float_left r10"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
									<?php endif;?>
								</span>
							</p>
							<?php
						}
						?>
						<div class="pj-size-count">
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="product_price[<?php echo $index;?>]" class="pj-form-field pj-positive-number w80"/>
							</span>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<p>
				<label class="title">&nbsp;</label>
				<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button pj-add-size" />
			</p>
		</div>
		<p>
			<label class="title"><?php __('lblImage', false, true); ?></label>
			<span class="inline_block">
				<input type="file" name="image" id="image" class="pj-form-field w300"/>
			</span>
		</p>
		<?php
		if(!empty($tpl['arr']['image']))
		{
			$image_url = PJ_INSTALL_URL . $tpl['arr']['image'];
			?>
			<p id="image_container">
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<img class="fd-image" src="<?php echo $image_url; ?>" />
					<a href="javascript:void(0);" class="pj-delete-image" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionDeleteImage&id=<?php echo $tpl['arr']['id'];?>"><?php __('btnDelete');?></a>
				</span>
			</p>
			<?php
		} 
		?>
		<p>
			<label class="title"><?php __('lblStatus'); ?></label>
			<span class="inline_block">
				<select name="status" id="status" class="pj-form-field required">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach (__('u_statarr', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['status'] ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<span class="inline_block">
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminProducts&action=pjActionIndex';" />
			</span>
		</p>
	</form>
	<div id="dialogDeleteImage" style="display: none" title="<?php __('lblDeleteImage');?>"><?php __('lblDeleteImageConfirm');?></div>
	
	<div id="fd_size_clone" style="display: none;">
		<div class="fd-size-row" data-index="{INDEX}">
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title fd-title-{INDEX}"><?php __('lblSize'); ?> {ORDER}:</label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][price_name][{INDEX}]" class="pj-form-field float_left r3 w200<?php echo (int) $v['is_default'] === 0 ? NULL : ' fdRequired'; ?>" lang="<?php echo $v['id']; ?>"/>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input float_left r10"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</span>
				</p>
				<?php
			}
			?>
			<div class="pj-size-count">
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="product_price[{INDEX}]" class="pj-form-field pj-positive-number w80"/>
				</span>
			</div>
			<div class="size-icons">
				<input type="button" value="<?php __('btnRemove'); ?>" class="pj-button pj-remove-size" />
			</div>
		</div>
	</div>
	<script type="text/javascript">
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
	var pjLocale = pjLocale || {};
	var myLabel = myLabel || {};
	myLabel.choose = "-- <?php __('lblChoose'); ?> --";
	var locale_array = new Array(); 
	pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	
	myLabel.field_required = "<?php __('fd_field_required'); ?>";
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>locale_array.push(<?php echo $v['id'];?>);<?php
	} 
	?>
	myLabel.locale_array = locale_array;
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
					$.get("index.php?controller=pjAdminProducts&action=pjActionGetLocale", {
						"locale" : ui.index
					}).done(function (data) {
						cid = $("#category_id").val();
						$("#boxCategory").html(data.category);
						$("#category_id").val(cid);
					});
				}
			});
		});
	})(jQuery_1_8_2);
	<?php endif; ?>
	</script>
	<?php
}
?>