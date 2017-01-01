micro || (micro = {});
micro.controllers || (micro.controllers = {});

$(function() {

	var member = Object.create(micro.core.controller);

	member.routes = function() {
			return {
				'/members/account' : this.account,
				'/members/notebook' : this.notebook,
				'/members/subscribe' : this.subscribe,
				'/members/calculator' : this.calculator,
				'/members/calculated' : this.calculated
			};
	};

	member.account = function() {
		micro.utils.form.ajax({
			'selector':'#form-account',
			success : function(data, textStatus, jqXHR, form, submitData) {
				window.location.replace(data.uri);
			}
		});
	};

	member.notebook = function() {

		$("#notebook-list").dataTable({
			"dom": 'l<"H"fr>t<"F"ip>', //"R" allow column reorder, "C" allow column hide.  http://datatables.net/usage/options#sDom   very odd...
			"processing": true,
			"serverSide": true,
			"ajax": "/api/notebook/datatables", //using this will use new api for data source
			//"jQueryUI": true,
			"pagingType": "full_numbers",
			//"pageLength" : 1,
			"columnDefs": [ {
				"targets": "_all",
				"defaultContent": ""
			} ],
			"order": [[6,'desc']],
			"columns": [
				{ "data" : "notebooks.id", "title" : "Id", "width" : "10px", "orderable": true, "searchable": true },
				{ "data" : "notebooks.name", "title" : "Name", "width" : "300px", "orderable": true, "searchable": true },
				{ "data" : "zip.state.abbr","title" : "St", "width" : "20px", "orderable": false, "searchable": false },
				{ "data" : "zip.zip",      "title" : "Zip", "width" : "50px", "orderable": true, "searchable": true },
				{ "data" : "county.name",  "title" : "County", "width" : "200px", "orderable": false, "searchable": true },
				{ "data" : "type",         "title" : "Type", "width" : "200px", "orderable": true, "searchable": true },
				{ "data" : "updated_at",   "title" : "Modified", "width" : "300px", "orderable": true, "searchable": false },
				{ "data" : "manage",       "title" : "View", "width" : "10px", "orderable": false, "searchable": false }
			]
		});

	};

	member.subscribe_stripe = false;
	member.subscribe = function() {
		micro.utils.form.ajax({
			'selector':'#form-cancel-subscribe',
			success : function(data, textStatus, jqXHR, form, submitData) {
				window.location.replace(data.uri);
			}
		});
		micro.utils.form.ajax({
			'onkeyup' : false,
			'selector':'#form-subscribe',
			before : function (submitData) {
				if (micro.controllers.member.subscribe_stripe) {
					//stripe call completed
					return true;
				}
				return false;
			},
			success : function(data, textStatus, jqXHR, form, submitData) {
				window.location.replace(data.uri);
			},
			error : function() {
				if ( !$('.update-card').length || $('.update-card')[0].checked ) {
					micro.controllers.member.subscribe_stripe = false;
				}
			}
		});

		$('.update-card').change(function() {
			if (this.checked) {
				micro.controllers.member.subscribe_stripe = false;
				$('.stripe-data').show();
			} else {
				micro.controllers.member.subscribe_stripe = true;
				$('.stripe-data').hide();
			}
			subscribeButton();
		}).trigger('change');
		$('.update-plan').change(function() {
			if (this.checked) {
				$('.plan-data').show();
			} else {
				$('.plan-data').hide();
			}
			subscribeButton();
		});
		function subscribeButton() {

			if ($('.update-plan')[0].checked || $('.update-card')[0].checked) {
				$('.subscribe-button').show();
				$('.cancel-button').hide();
			} else {
				$('.subscribe-button').hide();
				$('.cancel-button').show();
			}
		}

		$('.cancel-subscription-modal-button').click(function(event) {
			$('.cancel-button input').trigger('click');
		});

		$(':input[data-stripe]').change(function() {
			//if anything is changed with the stripe data, make sure to re-get the stripe token
			micro.controllers.member.subscribe_stripe = false;
		});

		var stripeErrorTemplate = $.validator.format($('.stripe-error-template').html());
		$('#form-subscribe').submit(function(event) {
			event.preventDefault();

			if (micro.controllers.member.subscribe_stripe) return;

			var form = this;
			var $form = $(form);

			if (!$form.valid()) return;

			// Disable the submit button to prevent repeated clicks
			micro.utils.form.disableSubmit(form);
			$form.find('.stripe-errors').empty();

			Stripe.card.createToken($form, function(status, response) {
				if (response.error) {
					// Show the errors on the form
					var $error_element = $(':input[data-stripe="' + response.error.param + '"]');
					if ($error_element.length) {
						var validator = $form.data('validator');
						validator.errorList.push({
							message: response.error.message,
							element: $error_element[0]
						});
						validator.defaultShowErrors();
					} else {
						$form.find('.stripe-errors').html(stripeErrorTemplate(response.error.message));
					}
					micro.utils.form.enableSubmit(form);
				} else {
					// response contains id and card, which contains additional card details
					var token = response.id;
					// Insert the token into the form so it gets submitted to the server
					$form.append($('<input type="hidden" name="subscribe[token]" />').val(token));
					micro.controllers.member.subscribe_stripe = true;
					$form.submit();
				}
			});

			// Prevent the form from submitting with the default action
			return false;
		});

	};

	member.calculator = function() {

		/**
		 * FORM SUBMISSION
		 */
		micro.utils.form.ajax({
			selector: '#form-calculator',
			success : function(data, textStatus, jqXHR, form, submitData) {
				window.location.replace(data.uri);
			}
		});

		/**
		 * SETUP accordion steps
		 */
		$('#accordion > h3 + div').wrap('<div class="accordion-body">');
		//$('#accordion').accordion();

		$('#accordion > div.accordion-body').click(function() {
			$('#accordion > h3').removeClass('ui-accordion-header-active');
			$(this).prev('h3').addClass('ui-accordion-header-active');
		}).on('focus', ':input', function() {
			$(this).closest('div.accordion-body').trigger('click');
		});

		$('#accordion > h3').click(function() { $(this).next().trigger('click') });

		/**
		 * LOAD ZIP and endorsements/misc items when selected
		 */
		window.county_data = [];
		$(".select2-zip").select2({
			allowClear: true,
			minimumInputLength: 3,
			initSelection : function (element, callback) {
				var data = {};
				var curData = element.select2('data');
				if (curData && curData.id && curData.text) {
					data = curData;
				} else {
					data = {id: element.val(), text: element.data("value_text"), data : {
						state_id : 0
					}};
				}
				callback(data);
			},
			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
				url: "/api/zip/select2",
				dataType: 'json',
				data: function (term, page) {
					return {
						q: term, // search term
						page_limit: 10
					};
				},
				results: function (data, page) { // parse the results into the format expected by Select2.
					var formatted = [];
					$.each(data.zips, function(index, value) {
						formatted.push({id:value.id, text:value.zip, data:value});
					});
					return {results: formatted};
				}
			}
		}).on("change", function(event) {
			var counties;
			window.county_data = [];
			if (event.removed) {
				$("input.select2-county").select2("data", null).trigger("change");
				$('.county-group').addClass("hide");

				if (!event.added || event.added.data.state_id != event.removed.data.state_id) {
					$('#endorsements').empty();
					$('#misc').empty();
				}
			}
			if (event.added) {

				var stateId = event.added.data.state_id;

				if (!event.removed || stateId != event.removed.data.state_id ) {
					$('#endorsements').load('/members/calculator/endorsements/' + stateId);
					$('#misc').load('/members/calculator/misc/' + stateId);
				}

				try {
					counties = JSON.parse("{" + event.added.data.counties + "}");
				} catch (err) {
					counties = {};
				}
				Object.keys(counties).forEach(function(key) {
					window.county_data.push({
						"id":key,
						"text":counties[key]
					})
				});
				if (window.county_data.length == 1) {
					$("input.select2-county").select2("data", window.county_data[0]).trigger("change");
				}
				$('.county-group').removeClass("hide");
			}

		});

		/**
		 * LOAD counties and doc data when selected
		 */
		$(".select2-county").select2({
			allowClear: true,
			query: function (query) {
				var data = {results: []};
				data.results = window.county_data;

				query.callback(data);
			},
			initSelection : function (element, callback) {
				var data = {};
				var curData = element.select2('data');
				if (curData && curData.id && curData.text) {
					data = curData;
				} else {
					data = {id: element.val(), text: element.data("value_text")};
				}
				callback(data);
			}
		}).on("change", function(event) {
			//can't use event.added / event.removed since the change is manually triggered
			var select2data = $(this).select2('data');
			if (select2data) { //added
				var countyId = select2data.id;
				$('#documents').load('/members/calculator/docs/' + countyId);
				$('#countyNote').load('/members/calculator/note/' + countyId);

				//$.ajax({
				//	'url':"/api/zip/select2"
				//}).always(function() {
				//
				//}).done(function() {
				//	//add the document form fields
				//}).fail(function() {
				//	//remove document form fields
				//});
			} else { //removed
				//since manually triggered "removed" may not be part of event
				//hide document fields
				$('#documents').empty();
				$('#countyNote').empty();

			}

			console.log("change "+JSON.stringify({val:event.val, added:event.added, removed:event.removed}));
		});

		/**
		 * Set input boxes viewable when loan type is selected
		 */
		$('input.loan-type').change(function() {
			//$('#purchase-price').hide().find('input').attr('disabled', true);
			var $loan = $('#loan-amount');
			var $purchase = $('#purchase-price');
			$loan.hide();
			$purchase.hide();

			var type = $('input.loan-type:checked').val();

			switch (type) {
				case 'purchase':
					$loan.show().find('input').prop('disabled', false);
					$purchase.show().find('input').prop('disabled', false);
				break;
				case 'cash':
					$loan.find('input').val('').prop('disabled', true);
					$purchase.show().find('input').prop('disabled', false);
				break;
				case 'refinance':
					$loan.show().find('input').prop('disabled', false);
					$purchase.find('input').val('').prop('disabled', true);
					break;
				default:
					$purchase.hide().find('input').prop('disabled', true);
					$loan.hide().find('input').prop('disabled', true);
				break;
			}

		}).eq(0).trigger('change');

		/**
		 * VIEW non-default endorsements
		 */
		$('body').on('click', '.view-endorsements', function() {
			if ($('.endorse-non-std:first').is(":visible")) {
				$('.endorse-non-std').hide();
			} else {
				$('.endorse-non-std').show();
			}
		});

		$('.select2').on("select2-opening select2-clearing", function() {
			$(this).parent().trigger('click');
		});

	};

	member.calculated = function() {

		micro.utils.form.ajax({
			selector: '#form-export',
			before: function() {
				var type = $('.export-output:checked').val();
				switch (type) {
					case 'HUD':
					case 'GFE':
					case 'email':
						$('.export-output').text(type);
						$('#wait-modal').modal({show:true, keyboard:false, backdrop:'static'});
						break;
					case 'print':
							window.print();
							return false;
						break;
					default:
						return false;
				}

			},
			success: function (data, textStatus, jqXHR, form, submitData) {
				micro.utils.form.enableSubmit(form);

				var type = $('.export-output:checked').val();

				switch (type) {
					case 'HUD':
					case 'GFE':
						var downloadUrl = form.action + "/" + type;
						$.fileDownload(downloadUrl);
						break;
					case 'email':
						window.setTimeout(function() {
							alert('Email sent!');
						}, 500);
						break;
				}

			},
			complete: function(jqXHR, textStatus) {
				$('#wait-modal').modal('hide');
			}
		});

		$('.export-output').change(function() {
			var type = $('.export-output:checked').val();
			if ($(this).val()) {
				$('.export-button').prop('disabled', false);
			}

			if (type == 'email') {
				$('#export-email-box').show();
			} else {
				$('#export-email-box').hide();
			}


		});


	};

	micro.controllers.member = member;
	member.init();

});
