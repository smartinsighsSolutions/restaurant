<div class="container-fluid pjMbContainer">
	<nav class="navbar navbar-default pjMbHeader">
		<ul class="nav navbar-nav navbar-left pjMbNav">
			<li<?php echo $_GET['action'] == 'pjActionMenu' ? ' class="active"' : null;?>>
				<a href="#" class="pjMbMenu">
					<i class="fa fa-file-text-o"></i>
					<?php __('front_menu');?>
				</a>
			</li><!-- /.dropdown -->

			<li<?php echo $_GET['action'] == 'pjActionOffers' ? ' class="active"' : null;?>>
				<a href="#" class="pjMbSpecialOffers">
					<i class="fa fa-star"></i>
					<?php __('front_special_offers');?>
				</a>
			</li>
		</ul><!-- /.nav navbar-nav navbar-left pjMbNav -->
		
		<div class="nav navbar-nav navbar-right pjMbHeaderOptions">
			<?php
            if (!empty($tpl['category_arr'])) {
                ?>
				<div class="navbar-form pull-left pjMbCategories">
					<div class="form-group">
						<label for="" class="control-label"><?php __('front_category'); ?></label>
	
						<div class="btn-group clearfix">
							<?php
                            $selected_category = null;
                if (isset($_GET['cid']) && (int) $_GET['cid'] > 0) {
                    foreach ($tpl['category_arr'] as $category) {
                        if ($_GET['cid'] == $category['id']) {
                            $selected_category = (!empty($category['parent_id']) ? '----' : null) . stripslashes($category['name']);
                            break;
                        }
                    }
                }
                if ($selected_category != null) {
                    ?>
								<button type="button" class="btn btn-default dropdown-toggle form-control" data-toggle="dropdown" aria-expanded="false">
									<span><?php echo $selected_category; ?></span>
									<i class="fa fa-caret-down"></i>
								</button>
								<?php
                } else {
                    ?>
								<button type="button" class="btn btn-default dropdown-toggle form-control" data-toggle="dropdown" aria-expanded="false">
									<span>-- <?php __('front_all_categories'); ?> --</span>
									<i class="fa fa-caret-down"></i>
								</button>
								<?php
                } ?>
						
							<ul class="dropdown-menu" role="menu">
								<?php
                                foreach ($tpl['category_arr'] as $category) {
                                    ?><li><a href="#" class="pjMbCategoryItem" data-id="<?php echo $category['id']?>"><?php echo(!empty($category['parent_id']) ? '----' : null) . stripslashes($category['name']); ?></a></li><?php
                                } ?>
							</ul><!-- /.dropdown-menu -->
						</div><!-- /.btn-group clearfix -->
					</div><!-- /.form-group -->
				</div><!-- /.navbar-form pull-left pjMbCategories -->
				<?php
            }
             
            if (isset($tpl['locale_arr']) && is_array($tpl['locale_arr']) && !empty($tpl['locale_arr']) && count($tpl['locale_arr']) > 1) {
                $locale_id = $controller->pjActionGetLocale();
                $selected_lang = '';
                foreach ($tpl['locale_arr'] as $locale) {
                    if ($locale_id == $locale['id']) {
                        $selected_lang = pjSanitize::html($locale['title']);
                    }
                } ?>
				
				<div class="navbar-form pull-left pjMbLanguages">
					<div class="form-group">
						<label for="" class="control-label"><?php __('front_language'); ?></label>
	
						<div class="btn-group">
							<button type="button" class="btn btn-default dropdown-toggle form-control" data-toggle="dropdown" aria-expanded="false">
								<?php echo $selected_lang; ?>
								<i class="fa fa-caret-down"></i>
							</button>
						
							<ul class="dropdown-menu" role="menu">
								<?php
                                foreach ($tpl['locale_arr'] as $k => $locale) {
                                    ?><li><a href="#" class="pjMbSelectorLocale" data-id="<?php echo $locale['id']; ?>"><?php echo pjSanitize::html($locale['title']); ?></a></li><?php
                                } ?>
							</ul><!-- /.dropdown-menu -->
						</div><!-- /.btn-group -->
					</div><!-- /.form-group -->
				</div><!-- /.navbar-form pull-left pjMbLanguages -->
				<?php
            }
            ?>
		</div><!-- /.navbar-right pjMbHeaderOptions -->
	</nav><!-- /.navbar navbar-default pjMbHeader -->
</div><!-- /.container-fluid pjMbContainer -->