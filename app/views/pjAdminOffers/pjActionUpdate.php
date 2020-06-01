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
	
	pjUtil::printNotice(__('infoUpdateSpecialOfferTitle', true, false), __('infoUpdateSpecialOfferDesc', true, false)); ?>
	<div class="pj-loader-outer">
		<div class="pj-loader"></div>
		<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
		<div class="multilang"></div>
		<?php endif; ?>
		
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOffers&amp;action=pjActionUpdate" method="post" id="frmUpdateOffer" class="form pj-form" autocomplete="off" enctype="multipart/form-data">
			<input type="hidden" name="offer_update" value="1" />
			<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
			?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('lblOfferTitle'); ?></label>
					<span class="inline_block">
						<input type="text" id="i18n_name_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['name'])); ?>" lang="<?php echo $v['id']; ?>" />
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
			<div class="p">
				<label class="title"><?php __('lblProduct');?></label>
				<div id="pjMbProductBox" class="block float_left">
					<?php
					$cnt = 0;
					foreach($tpl['product_id_arr'] as $product_id => $size_id)
					{
						$index = 'fd_' . rand(1, 999999);
						?>
						<div class="pjMbProductRow overflow" data-index="<?php echo $index;?>">
							<div class="pjMbProductList float_left r10">
								<select id="product_id_<?php echo $index;?>" name="product_id[<?php echo $index;?>]" class="pj-form-field w300 pjProductList required" data-index="<?php echo $index;?>">
									<option value="">-- <?php __('lblSelectProduct');?> --</option>
									<?php
									foreach ($tpl['product_arr'] as $arr)
									{
										foreach($arr as $v )
										{
											$name_arr = array();
											if(!empty($v['parent_category']))
											{
												$name_arr[] = $v['parent_category'];
											}
											if(!empty($v['category']))
											{
												$name_arr[] = $v['category'];
											}
											$name_arr[] = $v['name'];
											?><option value="<?php echo $v['id']; ?>"<?php echo $product_id == $v['id'] ? ' selected="selected"' : NULL; ?> data-size="<?php echo $v['set_different_sizes']?>"><?php echo join(", ", $name_arr); ?></option><?php
										}
									}
									?>
								</select>
							</div>
							<div id="pjMbProductSize_<?php echo $index;?>" class="float_left r10" style="display: <?php echo !empty($size_id) ? 'block' : 'none';?>;">
								<?php
								if(!empty($size_id))
								{
									if(isset($tpl['size_arr'][$size_id]))
									{
										?>
										<select name="size_id[<?php echo $index;?>]" class="pj-form-field w150 required pjMbSize">
											<option value="">-- <?php __('lblSelectSize');?> --</option>
											<?php
											foreach($tpl['size_arr'][$size_id] as $size)
											{
												?><option value="<?php echo $size['id']?>"<?php echo $size['id'] == $size_id ? ' selected="selected"' : null; ?>><?php echo $size['size'];?> - <?php echo pjUtil::formatCurrencySign($size['price'], $tpl['option_arr']['o_currency'])?></option><?php
											} 
											?>
										</select>
										<?php
									}
								} 
								?>
							</div>
							<?php
							if($cnt > 0)
							{ 
								?>
								<div class="float_left">
									<input type="button" value="<?php __('btnRemove'); ?>" class="pj-button pj-remove-size" />
								</div>
								<?php
							} 
							?>
						</div><!-- pjMbProductRow -->
						<?php
						$cnt++;
					} 
					?>
				</div><!-- #pjMbProductBox -->
			</div><!-- \.p -->
			<p>
				<label class="title">&nbsp;</label>
				<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button pj-add-product" />
			</p>
			<p>
				<label class="title"><?php __('lblPeople'); ?></label>
				<span class="inline-block">
					<input type="text" id="people" name="people" value="<?php echo $tpl['arr']['people'];?>" class="pj-form-field positive field-int w80" data-msg-positive="<?php __('lblPositiveNumber'); ?>"/>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblPrice'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" id="price" name="price" value="<?php echo $tpl['arr']['price'];?>" class="pj-form-field positive w80" data-msg-positive="<?php __('lblPositiveNumber'); ?>"/>
				</span>
			</p>
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
						<img class="mb-image pjMbOfferImage" src="<?php echo $image_url; ?>" />
						<a href="javascript:void(0);" class="pj-delete-image" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOffers&amp;action=pjActionDeleteImage&id=<?php echo $tpl['arr']['id'];?>"><?php __('btnDelete');?></a>
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
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminOffers&action=pjActionIndex';" />
				</span>
			</p>
		</form>
	</div>
	
	<div id="pjMbProductClone" style="display: none;">
		<div class="pjMbProductRow overflow" data-index="{INDEX}">
			<div class="pjMbProductList float_left r10">
				<select id="product_id_{INDEX}" name="product_id[{INDEX}]" class="pj-form-field w300 pjProductList required"  data-index="{INDEX}">
					<option value="">-- <?php __('lblSelectProduct');?> --</option>
					<?php
					foreach ($tpl['product_arr'] as $arr)
					{
						foreach($arr as $v )
						{
							$name_arr = array();
							if(!empty($v['parent_category']))
							{
								$name_arr[] = $v['parent_category'];
							}
							if(!empty($v['category']))
							{
								$name_arr[] = $v['category'];
							}
							$name_arr[] = $v['name'];
							?><option value="<?php echo $v['id']; ?>" data-size="<?php echo $v['set_different_sizes']?>"><?php echo join(", ", $name_arr); ?></option><?php
						}
					}
					?>
				</select>
			</div>
			<div id="pjMbProductSize_{INDEX}" class="float_left r10" style="display: none;">
			</div>
			<div class="float_left">
				<input type="button" value="<?php __('btnRemove'); ?>" class="pj-button pj-remove-size" />
			</div>
		</div><!-- pjMbProductRow -->
	</div>
	
	<div id="dialogDeleteImage" style="display: none" title="<?php __('lblDeleteImage');?>"><?php __('lblDeleteImageConfirm');?></div>
	
	<script type="text/javascript">
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
	var pjLocale = pjLocale || {};
	var myLabel = myLabel || {};
	var locale_array = new Array(); 
	pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	
	myLabel.field_required = "<?php __('lblFieldRequired'); ?>";
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>locale_array.push(<?php echo $v['id'];?>);<?php
	} 
	?>
	myLabel.locale_array = locale_array;
	myLabel.same_offer = "<?php __('lblSameSpecialOffer'); ?>";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
					
				}
			});
		});
	})(jQuery_1_8_2);
	<?php endif; ?>
	</script>
	<?php
}
?>