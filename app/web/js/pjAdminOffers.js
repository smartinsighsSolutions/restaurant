var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateOffer = $("#frmCreateOffer"),
			$frmUpdateOffer = $("#frmUpdateOffer"),
			$dialogDeleteImage = $("#dialogDeleteImage"),
			chosen = ($.fn.chosen !== undefined),
			dialog = ($.fn.dialog !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		$(".field-int").spinner({
			min: 0
		});
		if (chosen) {
			$('#pjMbProductBox').find(".pjProductList").chosen();
		}
		if ($frmCreateOffer.length > 0 && validate) {
			
			$.validator.addMethod('positive',
			    function (value) { 
			        return Number(value) > 0;
			    });
			
			
			$frmCreateOffer.validate({
				errorPlacement: function (error, element) {
					var name = element.attr('name');
					if(name.indexOf("product_id") >= 0)
					{
						var index = element.attr('data-index');
						$('#product_id_'+index+'_chzn').find('.chzn-single').addClass('pjBorderError');
					}else if(name.indexOf("size_id") >= 0){
						element.addClass('pjBorderError');
					}else{
						error.insertAfter(element.parent());
					}
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
							url: "index.php?controller=pjAdminOffers&action=pjActionCheckOffer",
							type: 'post',
							data: {locale: locale}
						},
						messages: {
					    	required: myLabel.field_required,
					    	remote: myLabel.same_offer
					    }
					});
				}
			}
		}
		if ($frmUpdateOffer.length > 0 && validate) {
			$.validator.addMethod('positive',
			    function (value) { 
			        return Number(value) > 0;
			    });
			$frmUpdateOffer.validate({
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
						id = $frmUpdateOffer.find("input[name='id']").val();
					element.rules('add', {
						remote: {
							url: "index.php?controller=pjAdminOffers&action=pjActionCheckOffer",
							type: 'post',
							data: {id: id, locale: locale}
						},
						messages: {
					    	required: myLabel.field_required,
					    	remote: myLabel.same_offer
					    }
					});
				}
			}
		}
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminOffers&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminOffers&action=pjActionDeleteOffer&id={:id}"}
				          ],
				columns: [
				          	{text: myLabel.title, type: "text", sortable: true, editable: false, width: 250},
				          	{text: myLabel.people, type: "text", sortable: true, editable: false, width: 70, align: "center"},
				          	{text: myLabel.price, type: "text", sortable: true, editable: false, width: 80},
				          	{text: myLabel.products, type: "text", sortable: true, editable: false, width: 80, align: "center"},
				          	{text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, options: [
					  				                                                                                     {label: myLabel.active, value: "T"}, 
					  				                                                                                     {label: myLabel.inactive, value: "F"}
					  				                                                                                     ], applyClass: "pj-status"}
				         ],
				dataUrl: "index.php?controller=pjAdminOffers&action=pjActionGetOffer",
				dataType: "json",
				fields: ['name', 'people', 'price', 'cnt_products', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminOffers&action=pjActionDeleteOfferBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				sortable: true,
				sortableUrl: "index.php?controller=pjAdminOffers&action=pjActionSortOffer",
				saveUrl: "index.php?controller=pjAdminOffers&action=pjActionSaveOffer&id={:id}",
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
			$grid.datagrid("load", "index.php?controller=pjAdminOffers&action=pjActionGetOffer", "name", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminOffers&action=pjActionGetOffer", "name", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminOffers&action=pjActionGetOffer", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-delete-image", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogDeleteImage.data('href', $(this).data('href')).dialog("open");
		}).on("click", '.pj-add-product', function(e){
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var clone_text = $('#pjMbProductClone').html(),
				index = Math.ceil(Math.random() * 999999);
			clone_text = clone_text.replace(/\{INDEX\}/g, 'fd_' + index);
			$('#pjMbProductBox').append(clone_text);
			if (chosen) {
				$('#pjMbProductBox').find(".pjProductList").chosen();
			}
		}).on("click", '.pj-remove-size', function(e){
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).parent().parent().remove();
		}).on("change", ".pjProductList", function (e) {
			var value = $(this).val(),
				index = $(this).attr('data-index'),
				option = $('option:selected', this).attr('data-size');
			if(value != '')
			{
				if(option == 'T')
				{
					$('.pj-loader').show();
					$.ajax({
						type: "GET",
						dataType: "html",
						url: "index.php?controller=pjAdminOffers&action=pjActionGetSizes&product_id=" + value + "&index=" + index,
						success: function (res) {
							$('#pjMbProductSize_' + index).html(res).show();
							$('.pj-loader').hide();
						}
					});
				}else{
					$('#pjMbProductSize_' + index).html("").hide();
				}
				$('#product_id_'+index+'_chzn').find('.chzn-single').removeClass('pjBorderError');
			}
		}).on("change", ".pjMbSize", function (e) {
			var value = $(this).val();
			if(value != '')
			{
				$(this).removeClass('pjBorderError');
			}
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