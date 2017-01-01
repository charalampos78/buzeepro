micro || (micro = {});

$(function() {

	var $footer = $('#footer');
	var $pageBody = $('#page-body');
	var $pageBodyContent = $('#page-body-content-wrapper');
	var resizeActive = false;

	if ($pageBody.length && $pageBodyContent.length) {
		function stickyFooter() {
			var height, footTop, pageBodyBottom, pageBodyContentHeight, pageBodyAdj;
			height = $footer.closest('.container').outerHeight();
			$('#wrap').css({marginBottom: -1 * height});
			$('#push').css({height: height});

			$pageBodyContent.css({minHeight: "auto"});

			footTop = $footer.offset().top;
			pageBodyBottom = $pageBody.outerHeight() + $pageBody.offset().top;
			pageBodyContentHeight = $pageBodyContent.height();

			pageBodyAdj = footTop - pageBodyBottom;
			pageBodyAdj += pageBodyContentHeight + 40;

			$pageBodyContent.css({minHeight: pageBodyAdj});
			resizeActive = false;
		}

		stickyFooter();
		$(window).resize(function () {
			console.log('------------resize');
			if (!resizeActive) {
				console.log('settimer');
				resizeActive = true;
				setTimeout(function () {
					console.log('sticky');
					stickyFooter();
				}, 100);
			}
		});
	}

	$(".zip_select").select2({
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
					formatted.push({id:value.id, text:value.zip});
				});
				return {results: formatted};
			}
		}
	});

});
