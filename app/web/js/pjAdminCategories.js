var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateCategory = $("#frmCreateCategory"),
			$frmUpdateCategory = $("#frmUpdateCategory"),
			$dialogDeleteImage = $("#dialogDeleteImage"),
			dialog = ($.fn.dialog !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if ($frmCreateCategory.length > 0 && validate) {
			$frmCreateCategory.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				}
			});
			
			if(myLabel.locale_array.length > 0)
			{
				var locale_array = myLabel.locale_array;
				for(var i = 0; i < locale_array.length; i++)
				{
					var element = $("#i18n_name_" + locale_array[i]),
						locale = element.attr('lang');
					element.rules('add', {
						remote: {
							url: "index.php?controller=pjAdminCategories&action=pjActionCheckCategory",
							type: 'post',
							data: {locale: locale}
						},
						messages: {
					    	required: myLabel.field_required,
					    	remote: myLabel.same_category
					    }
					});
				}
			}
		}
		if ($frmUpdateCategory.length > 0 && validate) {
			$frmUpdateCategory.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				}
			});
			if(myLabel.locale_array.length > 0)
			{
				var locale_array = myLabel.locale_array;
				for(var i = 0; i < locale_array.length; i++)
				{
					var element = $("#i18n_name_" + locale_array[i]),
						locale = element.attr('lang'),
						id = $frmUpdateCategory.find("input[name='id']").val();
					element.rules('add', {
						remote: {
							url: "index.php?controller=pjAdminCategories&action=pjActionCheckCategory",
							type: 'post',
							data: {id: id, locale: locale}
						},
						messages: {
					    	required: myLabel.field_required,
					    	remote: myLabel.same_category
					    }
					});
				}
			}
		}
		function _formatName(str, obj)
		{
			if(obj.parent_id != null)
			{
				return '-------' + str;
			}else{
				return str;
			}
		}
		function formatProducts(str, obj)
		{
			if(obj.cnt_products == '0')
			{
				return '0';
			}else{
				return '<a href="index.php?controller=pjAdminProducts&category_id='+obj.id+'">'+str+'</a>';
			}
		}
		function formatDown(val, obj) {
			return (obj.down === 1) ? ['<a href="index.php?controller=pjAdminCategories" class="arrow_down" rev="down" rel="', obj.id , '" title="', myLabel.down, '"></a>'].join("") : '';
		}
		function formatUp(val, obj) {
			return (obj.up === 1) ? ['<a href="index.php?controller=pjAdminCategories" class="arrow_up" rev="up" rel="', obj.id , '" title="', myLabel.up, '"></a>'].join("") : '';
		}
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminCategories&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminCategories&action=pjActionDeleteCategory&id={:id}"}
				          ],
				columns: [
				          	{text: myLabel.category_title, type: "text", sortable: true, editable: false, width: 460, renderer: _formatName},
				          	{text: myLabel.products, type: "text", sortable: false, editable: false, renderer: formatProducts, width: 80, align: 'center'},
				          	{text: "", type: "text", sortable: false, editable: false, renderer: formatDown, width: 21},
					        {text: "", type: "text", sortable: false, editable: false, renderer: formatUp, width: 21}
				         ],
				dataUrl: "index.php?controller=pjAdminCategories&action=pjActionGetCategory",
				dataType: "json",
				fields: ['name', 'cnt_products', 'down', 'up'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminCategories&action=pjActionDeleteCategoryBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				sortable: true,
				sortableUrl: "index.php?controller=pjAdminCategories&action=pjActionSortCategory",
				saveUrl: "index.php?controller=pjAdminCategories&action=pjActionSaveCategory&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminCategories&action=pjActionGetCategory", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminCategories&action=pjActionGetCategory", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminCategories&action=pjActionGetCategory", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-delete-image", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogDeleteImage.data('href', $(this).data('href')).dialog("open");
		}).on("change", 'select[name="parent_id"]', function (e) {
			
			if($(this).val() == ':NULL')
			{
				$('.mbMainInfo').show();
			}else{
				$('.mbMainInfo').hide();
			}
		}).on("click", ".arrow_up, .arrow_down", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.post("index.php?controller=pjAdminCategories&action=pjActionSetOrder", {
				"id": $(this).attr("rel"),
				"direction": $(this).attr("rev")
			}).done(function (data) {
				var content = $grid.datagrid("option", "content");
				$grid.datagrid("load", "index.php?controller=pjAdminCategories&action=pjActionGetCategory", "id", "ASC", content.page, content.rowCount);
			});
			return false;
		});
		
		if ($dialogDeleteImage.length > 0 && dialog) 
		{
			$dialogDeleteImage.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 380,
				buttons: (function () {
					var buttons = {};
					buttons[mbApp.locale.button.delete] = function () {
						$.ajax({
							type: "GET",
							dataType: "json",
							url: $dialogDeleteImage.data('href'),
							success: function (res) {
								if(res.code == 200){
									$('#image_container').remove();
									$dialogDeleteImage.dialog('close');
								}
							}
						});
					};
					buttons[mbApp.locale.button.cancel] = function () {
						$dialogDeleteImage.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
	});
})(jQuery_1_8_2);