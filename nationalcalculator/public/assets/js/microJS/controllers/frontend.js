micro || (micro = {});
micro.controllers || (micro.controllers = {});

$(function() {

	var frontend = Object.create(micro.core.controller);

	frontend.routes = function() {
			return {
				'/contact' : this.contact
			};
	};

	frontend.contact = function() {
		micro.utils.form.ajax({
			'selector':'#form-contact',
			success : function(data, textStatus, jqXHR, form, submitData) {
				$('#form-contact').hide();
				$('#contact-success').show();
			}
		});
	};

	micro.controllers.frontend = frontend;
	frontend.init();

});
