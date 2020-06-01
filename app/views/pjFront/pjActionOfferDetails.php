<?php
include_once PJ_VIEWS_PATH . 'pjFront/elements/header.php';
?>
<div class="container-fluid pjMbContainer pjMbContent">
	<header class="pjMbContentHead">
		<h1 class="pjMbContentTitle"><?php echo pjSanitize::html($tpl['arr']['name']);?></h1><!-- /.pjMbContentTitle -->

		<dl class="dl-horizontal pjMbSpecialOfferMeta">
			<dt><?php echo pjUtil::formatCurrencySign($tpl['arr']['price'], $tpl['option_arr']['o_currency']);?></dt>
			<dd><?php echo $tpl['arr']['people'];?> <?php $tpl['arr']['people'] != 1 ? __('front_people') : __('front_person'); ?></dd>
		</dl><!-- /.dl-horizontal pjMbSpecialOfferMeta -->

		<p><?php echo nl2br(pjSanitize::html($tpl['arr']['description']));?></p>
	</header><!-- /.pjMbContentHead -->
	
	<div class="pjMbContentBody pjMbOfferMenu">
		
			<?php
			foreach($tpl['product_arr'] as $k => $product)
			{
				$image_url = null;
				if(!empty($product['image']))
				{
					$image_url = PJ_INSTALL_URL . $product['image'];
				}
				
				if($k % 2 == 0)
				{
					?>
					<div class="row pjMbOfferMenuBody">
					<?php
				}
					?>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pjMbOfferMenuItem">
						<header class="pjMbOfferMenuHead">
							<h2 class="pjMbOfferMenuTitle"><?php echo pjSanitize::html($product['category']);?></h2><!-- /.pjMbOfferMenuTitle -->
						</header><!-- /.pjMbOfferMenuHead -->
						<div class="thumbnail">
							<div class="row">
								<?php
								if($image_url != null)
								{ 
									?>
									<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6">
										<div class="pjMbOfferMenuItemImage">
											<img src="<?php echo $image_url;?>" alt="" />
										</div><!-- /.pjMbOfferMenuItemImage -->
									</div><!-- /.col-lg-4 col-md-4 col-sm-5 col-xs-6 -->
									<div class="col-lg-8 col-md-8 col-sm-7 col-xs-6 pjMbOfferMenuItemContent">
										<h3 class="pjMbOfferMenuItemTitle"><?php echo pjSanitize::html($product['name']);?></h3><!-- /.pjMbOfferMenuItemTitle -->
										
										<p><?php echo nl2br(pjSanitize::html($product['description']));?></p>
									</div><!-- /.col-lg-8 col-md-8 col-sm-7 col-xs-6 pjMbOfferMenuItemContent -->
									<?php
								}else{
									?>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pjMbOfferMenuItemContent">
										<h3 class="pjMbOfferMenuItemTitle"><?php echo pjSanitize::html($product['name']);?></h3><!-- /.pjMbOfferMenuItemTitle -->
										
										<p><?php echo nl2br(pjSanitize::html($product['description']));?></p>
									</div><!-- /.col-lg-8 col-md-8 col-sm-7 col-xs-6 pjMbOfferMenuItemContent -->
									<?php
								} 
								?>
							</div><!-- /.row -->
						</div><!-- /.thumbnail -->
					</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-12 pjMbOfferMenuItem -->
					<?php
				if($k % 2 != 0)
				{
					?>
					</div>	
					<?php
				}
			} 
			?>
		
	</div><!-- /.pjMbProducts -->
</div><!-- /.container-fluid pjMbContainer pjMbContent -->