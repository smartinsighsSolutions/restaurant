/*!
 * Restaurant Menu Maker
 * https://www.phpjabbers.com/restaurant-menu-maker/
 * 
 * Copyright 2014, StivaSoft Ltd.
 * 
 */
(function (window, undefined){
	"use strict";
	
	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	
	var document = window.document,
		validate = (pjQ.$.fn.validate !== undefined),
		datepicker = (pjQ.$.fn.datepicker !== undefined),
		dialog = (pjQ.$.fn.dialog !== undefined),
		routes = [
		          	{pattern: /^#!\/Menu$/, eventName: "loadMenu"},
		          	{pattern: /^#!\/SpecialOffers$/, eventName: "loadOffers"},
		          	{pattern: /^#!\/OfferDetails\/offer_id:(\d+)?$/, eventName: "loadOfferDetails"},
		          	{pattern: /^#!\/Products\/cid:(\d+)?$/, eventName: "loadProducts"},
		          ];
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function hashBang(value) {
		if (value !== undefined && value.match(/^#!\//) !== null) {
			if (window.location.hash == value) {
				return false;
			}
			window.location.hash = value;
			return true;
		}
		
		return false;
	}
	
	function onHashChange() {
		var i, iCnt, m;
		for (i = 0, iCnt = routes.length; i < iCnt; i++) {
			m = window.location.hash.match(routes[i].pattern);
			if (m !== null) {
				pjQ.$(window).trigger(routes[i].eventName, m.slice(1));
				break;
			}
		}
		if (m === null) {
			pjQ.$(window).trigger("loadEvents");
		}
	}
	pjQ.$(window).on("hashchange", function (e) {
    	onHashChange.call(null);
    });
	
	function MenuBuilder(opts) {
		if (!(this instanceof MenuBuilder)) {
			return new MenuBuilder(opts);
		}
				
		this.reset.call(this);
		this.init.call(this, opts);
		
		return this;
	}
	
	MenuBuilder.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};
	
	MenuBuilder.size = function(obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};
	
	MenuBuilder.prototype = {
		reset: function () {
			this.$container = null;
			this.container = null;
			this.opts = {};
			
			this.cid = null;
			this.offer_id = null;
			return this;
		},
		disableButtons: function () {
			var $el;
			this.$container.find(".pjMbSelectorButton").each(function (i, el) {
				$el = pjQ.$(el).attr("disabled", "disabled");
			});
		},
		enableButtons: function () {
			this.$container.find(".pjMbSelectorButton").removeAttr("disabled");
		},
		
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.container = document.getElementById("mbContainer_" + this.opts.index);
			this.$container = pjQ.$(this.container);
			
			this.$container.on("click.mb", ".pjMbSelectorLocale", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var locale = pjQ.$(this).data("id");
				self.opts.locale = locale;
				
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionLocale"].join(""), {
					"locale_id": locale
				}).done(function (data) {					
					if(!hashBang("#!/Menu"))
					{
						self.loadMenu.call(self);
					}
				}).fail(function () {
					log("Deferred is rejected");
				});
				return false;
			}).on("click.mb", ".pjMbMenu", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Menu");
				return false;
			}).on("click.mb", ".pjMbSpecialOffers", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/SpecialOffers");
				return false;
			}).on("click.mb", ".pjMbCategoryItem", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Products/cid:" + pjQ.$(this).attr('data-id'));
				return false;
			}).on("click.mb", ".pjMbSpecialOffer", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/OfferDetails/offer_id:" + pjQ.$(this).attr('data-id'));
				return false;
			});
			
			pjQ.$(window).on("loadMenu", this.container, function (e) {
				self.loadMenu.call(self);
			}).on("loadOffers", this.container, function (e) {
				self.loadOffers.call(self);
			}).on("loadProducts", this.container, function (e) {
				switch (arguments.length) {
					case 1:
						break;
					case 2:
						self.cid = arguments[1];
					break;
				}
				self.loadProducts.call(self);
			}).on("loadOfferDetails", this.container, function (e) {
				switch (arguments.length) {
					case 1:
						break;
					case 2:
						self.offer_id = arguments[1];
					break;
				}
				self.loadOfferDetails.call(self);
			});
			
			if (window.location.hash.length === 0) {
				this.loadMenu.call(this);
			} else {
				onHashChange.call(null);
			}
		},
		
		loadMenu: function () {
			var self = this,
				index = this.opts.index,
				params = 	{
								"theme": this.opts.theme,
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index
							};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionMenu"].join(""), params).done(function (data) {
				self.$container.html(data);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		loadOffers: function () {
			var self = this,
				index = this.opts.index,
				params = 	{
								"theme": this.opts.theme,
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index
							};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionOffers"].join(""), params).done(function (data) {
				self.$container.html(data);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		loadProducts: function () {
			var self = this,
				index = this.opts.index,
				params = 	{
								"theme": this.opts.theme,
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index
							};
			if (self.cid !== null) {
				params = pjQ.$.extend(params, {
					"cid": self.cid
				});
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionProducts"].join(""), params).done(function (data) {
				self.$container.html(data);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		loadOfferDetails: function () {
			var self = this,
				index = this.opts.index,
				params = 	{
								"theme": this.opts.theme,
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index
							};
			if (self.offer_id !== null) {
				params = pjQ.$.extend(params, {
					"offer_id": self.offer_id
				});
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionOfferDetails"].join(""), params).done(function (data) {
				self.$container.html(data);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		}
	};
	
	window.MenuBuilder = MenuBuilder;	
})(window);