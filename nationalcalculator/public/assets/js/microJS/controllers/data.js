micro || (micro = {});

$(function() {

	var data = micro.core.createController('data');

	data.routes = function() {
		return {
			'/manage/data/states' : this.states,
			'/manage/data/counties' : this.counties,
			'/manage/data/zips' : this.zips,
			'/manage/data/state-' : this.stateEdit, //add & edit
			'/manage/data/county-' : this.countyEdit, //add & edit
			'/manage/data/zip-' : this.zipEdit //add & edit
		};
	};

	data.states = function() {
		$("#states-list").dataTable({
			"dom": '<"H"fr>t<"F"i>', //"R" allow column reorder, "C" allow column hide.  http://datatables.net/usage/options#sDom   very odd...
			"processing": false,
			"serverSide": false,
			"ajax": {
				"url": "/api/state/datatables",
				"data": function ( d ) {
					d.iDisplayLength = 100;
				}
			}, //using this will use new api for data source
			"pagingType": "full_numbers",
			"pageLength" : 100,
			"columnDefs": [ {
				"targets": "_all",
				"defaultContent": ""
			} ],
			"order": [[0,'asc']],
			"columns": [
				{ "data" : "name",    "title" : "Name", "width" : "100px", "orderable": true, "searchable" : true },
				{ "data" : "status_flag",       "title" : "Status", "width" : "100px", "orderable": true, "searchable" : true },
				{ "data" : "endorsement_count", "title" : "Endo#", "width" : "10px", "orderable": true, "searchable" : true },
				{ "data" : "misc_count",        "title" : "Misc#", "width" : "10px", "orderable": true, "searchable" : true },
				{ "data" : "rate_count",        "title" : "Rate#", "width" : "10px", "orderable": true, "searchable" : true },
				{ "data" : "manage",  "title" : "Manage", "width" : "100px", "orderable": false, "searchable": false }
			]
		});
	};
	data.stateEdit = function() {
		micro.utils.form.ajax({
			selector : '#state-form',
			success : function(data, textStatus, jqXHR, form, submitData) {
				window.location.replace(data.uri);
			}
		});

		micro.utils.multi.init();

		var that = this;
		micro.utils.multi.attach('rate-county', function() {
			that.stateEditCounty();
		});
		this.stateEditCounty();

	};
	data.stateEditCounty = function() {
		$(".select2-county").filter(function() {
			return $(this).data('select2') == undefined;
		}).select2({
			allowClear: true,
			minimumInputLength: 3,
			multiple:true,
			initSelection : function (element, callback) {
				var data = [];
				$.each(element.data('select2_data'), function (key , value) {
					data.push({id: key, text: value});
				});
				callback(data);
			},
			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
				url: "/api/county/select2",
				dataType: 'json',
				data: function (term, page) {
					return {
						q: term, // search term
						page_limit: 10,
						state_id: $('#state-id').val() // search term
					};
				},
				results: function (data, page) { // parse the results into the format expected by Select2.
					var formatted = [];
					$.each(data.counties, function(index, value) {
						formatted.push({id:value.id, text:value.name, data:value});
					});
					return {results: formatted};
				}
			}
		});
	};
	data.counties = function() {
		$("#counties-list").dataTable({
			"dom": 'l<"H"fr>t<"F"ip>', //"R" allow column reorder, "C" allow column hide.  http://datatables.net/usage/options#sDom   very odd...
			"processing": true,
			"serverSide": true,
			"ajax": "/api/county/datatables", //using this will use new api for data source
			"pagingType": "full_numbers",
			"pageLength" : 25,
			"columnDefs": [ {
				"targets": "_all",
				"defaultContent": ""
			} ],
			"order": [[0,'asc']],
			"columns": [
				{ "data" : "counties.name", "title" : "Name", "width" : "100px", "orderable": true, "searchable" : true },
				{ "data" : "state.name",    "title" : "State", "width" : "100px", "orderable": true, "searchable" : true },
				{ "data" : "document_count","title" : "Doc#", "width" : "10px", "orderable": false, "searchable" : false },
				{ "data" : "status_flag",   "title" : "Status", "width" : "10px", "orderable": false, "searchable" : false },
				{ "data" : "manage",        "title" : "Manage", "width" : "100px", "orderable": false, "searchable": false }
			]
		});
	};
	data.countyEdit = function() {
		micro.utils.form.ajax({
			selector : '#county-form',
			success : function(data, textStatus, jqXHR, form, submitData) {
				window.location.replace(data.uri);
			}
		});

		micro.utils.form.ajax({
			selector : '#county-copy-form',
			success : function(data, textStatus, jqXHR, form, submitData) {
				$('#county-copy-form .select2-county').select2('data', null);
			}
		});

		$('#copy-county-toggle').click(function() {
			$('#copy-county-box').slideToggle();
		});

		this.stateEditCounty();

		micro.utils.multi.init();

	};

	data.zips = function() {
		$("#zips-list").dataTable({
			"dom": 'l<"H"fr>t<"F"ip>', //"R" allow column reorder, "C" allow column hide.  http://datatables.net/usage/options#sDom   very odd...
			"processing": true,
			"serverSide": true,
			"ajax": "/api/zip/datatables", //using this will use new api for data source
			"pagingType": "full_numbers",
			"pageLength" : 25,
			"columnDefs": [ {
				"targets": "_all",
				"defaultContent": ""
			} ],
			"order": [[0,'asc']],
			"columns": [
				{ "data" : "zip",         "title" : "Name", "width" : "100px", "orderable": true, "searchable" : true },
				{ "data" : "county.name", "title" : "County", "width" : "100px", "orderable": true, "searchable" : true },
				{ "data" : "state.abbr",  "title" : "State", "width" : "100px", "orderable": false, "searchable" : false },
				{ "data" : "status_flag", "title" : "Status", "width" : "10px", "orderable": false, "searchable" : false },
				{ "data" : "manage",      "title" : "Manage", "width" : "100px", "orderable": false, "searchable": false }
			]
		});
	};
	data.zipEdit= function() {
		micro.utils.form.ajax({
			selector : '#zip-form',
			success : function(data, textStatus, jqXHR, form, submitData) {
				window.location.replace(data.uri);
			}
		});

		//if state isn't selected, then can't select county since it's state dependent
		$("#state-id").select2({allowClear: true}).on("change", function(event) {
			var $s2county = $('.select2-county');
			$s2county.select2('data', null).trigger('change');
			if ($(this).val()) {
				$s2county.removeClass('hide');
			} else {
				$s2county.addClass('hide');
			}
		});

		$(".select2-county").select2({
			allowClear: true,
			minimumInputLength: 3,
			initSelection : function (element, callback) {
				var data = {};
				var curData = element.select2('data');
				if (curData && curData.id && curData.text) {
					data = curData;
				} else {
					data = {id: element.val(), text: element.data("value_text")};
				}
				callback(data);
			},
			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
				url: "/api/county/select2",
				dataType: 'json',
				data: function (term, page) {
					return {
						q: term, // search term
						page_limit: 10,
						state_id : $('#state-id').val()
					};
				},
				results: function (data, page) { // parse the results into the format expected by Select2.
					var formatted = [];
					$.each(data.counties, function(index, value) {
						formatted.push({id:value.id, text:value.name, data:value});
					});
					return {results: formatted};
				}
			}
		});

		$('#zip-zip, .select2-county').change(function() {
			$('#extra-zips').load("/manage/data/zip-extra", {
				zip_id : $('#zip-id').val(),
				county_id : $('.select2-county').val(),
				zip : $('#zip-zip').val()
			});
		});

	};

	data.init();

});
