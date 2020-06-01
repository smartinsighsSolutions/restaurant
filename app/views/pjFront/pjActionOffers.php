<?php
include_once PJ_VIEWS_PATH . 'pjFront/elements/header.php';
?>

<div class="container-fluid pjMbContainer pjMbContent">
	<header class="pjMbContentHead">
		<h1 class="pjMbContentTitle"><?php __('front_offer_title');?></h1><!-- /.pjMbContentTitle -->

		<p><?php __('front_offer_desc');?></p>
	</header><!-- /.pjMbContentHead -->

	<div class="pjMbContentBody">
		<div class="row pjMbOffers">
			<?php
			if(!empty($tpl['arr']))
			{ 
				foreach($tpl['arr'] as $offer)
				{
					$image_url = null;
					if(!empty($offer['image']))
					{
						$image_url = PJ_INSTALL_URL . $offer['image'];
					}
					?>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pjMbOffer">
						<a href="#" class="thumbnail pjMbSpecialOffer" data-id="<?php echo $offer['id'];?>">
							<div class="pjMbOfferImage">
								<?php
								if($image_url != null)
								{ 
									?>
									<img src="<?php echo $image_url;?>" alt="" />
									<?php
								} 
								?>
							</div><!-- /.pjMbOfferImage -->
		
							<div class="pjMbOfferOverlay">
								<div class="pjMbOfferMeta">
									<p class="pjMbOfferPrice"><?php echo pjUtil::formatCurrencySign($offer['price'], $tpl['option_arr']['o_currency'])?></p><!-- /.pjMbOfferPrice -->
									<p>/ <?php echo $offer['people'];?> <?php echo $offer['people'] != 1 ? __('front_people') : __('front_person');?></p>
								</div><!-- /.pjMbOfferMeta -->
		
								<div class="pjMbOfferContent">
									<h2 class="pjMbOfferTitle"><?php echo pjSanitize::html($offer['name']);?></h2><!-- /.pjMbOfferTitle -->
								</div><!-- /.pjMbOfferContent -->
							</div><!-- /.pjMbOfferOverlay -->
						</a>
					</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-12 pjMbOffer -->
					<?php
				}
			}else{
				?>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pjMbOffer">
					<?php __('front_offer_not_found');?>
				</div>
				<?php
			} 
			?>
		</div><!-- /.row pjMbOffers -->
	</div><!-- /.pjMbContentBody -->
</div><!-- /.container-fluid pjMbContainer pjMbContent -->