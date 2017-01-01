micro || (micro = {});

$(function() {

	var manage = micro.core.createController('manage');

	manage.routes = function() {
		return {
			'/manage/user' : this.user,
			'/manage/content' : this.content
		};
	};
	manage.user = function() {

		micro.utils.form.ajax({
			'selector':'#user-form',
			success : function(data, textStatus, jqXHR, form, submitData) {
				window.location.replace(data.uri);
				//$('#form-forgot fieldset').hide();
				//$('.recover-message').show();
			}
		});


		$("#users-list").dataTable({
			"dom": 'l<"H"fr>t<"F"ip>', //"R" allow column reorder, "C" allow column hide.  http://datatables.net/usage/options#sDom   very odd...
			"processing": true,
			"serverSide": true,
			//"sAjaxSource": "/api/user/datatables", //using this will use old api for data source
			"ajax": "/api/user/datatables", //using this will use new api for data source
			//"jQueryUI": true,
			"pagingType": "full_numbers",
			//"pageLength" : 1,
			"columnDefs": [ {
				"targets": "_all",
				"defaultContent": ""
			} ],
			"order": [[0,'desc']],
			"columns": [
				{ "data" : "id",           "title" : "Id", "width" : "30px", "orderable": true, "searchable": true },
				{ "data" : "profile.last_name", "title" : "Name", "width" : "100px", "orderable": true, "searchable" : true },
				{ "data" : "username",     "title" : "Username", "width" : "100px", "orderable": true },
				{ "data" : "email",        "title" : "Email", "width" : "100px", "orderable": true },
				{ "data" : "created_at",   "title" : "Created", "width" : "100px", "orderable": true },
				{ "data" : "manage",       "title" : "Manage", "width" : "100px", "orderable": true, "searchable": false }
			]
		});

		$('#users-list').on('click','tbody > tr', function(event) {
			var user_id = $(this).data().user_id;
			//window.location = "/manage/user/edit/" + user_id;
		});
	};

	manage.content = function() {

		micro.utils.form.ajax({
			'selector':'#content-form',
			success : function(data, textStatus, jqXHR, form, submitData) {
				window.location.replace(data.uri);
			}
		});


		$("#contents-list").dataTable({
			"dom": 'l<"H"fr>t<"F"ip>', //"R" allow column reorder, "C" allow column hide.  http://datatables.net/usage/options#sDom   very odd...
			"processing": true,
			"serverSide": true,
			//"sAjaxSource": "/api/content/datatables", //using this will use old api for data source
			"ajax": "/api/content/datatables", //using this will use new api for data source
			//"jQueryUI": true,
			"pagingType": "full_numbers",
			//"pageLength" : 1,
			"columnDefs": [ {
				"targets": "_all",
				"defaultContent": ""
			} ],
			"order": [[0,'desc']],
			"columns": [
				{ "data" : "key",     "title" : "Key", "width" : "30px", "orderable": true, "searchable": true },
				{ "data" : "name",    "title" : "Name", "width" : "100px", "orderable": true, "searchable" : true },
				{ "data" : "content", "title" : "Content", "width" : "100px", "orderable": true },
				{ "data" : "manage",       "title" : "Manage", "width" : "100px", "orderable": true, "searchable": false }
			]
		});

		$('#contents-list').on('click','tbody > tr', function(event) {
			var content_id = $(this).data().content_id;
			//window.location = "/manage/content/edit/" + content_id;
		});

		$('.ckeditor').ckeditor({
			removeButtons: 'Font'
		});

	};

	manage.init();

});
