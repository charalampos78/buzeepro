if(typeof console === "undefined") {
	console = { log: function() { } };
}

var micro = {};

micro.controllers = {};

micro.core = {
	init : function() {
		//
		micro.utils.flash.init();
		micro.helper.init();

		//jQuery ajax hook to check login status
		$(document).ajaxComplete(function(event, xhr, settings) {
			var type = null;
			if ( xhr.responseJSON && 'error' in xhr.responseJSON) {
				type = xhr.responseJSON.error.type;
				type = type.substr(type.lastIndexOf("\\") + 1);

				switch (type) {
					case "AuthenticationException" : //401
						//@TODO probably should show some type of ajax login window
						window.location.reload();
						break;
					default : // status >= 405
						if (!('flash_msg' in xhr.responseJSON) && xhr.status > 400) {
							micro.utils.flash.show(xhr.responseJSON.error.message);
						} else if (xhr.status > 405) {
							micro.utils.flash.show(xhr.responseJSON.error.message);
						}
						break;
				}
			}

		});

	},
	initialized : null,
	controller : {
		init : function() {
			this.initialized = micro.core.route(this);
			return this.initialized;
		},
		initialized : null
	},
	createController : function(name) {
		var newController = Object.create(this.controller);
		micro.controllers[name] = newController;
		return newController;
	},
	route : function(controller) {
		var initialized = false;
		var routes = controller.routes();

		var path = window.location.pathname;
		$.each(routes,  function(route, method) {
			if (path.search(route) >= 0) {
				if (method.call(controller) !== false) {
					initialized = true;
				}
				//once route matched, stop checking others
				return false;
			}
		});

		this.initialized = this.initialized || initialized;

		return initialized;

	}


};


micro.utils = {
	form : {
		/**
		 * Provides ajax form submission
		 * @param {Object}  options Options for submission
		 * @param {string}  options.selector jQuery object selector of form being submitted
		 * @param {string}  [options.url] URL form is to be submitted to, can be a function that returns a string
		 * @param {string}  [options.type] Form submission method type
		 * @param {string}  [options.target] jQuery selector to replace html in for html type calls
		 * @param {string}  [options.dataType] jQuery ajax dataType option,
		 * @param {Object}  [options.extraData] jqForm "data" option for additional data to be submitted with form
		 * @param {Function}  [options.before] jqValidate before submit handler
		 * @param {Function}  [options.beforeSubmit] jqForm ajaxSubmit.beforeSubmit hook
		 * @param {Function}  [options.beforeSend] jQuery ajax.beforeSend callback
		 * @param {Function}  [options.complete] jQuery ajax.complete callback
		 * @param {Function}  [options.success] jQuery ajax.success callback
		 * @param {Function}  [options.error] jQuery ajax.error callback
		 */
		ajax: function (options) {

			var validationForms = [];
			$(options.selector).not(".jq-validated-form").each(function() {

				var $target_form = $(this);
				var submitData;

				//defaults
				submitData = $.extend({
					onkeyup : undefined, //must be undefined or will overwrite default onkeyup function
					url : null, //url default set on ajaxSubmit assign in case form attr action is dynamic
					type : ( $target_form.attr('method') ) ? $target_form.attr('method') : "POST",
					target : null, //option from jqForm ajaxSubmit
					extraData : null, //option from jqForm ajaxSubmit
					dataType: 'json',
					before : function() {}, //before the jqForm submit handler
					beforeSubmit : function() {}, //jqForm ajaxSubmit hook
					beforeSend : function() {}, //standard jq Ajax hook
					complete : function() {},
					success : function() {},
					error : function() {}
				}, options);
				console.log(submitData);

				//this class is to prevent re-initialization.
				$target_form.addClass("jq-validated-form");
				var validate = $target_form.validate({
					onkeyup: submitData.onkeyup,
					errorElement: "span",
					errorClass: "has-error",
					focusCleanup: false,
					highlight: function( element, errorClass, validClass ) {
						if ( element.type === "radio" ) {
							this.findByName( element.name ).addClass( errorClass ).removeClass( validClass );
						} else {
							$( element ).addClass( errorClass ).removeClass( validClass );
						}
						//bootstrap
						$(element).closest('.form-group, .form-group-lg, .form-group-sm').addClass('has-error');
					},
					unhighlight: function( element, errorClass, validClass ) {
						if ( element.type === "radio" ) {
							this.findByName( element.name ).removeClass( errorClass ).addClass( validClass );
						} else {
							$( element ).removeClass( errorClass ).addClass( validClass );
						}
						//bootstrap
						$(element).closest('.form-group, .form-group-lg, .form-group-sm').removeClass('has-error');
					},
					clearErrors: function(form) {
						//validate doesn't remove old errors of dynamic elements
						var elements = $(":input.has-error", form);
						for ( var i = 0; elements[ i ]; i++ ) {
							this.settings.unhighlight.call( this, elements[ i ], this.settings.errorClass, this.settings.validClass );
						}
					},
					//onfocusout:false, //causes issues with 1.11.0 validation plugin
					submitHandler: function(form) {
						//before hook
						if (submitData.before(form, submitData) === false ) return false;
						micro.utils.form.disableSubmit(form);
						this.settings.clearErrors.call(this, form);
						$(form).ajaxSubmit({
							url: (submitData.url) ? ( typeof(submitData.url)=="function"? submitData.url() : submitData.url) : $target_form.attr('action'),
							type:submitData.type,
							dataType:submitData.dataType,
							target: submitData.target,
							data: submitData.extraData,
							beforeSubmit: function (jqXHR, settings){
								//this is before the ajax call, ajaxSubmit specific
								//beforeSubmit hook
								submitData.beforeSubmit(jqXHR, settings, form, submitData);
							},
							beforeSend: function (jqXHR, settings){
								//beforeSend hook
								submitData.beforeSend(jqXHR, settings, form, submitData);
							},
							complete: function(jqXHR, textStatus) {
								//data = JSON.parse(jqXHR.responseText);
								console.log('Ajax Form Submission Response:',jqXHR);
								//complete hook
								submitData.complete(jqXHR, textStatus, form, submitData);
							},
							success: function(data, textStatus, jqXHR) {
								if (submitData.dataType == 'json' && !('uri' in data)) {
									//self
									data.uri = window.location.pathname;
								}
								//success hook
								submitData.success(data, textStatus, jqXHR, form, submitData);
							},
							error: function(jqXHR, textStatus, errorThrown) {
								micro.utils.form.enableSubmit(form);

								//console.log('Ajax Form Response Data:',data);
								if (submitData.dataType == 'json') {
									var data;
									try {
										data = JSON.parse(jqXHR.responseText);
									} catch (err) {
										data = {};
									}
									micro.utils.form.processAjaxErrors(form, validate, data);
								}
								//validate.resetForm(); //also clears values - not wanted
								//error hook
								submitData.error(jqXHR, textStatus, errorThrown, form, submitData);

							}
						});
					}
				});
				validationForms.push(validate);
			});
			return validationForms;
		},
		disableSubmit: function(form) {
			$(form).find("input[type='submit'], button").attr('disabled', true);
		},
		enableSubmit: function(form) {
			$(form).find("input[type='submit'], button").attr('disabled', false);
		},
		processAjaxErrors: function(form, validate, response) {
			var loop_errors = function(errors, extra) {
				$.each(errors, function(key, val) {
					if (key == 'msg') return true; //continue;

					if (typeof extra != "undefined" && key != '_external') {
						key = extra + '[' + key + ']';
					}
					if (typeof extra != "undefined" && key == '_external') {
						key = extra;
					}
					if (typeof val == "object" && !(val instanceof Array)) {
						loop_errors(val, key);
					} else {
						fieldMsg = {};
						fieldMsg[key] = val;
						try {
							validate.showErrors(fieldMsg);
						} catch(err) {
							//ignore array keys that don't exist in form
							console.log('Ajax Form Non-existant Key:',err, fieldMsg);
						}
					}
					return ""; //unnecessary
				});
			};

			var type = null, data_errors = null, data_uri = null, message = "";
			if ("error" in response) {
				if ("type" in response.error) {
					type = response.error.type;
					type = type.substr(type.lastIndexOf("\\")+1);
				}
				if ("data" in response.error) {
					if ("errors" in response.error.data) {
						data_errors = response.error.data.errors;
					}
					if ("uri" in response.error.data) {
						data_uri = response.error.data.uri;
					}
				}
			}
			switch (type) {
				case "ValidationException" :
					if (data_errors) {
						loop_errors(data_errors);
					}
					break;
			}

			micro.utils.form.enableSubmit(form);

			//scroll to first error
			var errorOffset = $('input.error, textarea.error, select.error').eq(0).offset();
			errorOffset = (errorOffset) ? (errorOffset.top - 50) : 0;
			if (errorOffset) {
				$('html, body').animate({
					scrollTop: errorOffset
				}, 1000);
			}

		}
	},
	flash: {
		timeout: null,
		initialized: false,
		types: ['info', 'success', 'notice', 'warning', 'error'],
		menuSelector: ".navbar",
		init: function() {
			var $flash = $("#flash-notifications");
			var that = this;
			$flash.on('click', '.flash-block', function(e) {
				//can't remove immediately in case there's a event attached to something within the alert
				$(this).stop(true).slideUp({
					duration: 1000,
					complete: $.proxy(function() { $(this).remove(); }, this),
					step: function() { that.position(); }
				});

			});

			$(window).on('scroll', function() {
				if ($('#flash-notifications').find('.flash-block').length) {
					micro.utils.flash.position();
				}
			});
			$(window).on('resize', function() {
					micro.utils.flash.position();
			});


			//jQuery ajax hook to display any flash messages sent back in any ajax call.
			$(document).ajaxComplete(function(event, xhr, settings) {
				if ( xhr.responseJSON && 'flash_msg' in xhr.responseJSON) {
					$.each(xhr.responseJSON.flash_msg, function(key, val) {
						if (val instanceof Array) {
							$.each(val, function(index, msg) {
								that.show(val, {'type':key});
							});
						} else {
							that.show(val);
						}
					})
				}
			});

			this.initialized = true;

			//show initial messages
			this.show();

		},
		position: function() {
			//this adjusts the page margin height to match height of flash message.
			//allows flash message to scroll with page
			var $flash = $("#flash-notifications");
			var $body = $("body");
			var $menu = $(micro.utils.flash.menuSelector); //used for scrolling top menu
			if ($(this).scrollTop() < $flash.height() || $flash.height() == 0) {
				$flash.css("position","relative");
				$body.css("margin-top","0");
			} else {
				$flash.css("position","fixed");
				$body.css("margin-top",$flash.height());
			}

			if ($menu.css('position') == "fixed") {
				//$menu.css('top', $flash.height()); //flash messages above the menu
				$flash.css('top', $menu.height()); //flash messages below the menu
				$('body').css('height', $(window).height() - $flash.height());
			} else {
				$menu.css('top',"");
				$flash.css('top',"");
			}

		},
		show: function(msg, options) {
			if (!this.initialized) {
				this.init();
			}

			options = $.extend({
				"class" : "",
				"type" : "info"
			}, options);

			if (this.types.indexOf(options.type) < 0) {
				console.log('Invalid Option Type');
				options.type = "info";
			}

			options.type = "flash-type-" + options.type;

			if (msg) {
				$('<div class="flash-block '+options.type+' '+options['class']+'"><p> '+msg+' </p>').appendTo('#flash-notifications');
			}

			var delay_flash = 0;
			var fixPosition = this.position;
			$("#flash-notifications .flash-block").not(':has(.click-remove)').hide().prepend("<div class='right click-remove'>(click to remove)");
			$("#flash-notifications .flash-block").each(function () {
				var $this = $(this);
				delay_flash += 500;
				if (!$this.queue().length) {
					$this
						.delay(delay_flash).slideDown({duration:1000, step:function() { fixPosition(); } });

					if ($this.is('.flash-stay-open')) return 1; //~= continue;

					$this
						.delay(delay_flash+4000).slideUp({duration:1000, step:function() { fixPosition(); } })
						.queue(function() { $this.remove(); } );
				}
			});

		},
		hideAll: function() {
			$('#flash-notifications .flash-block').slideUp().remove();
		}
	},
	multi: {
		init: function() {
			if (!$.validator) {
				throw "jQuery Validate must be included to use multi form util";
			}


			var multiThis = this;

			$('body').on('click','a.multi-add', function() {
				var $this = $(this);
				var name = $this.data('multi');
				var template = multiThis.getTemplate(name);
				var params = $this.data('multi-params') || [];
				params = params.slice(0);

				var postfix = $this.data('multi-postfix') || "";
				var $list = $("#" + name + postfix + "-multi");
				if (!$list.length) {
					throw "Multi list: " + name + " not found";
				}

				var itemCount = $list.children(".multi-item").length;
				var itemName = "new-" + itemCount;
				params.unshift(itemName);

				var templateGenerated = template(params);

				$(templateGenerated).appendTo($list);

				if (name in multiThis.actions) {
					multiThis.actions[name]();
				}


			});

			$('body').on('click','.multi-item a.multi-remove', function() {
				var $item = $(this).closest('.multi-item');
				$item.find(':input').val(null);
				$item.find('.input-deleted').val(1);
				$item.hide();
			});

		},
		attach : function(name, action) {
			this.actions[name] = action;
		},
		getTemplate : function(name) {
			if (!(name in this.templates)) {
				var template = jQuery("#"+name+"-multi-template");
				if (template.length) {
					this.templates[name] = jQuery.validator.format(template.html().trim());
				} else {
					throw "MultiTemplte: " + name + " not found";
				}
			}
			return this.templates[name];
		},
		actions : {},
		templates : {}
	},
	getQuery: function( name ) {
		name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
		var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
			results = regex.exec(location.search);
		return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}

};

micro.helper = {
	init : function() {
		this.pluginsLoad();
	},
	pluginsLoad : function() {
		//initialization for global plugins

		if ($.fn.select2) {
			$('.select2-init').select2();
		}

		if ($.fn.raty) {
			//$.fn.raty.defaults.path = "";
			$.fn.raty.defaults.half = true;
			$.fn.raty.defaults.space = false;
			$.fn.raty.defaults.size = 23;
			$.fn.raty.defaults.starOn = "/assets/images/icons/star_on.png";
			$.fn.raty.defaults.starOff = "/assets/images/icons/star_off.png";
			$.fn.raty.defaults.starHalf = "/assets/images/icons/star_half.png";
		}

		if ($.fn.datepicker) {
			$.datepicker.setDefaults({
				//dateFormat: 'mm/dd/yy',
				changeMonth: true,
				changeYear: true,
				yearRange: "-80:+10",
				onChangeMonthYear: function(year, month, inst) {
					if (inst.changingFlag) return; //prevent infinite recursion

					var newDateFormat = $(this).datepicker('option','dateFormat');
					var newDate = $datepicker.formatDate(newDateFormat, new Date(year, month-1, inst.selectedDay));

					inst.changingFlag = true;
					$(this).datepicker('setDate', newDate);
					inst.changingFlag = false;
					//debugger;
				}
			});

			$(".datepicker").datepicker();
		}

		if ($.fn.dialog) {
			/* Global settings for jqueryUI Dialog */
			$.extend($.ui.dialog.prototype.options, micro.helper.config.jqUiDialog);
		}


		if ($.timepicker) {
			/*
			$.timepicker._defaults.minutes.interval = 15;
			$.timepicker._defaults.minuteText = "Min";
			$.timepicker._defaults.hours.ends = 24;
			$.timepicker._defaults.rows = 5;
			$.timepicker._defaults.onMinuteShow = function(hour, minute) {
				if (hour == 24 && minute > 0) {
					minute = null;
					return false;
				}
				return true;
			}
			//*/
			$( ".timepicker" ).timepicker({
				showPeriod: true
			});
		}

		if ($.fn.autosize) {
			$('textarea.text-fit').autosize();
		}

		if ($.fn.hideShowPassword) {
			$('input[type=password]').hideShowPassword(false, 'focus');
		}

		if ($.fn.ckeditor) {

			CKEDITOR.config = $.extend({}, CKEDITOR.config, micro.helper.config.ckeditor);


			$(".ckeditor-simple").each(function() {
				//var ck_width, ck_height;
				//ck_width = "100%"; //($element.css("width") == "0px") ? "602px" : $element.css("width");
				//ck_height = "auto"; //( parseInt($element.css("height")) < 100) ? "100px" : $element.css("height");
				//var ck_config = $.extend(simple_config, {width:ck_width, height:ck_height});

				$(this).ckeditor(micro.helper.config.cke_simple());
			});
		}

		if (typeof TableTools != "undefined") {
			TableTools.BUTTONS.download = micro.helper.config.tableTools;
		}


	},
	config : {
		ckeditor : {
			filebrowserBrowseUrl : "/elfinder/ckeditor4",
			removeButtons : "Save,NewPage,Print,Language,Flash,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField",
			contentsCss : ["/assets/css/vendor/bootstrap/bootstrap.css", "/assets/css/site.css"]
		},
		cke_simple : function(options) {
			options || (options = {});
			return $.extend({
				//width: '602px',
				toolbar: [
					//{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', 'Maximize', 'ShowBlocks', '-', 'Preview', '-', 'Templates' ] },
					{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
					{ name: 'align', groups: [ 'align' ], items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
					{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
					{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
					//{ name: 'insert', items: [ 'Link', 'Unlink', 'Anchor', '-', 'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
					{ name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
					{ name: 'colors', items: [ 'TextColor', 'BGColor' ] }
				] //,
				//extraAllowedContent : {
				//	button : {
				//		styles: 'name,type,value',
				//		attributes: 'width,height,border,border-width,border-style,margin,float'
				//	},
				//	form : {
				//		styles: '*',
				//		attributes: '*'
				//	},
				//	input : {
				//		styles: '*',
				//		attributes: '*'
				//	},
				//	label : {
				//		styles: '*',
				//		attributes: 'style,class,for'
				//	},
				//	div : {
				//		styles: '*',
				//		attributes: 'style,class,id'
				//	},
				//	script : '*'
				//}
			}, options);
		},
		//* DataTables TablesTools Plugin "download" template
		tableTools : {
			"sAction": "text",
			"sTag": "default",
			"sFieldBoundary": "",
			"sFieldSeperator": "\t",
			"sNewLine": "<br>",
			"sToolTip": "",
			"sButtonClass": "DTTT_button_text",
			"sButtonClassHover": "DTTT_button_text_hover",
			"sButtonText": "Download",
			"mColumns": "all",
			"bHeader": true,
			"bFooter": true,
			"sDiv": "",
			"fnMouseover": null,
			"fnMouseout": null,
			"sExtraData": [],
			"fnClick": function( nButton, oConfig ) {
				var oParams = this.s.dt.oApi._fnAjaxParameters( this.s.dt );
				$.each(oConfig.sExtraData, function(key, data) {
					if ('getValue' in data && typeof data.getValue == "function") {
						data.value = data.getValue();
					}
				});
				oParams = oParams.concat(oConfig.sExtraData);
				var iframe = document.createElement('iframe');
				iframe.style.height = "0px";
				iframe.style.width = "0px";
				iframe.src = oConfig.sUrl+"?"+$.param(oParams);
				document.body.appendChild( iframe );
			},
			"fnSelect": null,
			"fnComplete": null,
			"fnInit": null
		},
		jqUiDialog : {
			//dialogClass: "modal-dialog",
			modal: true,
			//width: 800,
			height: "auto",
			//minHeight: "400", //doesn't always work as expected
			show: {
				effect: "drop",
				duration: 500,
				direction: 'up'
			},
			hide: {
				effect: "drop",
				direction: "down",
				duration: 500
			},
			resizable: false,
			draggable: false,
			open: function(event, ui) {
				/* close on click anywhere */
				//$(this).closest(".ui-dialog").add('.ui-widget-overlay:last').on('click', { that: this} , function(e) {
				//	$(e.data.that).dialog('close');
				//});

				/* close on click outside of dialog */
				//$('.ui-widget-overlay:last').on('click', { that: this} , function(e) {
				//	$(e.data.that).dialog('close');
				//});

			}
		}
	}
};


micro.core.init();