<?php
include_once PJ_VIEWS_PATH . 'pjFront/elements/header.php';
?>
<div class="container-fluid pjMbContainer pjMbContent">
	<?php
	if(isset($tpl['category']) && !empty($tpl['category']))
	{ 
		?>
		<header class="pjMbContentHead">
			<h1 class="pjMbContentTitle"><?php echo pjSanitize::html($tpl['category']['name']);?></h1><!-- /.pjMbContentTitle -->
	
			<p><?php echo nl2br(pjSanitize::html($tpl['category']['description']));?></p>
		</header><!-- /.pjMbContentHead -->
		<div class="pjMbContentBody">
			<?php
			if(count($tpl['main_arr']) > 0 || count($tpl['sub_categories']) > 0)
			{ 
				?>
				<div class="pjMbProducts">
					<?php
					$product_arr = $tpl['main_arr'];
					include PJ_VIEWS_PATH . 'pjFront/elements/products.php'; 
					?>
				</div><!-- /.pjMbProducts -->
				<?php
				if(count($tpl['sub_categories']) > 0)
				{
					foreach($tpl['sub_categories'] as $sub_category)
					{
						if(isset($tpl['sub_arr'][$sub_category['id']]) && count($tpl['sub_arr'][$sub_category['id']]) > 0)
						{
							?>
							<div class="pjMbProducts">
								<header class="pjMbProductsHead">
									<h2 class="pjMbProductsTitle"><?php echo pjSanitize::html($sub_category['name']);?></h2><!-- /.pjMbProductsTitle -->
								</header><!-- /.pjMbProductsHead -->
								
								<?php
								$product_arr = $tpl['sub_arr'][$sub_category['id']];
								include PJ_VIEWS_PATH . 'pjFront/elements/products.php';
								?>
							</div><!-- /.pjMbProducts -->
							<?php
						}
					}
				}
			}else{
				__('front_product_not_found');
			}
			?>
		</div><!-- /.pjMbContentBody -->
		<?php
	}else{
		?>
		<div class="pjMbContentBody">
			<br/>
			<?php __('front_product_not_found');?>
		</div>
		<?php
	} 
	?>
</div><!-- /.container-fluid pjMbContainer pjMbContent -->