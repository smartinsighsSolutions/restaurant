<?php
if (pjObject::getPlugin('pjOneAdmin') !== null && $controller->isAdmin()) {
    $controller->requestAction(array('controller' => 'pjOneAdmin', 'action' => 'pjActionMenu'));
}
?>

<div class="leftmenu-top"></div>
<div class="leftmenu-middle">
	<ul class="menu">
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminProducts' ? 'menu-focus' : null; ?>"><span class="menu-products">&nbsp;</span><?php __('menuProducts'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminCategories' ? 'menu-focus' : null; ?>"><span class="menu-categories">&nbsp;</span><?php __('menuCategories'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOffers&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminOffers' ? 'menu-focus' : null; ?>"><span class="menu-offers">&nbsp;</span><?php __('menuSpecialOffers'); ?></a></li>
		<?php
        if ($controller->isAdmin()) {
            ?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionIndex" class="<?php echo($_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionIndex'))) || in_array($_GET['controller'], array('pjAdminLocales', 'pjBackup', 'pjLocale', 'pjSms')) ? 'menu-focus' : null; ?>"><span class="menu-options">&nbsp;</span><?php __('menuOptions'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminUsers' ? 'menu-focus' : null; ?>"><span class="menu-users">&nbsp;</span><?php __('menuUsers'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionInstall" class="<?php echo $_GET['controller'] == 'pjAdminOptions' && $_GET['action'] == 'pjActionInstall' ? 'menu-focus' : null; ?>"><span class="menu-install">&nbsp;</span><?php __('menuInstall'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" class="<?php echo $_GET['controller'] == 'pjAdminOptions' && $_GET['action'] == 'pjActionPreview' ? 'menu-focus' : null; ?>"><span class="menu-preview">&nbsp;</span><?php __('menuPreview'); ?></a></li>
			
			<?php
        }
        if ($controller->isEditor()) {
            ?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionProfile" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionProfile' ? 'menu-focus' : null; ?>"><span class="menu-users">&nbsp;</span><?php __('menuProfile'); ?></a></li><?php
        }
        ?>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionLogout"><span class="menu-logout">&nbsp;</span><?php __('menuLogout'); ?></a></li>
	</ul>
</div>
<div class="leftmenu-bottom"></div>