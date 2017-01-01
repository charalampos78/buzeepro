/*
* This is only necessary if everything isn't compiled together into a single file
*
* */
$(function() {

	requirejs.config({

		paths: {
			'microBoot' : './microBoot.js',
			'microCore' : './microCore.js',
			'microRoutes' : './microRoutes.js',
			'controller.manage' : './controller/manage.js',
			'controller.account' : './controller/account.js'
		},
		bundles: {
			'micro' : ['microBoot', 'microCore', 'microRoutes']
		}
	});


	requirejs(['micro'], function() {

	});

});