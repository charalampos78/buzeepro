micro || (micro = {});
micro.controllers || (micro.controllers = {});

$(function() {

	var account = Object.create(micro.core.controller);

	account.routes = function() {
		return {
			'/login/recover' : this.recover,
			'/login' : this.login,
			'/register' : this.register
		};
	};

	account.recover = function() {
		micro.utils.form.ajax({
			'selector':'#form-recover',
			success : function(data, textStatus, jqXHR, form, submitData) {
				window.location.replace("/login");
			}
		});
	};

	account.login = function() {
		micro.utils.form.ajax({
			'selector':'#form-login',
			success : function(data, textStatus, jqXHR, form, submitData) {
				window.location.replace(data.uri);
			}
		});

		micro.utils.form.ajax({
			'selector':'#form-forgot',
			success : function(data, textStatus, jqXHR, form, submitData) {
				$('#form-forgot fieldset').hide();
				$('.recover-message').show();
			}
		});

		$('#form-login #login\\[email\\]').focus();


		$('#show-forgot').click(function() {
			$('#form-login').hide();
			$('#form-forgot').show();
			$('#form-forgot input[name="login[email]"]').val($('#form-login input[name="login[email]"]').val());
		});
		$('#show-login').click(function() {
			$('#form-forgot').hide();
			$('#form-login').show();
			$('#form-login input[name="login[email]"]').val($('#form-forgot input[name="login[email]"]').val());
		});
		if (window.location.hash == "#forgot") {
			$('#show-forgot').trigger('click');
		}


	};

	account.register = function() {
		micro.utils.form.ajax({
			'selector':'#form-register',
			success : function(data, textStatus, jqXHR, form, submitData) {
				window.location.replace(data.uri);
			}
		});
	};

		micro.controllers.account = account;
	account.init();

});
