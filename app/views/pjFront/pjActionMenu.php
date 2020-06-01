<?php
include_once PJ_VIEWS_PATH . 'pjFront/elements/header.php';
?>

<div class="container-fluid pjMbContainer pjMbContent pjMbProductCategories">
	<header class="pjMbContentHead">
		<h1 class="pjMbContentTitle"><?php __('front_menu_title');?></h1><!-- /.pjMbContentTitle -->

		<p><?php __('front_menu_desc');?></p>
	</header><!-- /.pjMbContentHead -->
	
	<div class="pjMbContentBody">
		<div class="row">
			<?php
			if(!empty($tpl['category_arr']))
			{
				foreach($tpl['category_arr'] as $category)
				{
					$image_url = null;
					if(!empty($category['image']))
					{
						$image_url = PJ_INSTALL_URL . $category['image'];
					}
					?>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<a href="#" class="thumbnail pjMbProductCategory pjMbCategoryItem" data-id="<?php echo $category['id']; ?>">
							<div class="pjMbCategoryImage">
								<?php
								if($image_url != null)
								{ 
									?>
									<img src="<?php echo $image_url;?>" class="img-responsive" alt="" />
									<?php
								} 
								?>
							</div><!-- /.pjMbCategoryImage -->
				
							<div class="caption pjMbCategoryContent">
								<div class="pjMbCategoryContentInner">
									<h2 class="pjMbCategoryTitle"><?php echo pjSanitize::html($category['name']);?></h2><!-- /.pjMbCategoryTitle -->
									<?php
									$desc = pjUtil::truncateDescription($category['description'], 160, ' '); 
									?>					
									<p><?php echo nl2br(pjSanitize::html($desc));?></p>
								</div><!-- /.pjMbCategoryContentInner -->
							</div><!-- /.caption pjMbCategoryContent -->
						</a>
					</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-12 -->
					<?php
				}
			} else {
				?>
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
					<?php __('front_no_menus_available');?>
				</div><!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-12 -->
				<?php
			}
			?>
		</div><!-- /.row -->
	</div><!-- /.pjMbContentBody -->
</div><!-- /.container-fluid pjMbContainer pjMbContent pjMbProductCategories -->