var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateProduct = $("#frmCreateProduct"),
			$frmUpdateProduct = $("#frmUpdateProduct"),
			$dialogDelete = $("#dialogDeleteImage"),
			dialog = ($.fn.dialog !== undefined),
			multiselect = ($.fn.multiselect !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			remove_arr = new Array();
		
		
		function setSizes()
		{
			var index_arr = new Array();
				
			$('#fd_size_list').find(".fd-size-row").each(function (index, row) {
				index_arr.push($(row).attr('data-index'));
			});
			$('#index_arr').val(index_arr.join("|"));
		}
		
		if ($frmCreateProduct.length > 0 && validate) {
			$frmCreateProduct.validate({
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
				},
				submitHandler: function(form){
					var valid = true,
						localeId = null;
					if($('input[name=set_different_sizes]:checked', '#frmCreateProduct').val() == 'T')
					{
						setSizes();
						$("#frmCreateProduct .pj-positive-number").each(function() {
							if($(this).val() == '')
							{
								valid = false;
								$(this).addClass('pj-error-field');
							}else{
								if(Number($(this).val()) < 0 || $.isNumeric($(this).val()) == false)
							    {
							    	valid = false;
							    	$(this).addClass('pj-error-field');
							    }else{
							    	valid = true;
							    	$(this).removeClass('pj-error-field');
							    }
							}
						});
						$("#frmCreateProduct .fdRequired").each(function() {
							if($(this).val() == '' && $('#set_yes').is(':checked'))
							{
								valid = false;
						    	$(this).addClass('pj-error-field');
						    	if(localeId == null)
						    	{
						    		localeId = $(this).attr('lang');
						    	}
						    	
							}else{
								$(this).removeClass('pj-error-field');
							}
						});
						if(localeId != null)
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
					
					if(valid == true)
					{
						form.submit();
					}
				}
			});
		}
		if ($frmUpdateProduct.length > 0 && validate) {
			$frmUpdateProduct.validate({
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
				},
				submitHandler: function(form){
					var valid = true,
						localeId = null;
					if($('input[name=set_different_sizes]:checked', '#frmUpdateProduct').val() == 'T')
					{
						setSizes();
						$("#frmUpdateProduct .pj-positive-number").each(function() {
							if($(this).val() == '')
							{
								valid = false;
								$(this).addClass('pj-error-field');
							}else{
								if(Number($(this).val()) < 0 || $.isNumeric($(this).val()) == false)
							    {
							    	valid = false;
							    	$(this).addClass('pj-error-field');
							    }else{
							    	valid = true;
							    	$(this).removeClass('pj-error-field');
							    }
							}
						});
						$("#frmUpdateProduct .fdRequired").each(function() {
							if($(this).val() == '' && $('#set_yes').is(':checked'))
							{
								valid = false;
						    	$(this).addClass('pj-error-field');
						    	if(localeId == null)
						    	{
						    		localeId = $(this).attr('lang');
						    	}
						    	
							}else{
								$(this).removeClass('pj-error-field');
							}
						});
						if(localeId != null)
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
					
					if(valid == true)
					{
						form.submit();
					}
				}
			});
		}
		if ($frmCreateProduct.length > 0 || $frmUpdateProduct.length > 0) 
		{
			if(myLabel.locale_array.length > 0)
			{
				var locale_array = myLabel.locale_array;
				for(var i = 0; i < locale_array.length; i++)
				{
					var name = $("#i18n_name_" + locale_array[i]),
						description = $("#i18n_description_" + locale_array[i]);
					name.rules('add', {
						messages: {
					    	required: myLabel.field_required
					    }
					});
					description.rules('add', {
						messages: {
					    	required: myLabel.field_required
					    }
					});
				}
			}
		}
		if ($dialogDelete.length > 0 && dialog) 
		{
			$dialogDelete.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 400,
				buttons: (function () {
					var buttons = {};
					buttons[mbApp.locale.button.delete] = function () {
						$.ajax({
							type: "GET",
							dataType: "json",
							url: $dialogDelete.data('href'),
							success: function (res) {
								if(res.code == 200){
									$('#image_container').remove();
									$dialogDelete.dialog('close');
								}
							}
						});
					};
					buttons[mbApp.locale.button.cancel] = function () {
						$dialogDelete.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		function formatImage(val, obj) {
			var src = val ? val : 'app/web/img/backend/no_image.png';
			return ['<a href="index.php?controller=pjAdminProducts&action=pjActionUpdate&id=', obj.id ,'"><img src="', src, '" style="width: 84px" /></a>'].join("");
		}
		function formatDown(val, obj) {
			return (obj.down === 1) ? ['<a href="index.php?controller=pjAdminProducts" class="arrow_down" rev="down" rel="', obj.id , '" title="', myLabel.down, '"></a>'].join("") : '';
		}
		function formatUp(val, obj) {
			return (obj.up === 1) ? ['<a href="index.php?controller=pjAdminProducts" class="arrow_up" rev="up" rel="', obj.id , '" title="', myLabel.up, '"></a>'].join("") : '';
		}
		if ($("#grid").length > 0 && datagrid) {
			var gridOpts = {
					buttons: [{type: "edit", url: "index.php?controller=pjAdminProducts&action=pjActionUpdate&id={:id}"},
					          {type: "delete", url: "index.php?controller=pjAdminProducts&action=pjActionDeleteProduct&id={:id}"}
					          ],
					columns: [{text: myLabel.image, type: "text", sortable: false, editable: false, renderer: formatImage, width: 90, align: "center"},
					          {text: myLabel.name, type: "text", sortable: true, editable: true, width: 160, editableWidth: 150},
					          {text: myLabel.category, type: "text", sortable: true, editable: false, width: 120},
					          {text: myLabel.price, type: "text", sortable: false, editable: false, width: 120},
					          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, options: [
					  				                                                                                     {label: myLabel.active, value: "T"}, 
					  				                                                                                     {label: myLabel.inactive, value: "F"}
					  				                                                                                     ], applyClass: "pj-status"}
					          ],
					dataUrl: "index.php?controller=pjAdminProducts&action=pjActionGetProduct" + pjGrid.queryString,
					dataType: "json",
					fields: ['image', 'name', 'categories', 'price', 'status'],
					paginator: {
						actions: [
						   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminProducts&action=pjActionDeleteProductBulk", render: true, confirmation: myLabel.delete_confirmation}
						],
						gotoPage: true,
						paginate: true,
						total: true,
						rowCount: true
					},
					saveUrl: "index.php?controller=pjAdminProducts&action=pjActionSaveProduct&id={:id}",
					select: {
						field: "id",
						name: "record[]"
					}
				};
			if(pjGrid.queryString != '')
			{
				gridOpts.columns = [{text: myLabel.image, type: "text", sortable: false, editable: false, renderer: formatImage, width: 90, align: "center"},
							          {text: myLabel.name, type: "text", sortable: true, editable: true, width: 210, editableWidth: 200},
							          {text: myLabel.price, type: "text", sortable: false, editable: false, width: 120},
							          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, options: [
								  				                                                                                     {label: myLabel.active, value: "T"}, 
								  				                                                                                     {label: myLabel.inactive, value: "F"}
								  				                                                                                     ], applyClass: "pj-status"},
							          {text: "", type: "text", sortable: false, editable: false, renderer: formatDown, width: 21},
								      {text: "", type: "text", sortable: false, editable: false, renderer: formatUp, width: 21}
							          ];
				gridOpts.fields = ['image', 'name', 'price', 'status', 'data', 'data']
			}
			var $grid = $("#grid").datagrid(gridOpts);
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
			$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct", "name", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct", "name", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-delete-image", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogDelete.data('href', $(this).data('href')).dialog("open");
		}).on("click", '.pj-add-size', function(e){
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var clone_text = $('#fd_size_clone').html(),
				index = Math.ceil(Math.random() * 999999),
				number_of_sizes = $('#fd_size_list').find(".fd-size-row").length,
				order = parseInt(number_of_sizes, 10) + 1;
			clone_text = clone_text.replace(/\{INDEX\}/g, 'fd_' + index);
			clone_text = clone_text.replace(/\{ORDER\}/g, order);
			$('#fd_size_list').append(clone_text);
		}).on("click", '.pj-remove-size', function(e){
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $size = $(this).parent().parent(),
				id = $size.attr('data-index');
			if(id.indexOf("fd") == -1)
			{
				remove_arr.push(id);
			}
			$('#remove_arr').val(remove_arr.join("|"));
			$size.remove();
			
			$('#fd_size_list').find(".fd-size-row").each(function (order, row) {
				var index = $(row).attr('data-index'),
					title = myLabel.size + " " + (order + 1) + ":";
				$('.fd-title-' + index).html(title);
			});
		}).on("click", '#set_yes', function(e){
			$('#multiple_prices').css('display', 'block');
			$('#signle_price').css('display', 'none');
		}).on("click", '#set_no', function(e){
			$('#multiple_prices').css('display', 'none');
			$('#signle_price').css('display', 'block');
		}).on("keyup", '.pj-positive-number', function(e){
			if($(this).val() == '')
			{
				$(this).removeClass('pj-error-field');
			}else{
				if(Number($(this).val()) < 0 || $.isNumeric($(this).val()) == false)
			    {
			    	$(this).addClass('pj-error-field');
			    }else{
			    	$(this).removeClass('pj-error-field');
			    }
			}
			
		}).on("keyup", '.fdRequired', function(e){
			if($(this).val() == '')
			{
				$(this).addClass('pj-error-field');
			}else{
				$(this).removeClass('pj-error-field');
			}
			
		}).on("change", '#filter_category_id', function(e){
			if($(this).val() == '')
			{
				window.location.href = "index.php?controller=pjAdminProducts&action=pjActionIndex";
			}else{
				window.location.href = "index.php?controller=pjAdminProducts&action=pjActionIndex&category_id=" + $(this).val();
			}
			
		}).on("click", ".arrow_up, .arrow_down", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.post("index.php?controller=pjAdminProducts&action=pjActionSetOrder", {
				"id": $(this).attr("rel"),
				"direction": $(this).attr("rev")
			}).done(function (data) {
				var content = $grid.datagrid("option", "content");
				$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct" + pjGrid.queryString, "name", "ASC", content.page, content.rowCount);
			});
			return false;
		});
	});
})(jQuery_1_8_2);