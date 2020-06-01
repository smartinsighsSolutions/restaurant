<?php
foreach ($product_arr as $k => $product) {
    $image_url = null;
    if (!empty($product['image'])) {
        $image_url = PJ_INSTALL_URL . $product['image'];
    }
    if ($k % 2 == 0) {
        ?>
		<div class="row pjMbProductsBody">
		<?php
    } ?>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pjMbProduct">
		<div class="thumbnail">
			<header class="pjMbProductHead">
				<h3 class="pjMbProductTitle"><?php echo pjSanitize::html($product['name']); ?></h3><!-- /.pjMbProductTitle -->
			</header><!-- /.pjMbProductHead -->
	
			<div class="row pjMbProductBody">
				<?php
                if ($image_url != null) {
                    ?>
					<div class="col-lg-4 col-md-4 col-sm-5 col-xs-12">
						<div class="pjMbProductImage">
							<img src="<?php echo $image_url; ?>" alt="" />
						</div><!-- /.pjMbProductImage -->
					</div><!-- /.col-lg-4 col-md-4 col-sm-5 col-xs-12 -->
		
					<div class="pjMbProductContent">
						<?php
                        if ($product['set_different_sizes'] == 'T') {
                            if (is_array($product['prices'])) {
                                foreach ($product['prices'] as $price) {
                                    list($p, $p_name) = explode("~|~", $price); ?>
									<dl class="dl-horizontal pjMbProductMeta">
										<dt><?php echo $p_name; ?></dt>
										<dd class="pjMbProductPrice"><?php echo pjUtil::formatCurrencySign($p, $tpl['option_arr']['o_currency'])?></dd>
									</dl><!-- /.dl-horizontal pjMbProductMeta -->
									<?php
                                }
                            }
                        } else {
                            ?><p class="pjMbProductPrice"><?php echo pjUtil::formatCurrencySign($product['price'], $tpl['option_arr']['o_currency'])?></p><!-- /.pjMbProductPrice --><?php
                        } ?>
		
						<p><?php echo nl2br(pjSanitize::html($product['description'])); ?></p>
					</div><!-- /.pjMbProductContent -->
					<?php
                } else {
                    ?>
					<div class="pjMbProductContent">
						<?php
                        if ($product['set_different_sizes'] == 'T') {
                            if (is_array($product['prices'])) {
                                foreach ($product['prices'] as $price) {
                                    list($p, $p_name) = explode("~|~", $price); ?>
									<dl class="dl-horizontal pjMbProductMeta">
										<dt><?php echo $p_name; ?></dt>
										<dd class="pjMbProductPrice"><?php echo pjUtil::formatCurrencySign($p, $tpl['option_arr']['o_currency'])?></dd>
									</dl><!-- /.dl-horizontal pjMbProductMeta -->
									<?php
                                }
                            }
                        } else {
                            ?><p class="pjMbProductPrice"><?php echo pjUtil::formatCurrencySign($product['price'], $tpl['option_arr']['o_currency'])?></p><!-- /.pjMbProductPrice --><?php
                        } ?>
		
						<p><?php echo nl2br(pjSanitize::html($product['description'])); ?></p>
					</div><!-- /.pjMbProductContent -->
					<?php
                } ?>
			</div><!-- /.row pjMbProductBody -->
		</div><!-- /.thumbnail -->
	</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-12 pjMbProduct -->
	<?php
    if ($k % 2 != 0) {
        ?>
		</div><!-- /.pjMbProductsBody -->
		<?php
    }
}
?>