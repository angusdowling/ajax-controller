/**
 * ajax
 * @module
 * @requires jquery.min.js
 * @requires underscore.min.js
 * @author Angus Dowling <angusdowling@live.com.au>
 */

(function (exports, $) {
	"use strict";

	/**
	 * Settings.
	 * @type {object}
	 */
	AjaxController.prototype.settings = null;

	/**
	 * Default settings.
	 * @type {object}
	 */
	AjaxController.prototype.defaults = null;
	
	/**
	 * Response data from server.
	 * @type {object}
	 */
	AjaxController.prototype.response = null;
	
	/**
	 * Container element selector.
	 * @type {object}
	 */
	AjaxController.prototype.container = null;
	
	/**
	 * data to be sent to server.
	 * @type {object}
	 */
	AjaxController.prototype.data = null;

	/**
	 * Current breakpoint.
	 * @type {number}
	 */
	AjaxController.prototype.cs = null;

	/**
	 * AjaxController Constructor.
	 * @param {object} options 
	 */
	function AjaxController(container, options){
		if(!$(container)[0]) return;

		this.container = container;
		this.settings = [];
		
		this.defaults = {
			container      : container + ' [data-ajax]',
			form           : container + ' [data-ajax] [data-ajax-form]',
			pagination     : container + ' [data-ajax] [data-ajax-pagination]',
			pagers         : container + ' [data-ajax] [data-ajax-pagination] [data-paged]',
			filter         : container + ' [data-ajax] [data-ajax-filter]',
			posts          : container + ' [data-ajax] [data-ajax-posts]',
			reset          : container + ' [data-ajax] [data-ajax-reset]',
			clear          : container + ' [data-ajax] [data-ajax-clear]',
			
			onSuccess      : function() { },
			onError        : function() { },
			onComplete     : function() { },
			onChange       : function() { }, 
			onSubmit       : function() { }, 
			onPaginate     : function() { },
			onReset        : function() { },
			onClear        : function() { },
			
			submitOnChange : true,
			append         : false,
			breakpoint     : 0,

			loadingClassContainer  : 'ajax-loading-container',
			loadingClassBody       : 'ajax-loading-body',

			fragment : {
				form : container + ' [data-ajax]'
			}
		}

		for(var i = 0; i < options.length; i++){
			var option = $.extend({
				breakpoint: options[i].breakpoint
			}, options[i].settings);

			this.settings.push($.extend( _.clone(this.defaults), option ));
		}
		
		this.setSettings();
		this.construct();
		this.query.fragment = this.settings[this.cs].fragment;

		$(document).trigger('ajaxcontroller:load');
	}

	/**
	 * Default query. If any data is not provided by the form, it will use this data.
	 */
	AjaxController.prototype.query = {
		action        : wp_ajax_data.data.action,
		_ajax_nonce   : wp_ajax_data.data.nonce,
		url           : wp_ajax_data.ajaxurl,
		post          : wp_ajax_data.data.post,
		options       : wp_ajax_data.data.options,
		ignored       : wp_ajax_data.data.ignored,
		appendContent : false,
		paged         : 1
	}

	/**
	 * Initialise ajax controller.
	 */
	AjaxController.prototype.construct = function()
	{
		this.setFormDefaults();
		this.onWindowResize();
	};

	/**
	 * Set current settings to use. Refresh handlers.
	 */
	AjaxController.prototype.setSettings = function()
	{
		var windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
		var previousBreakpoint = null;
		var closest = null;

		for(var i = 0; i < this.settings.length; i++){
			var setting = this.settings[i];

			if(windowWidth > setting.breakpoint){
				if(previousBreakpoint == null || setting.breakpoint > previousBreakpoint){
					previousBreakpoint = setting.breakpoint;
					closest = i;
				}
			}
		}

		if(this.cs !== closest){
			this.cs = closest;
			this.detachEvents();
			this.attachEvents();
		}
	};

	/**
	 * Attach events.
	 */
	AjaxController.prototype.attachEvents = function()
	{
		this.onChange();
		this.onSubmit();
		this.onPaginate();
		this.onReset();
		this.onClear();
	};

	/**
	 * Detach events.
	 */
	AjaxController.prototype.detachEvents = function()
	{
		$(document).off('.controller');
		$(this.settings[this.cs].form).off('.controller');
	}

	/**
	 * On window resize.
	 */
	AjaxController.prototype.onWindowResize = function()
	{
		var _this = this;

		$(window).on('resize', function(){
			_this.setSettings();
		});
	}
	
	/**
	 * Attach update event handler.
	 */
	AjaxController.prototype.onChange = function()
	{
		var _this = this;

		if(!this.settings[this.cs].submitOnChange){
			return false;
		}
		
		$(document).on('change.controller', _this.settings[this.cs].form, function(e){
			_this.query.paged = 1;
			_this.initState();
			_this.request('change');
		});
	}

	/**
	 * Attach update event handler.
	 */
	AjaxController.prototype.onSubmit = function()
	{
		var _this = this;
		
		$(document).on('submit.controller', _this.settings[this.cs].form, function(e){
			_this.query.paged = 1;
			_this.initState();
			_this.request('submit');
			e.preventDefault();
		});
	}

	/**
	 * Attach pagination event handler.
	 */
	AjaxController.prototype.onPaginate = function()
	{
		var _this = this;

		$(document).on('click.controller', _this.settings[this.cs].pagers, function(e){
			_this.preAppend();
			_this.setPaged(this);
			_this.initState();
			_this.request('paginate');
			e.preventDefault();
		});
	}

	/**
	 * Attach reset event handler.
	 */
	AjaxController.prototype.onReset = function()
	{
		var _this = this;

		$(document).on('click.controller', _this.settings[this.cs].reset, function(e){
			_this.clearForm();
			_this.applyFormDefaults();
			_this.query.paged = 1;
			_this.initState();
			_this.request('reset');

			e.preventDefault();
		});
	}

	/**
	 * Attach clear event handler.
	 */
	AjaxController.prototype.onClear = function()
	{
		var _this = this; 

		$(document).on('click.controller', _this.settings[this.cs].clear, function(e){
			_this.clearForm();
			_this.query.paged = 1;
			_this.initState();
			_this.request('clear');

			e.preventDefault();
		});
	}

	/**
	 * pre append.
	 */
	AjaxController.prototype.preAppend = function()
	{
		if(this.settings[this.cs].append){
			this.query.appendContent = true;
		}
	}

	/**
	 * pre append.
	 */
	AjaxController.prototype.postAppend = function()
	{
		this.query.appendContent = false;
	}

	/**
	 * Set paged value.
	 */
	AjaxController.prototype.setPaged = function(pager)
	{
		var paged = $(pager).attr('data-paged');
		
		switch(paged){
			case 'previous':
				this.query.paged = this.query.paged - 1;
				break;
				
			case 'next':
				this.query.paged = this.query.paged + 1;
				break;
				
			default:
				this.query.paged = parseInt(paged, 10);
				break;
		}
	}

	/**
	 * Set default form data in case of reset.
	 */
	AjaxController.prototype.setFormDefaults = function()
	{
		this.settings[this.cs].formDefaults = $(this.settings[this.cs].form).serializeObject();
	}

	/**
	 * Apply form default values.
	 */
	AjaxController.prototype.applyFormDefaults = function()
	{
		for(var key in this.settings[this.cs].formDefaults){
			var field = $('[name="'+key+'"]');
			var value = this.settings[this.cs].formDefaults[key];

			if(field.is(':checkbox')){
				for(var i = 0; i < value.length; i++){
					field.filter("[value='"+value[i]+"']").prop('checked', true);
				}
			}

			else {
				field.val(value);
			}
		}
	}

	/**
	 * Clear form values.
	 */
	AjaxController.prototype.clearForm = function()
	{
		var fields = $(this.settings[this.cs].form).find(':input').not(':button, :submit, :reset, [type="hidden"]');

		for(var i = 0; i < fields.length; i++){
			var field = fields.eq(i);

			if(field.is(':checkbox')){
				field.removeAttr('checked');
			}

			else {
				field.val('');
			}
		}
	}

	/**
	 * Initialize AJAX state in the DOM.
	 */
	AjaxController.prototype.initState = function()
	{
		$('html').addClass(this.settings[this.cs].loadingClassBody);
		$(this.container).addClass(this.settings[this.cs].loadingClassContainer);
	}

	/**
	 * Remove AJAX state from the DOM.
	 */
	AjaxController.prototype.removeState = function()
	{
		$('html').removeClass(this.settings[this.cs].loadingClassBody);
		$(this.container).removeClass(this.settings[this.cs].loadingClassContainer);
	}

	/**
	 * Serialize form data.
	 * @return Serialized Object
	 */
	AjaxController.prototype.serialize = function()
	{
		var query = _.clone(this.query);

		if($(this.settings[this.cs].form)[0]){
			return $.extend($(this.settings[this.cs].form).serializeObject(), query);
		}

		return query;
	}

	/**
	 * Handle callbacks.
	 */
	AjaxController.prototype.handleCallback = function(callback)
	{
		switch(callback){
			case 'change':
				this.settings[this.cs].onChange.call(this, this.data);
			break;
			case 'submit':
				this.settings[this.cs].onSubmit.call(this, this.data);
			break;
			case 'paginate':
				this.settings[this.cs].onPaginate.call(this, this.data);
			break;
			case 'reset':
				this.settings[this.cs].onReset.call(this, this.data);
			break;
			case 'clear':
				this.settings[this.cs].onClear.call(this, this.data);
			break;
		}
	}

	/**  
	 * Send query request to the server.
	 */
	AjaxController.prototype.request = function(callback)
	{
		var _this = this;
		this.data = this.serialize();
		this.handleCallback(callback);

		$.ajax({
			type : "POST",
			url  : _this.query.url,
			data : _this.data,
			success: function(response, textStatus, jqXHR) {
				_this.response = response; 
				_this.requestSuccess(); 
			},

			error: function(jqXHR, textStatus, error){
				_this.response = error;
				_this.requestError();
			},

			complete: function(jqXHR, textStatus){
				_this.requestComplete();
			}
		});
	};

	/** 
	 * AJAX request success.
	 */
	AjaxController.prototype.requestSuccess = function()
	{
		this.settings[this.cs].onSuccess.call(this);
		this.appendFragment();
		this.postAppend();
	};

	/** 
	 * AJAX request error.
	 */
	AjaxController.prototype.requestError = function()
	{
		this.settings[this.cs].onError.call(this);
		console.error(this.response);
	};

	/** 
	 * AJAX request complete.
	 */
	AjaxController.prototype.requestComplete = function()
	{
		this.settings[this.cs].onComplete.call(this);
		this.removeState();
	};

	/** 
	 * serve HTML fragment onto the browser.
	 */
	AjaxController.prototype.appendFragment = function()
	{
		if(typeof this.response.data !== "undefined"){
			for(var key in this.response.data.fragments){
				var $target = $(key);
				var data    = this.response.data.fragments[key];
	
				if($target[0]){
					if(this.query.appendContent && typeof $target.data('ajax-posts') !== "undefined"){
						$target.append(data);
					}

					else {
						$target.replaceWith(data);
					}
				}
			}
		}
	}

	/**
	 * Export plugin to window object as AjaxController
	 */
	exports.AjaxController = AjaxController;

	/**
	 * Serialize Object helper function
	 */
	$.fn.serializeObject = function()
	{
		var o = {};
		var a = this.serializeArray();

		$.each(a, function() {
			if (o[this.name] !== undefined) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});

		return o;
	};

	$(window).load(function(){
		$(document).trigger('ajaxcontroller:ready');
	});
})(window, jQuery);