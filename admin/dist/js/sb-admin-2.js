/*!
 * Start Bootstrap - SB Admin 2 v3.3.7+1 (http://startbootstrap.com/template-overviews/sb-admin-2)
 * Copyright 2013-2016 Start Bootstrap
 * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap/blob/gh-pages/LICENSE)
 */
$(function() {
    $('#side-menu').metisMenu();
});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        var topOffset = 50;
        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    // var element = $('ul.nav a').filter(function() {
    //     return this.href == url;
    // }).addClass('active').parent().parent().addClass('in').parent();
    var element = $('ul.nav a').filter(function() {
        return this.href == url;
    }).addClass('active').parent();

    while (true) {
        if (element.is('li')) {
            element = element.parent().addClass('in').parent();
        } else {
            break;
        }
    }
});


$(document).ready(function(){
	//alert("hi");
		$.ajax({
				type: "GET",
				url:  "../php/admin.php",
				datatype: 'json', 	
				data:{functionName:'morrisLine'},
					//"morrisLine":[k,v]
					
				success: function(response)                    
				{
			
				var Data =  JSON.parse(response);
				
				var mystring = "[";
				
				for(var i = 0; i < Data.length; i++) {
					var obj = Data[i];
					//console.log(obj.first);
					mystring += '{ "log_hour": \"'+obj.log_hour+'\", "visits": '+obj.visits+' },'
									
				}
				
				mystring = mystring.substring(0, mystring.length-1);
				mystring += "]";
				
				//console.log(mystring);

				var chart = AmCharts.makeChart( "morrisLine", {
				"type": "serial",
				"theme": "light",
				"dataProvider": JSON.parse(mystring),  
				"gridAboveGraphs": true,
				"startDuration": 1,
				"graphs": [ {
				"balloonText": "[[category]]: <b>[[value]]</b>",
				"fillAlphas": 0.8,
				"lineAlpha": 0.2,
				"type": "column",
				"valueField": "visits"
			  } ],
				"chartCursor": {
				"categoryBalloonEnabled": false,
				"cursorAlpha": 0,
				"zoomable": false
			  },
				"categoryField": "log_hour",
				"categoryAxis": {
				"gridPosition": "start",
				"gridAlpha": 0,
				"tickPosition": "start",
				"tickLength": 20,
				"labelRotation": 45
			  },
				"export": {
				"enabled": true
			  }

			});
										
        }}); 


		
		
		$.ajax({
				type: "GET",
				url:  "../php/admin.php",
				datatype: 'json', 	
				data:{functionName:'pieChart'},
					//"morrisLine":[k,v]
					
				success: function(response)                    
				{
			    //parse string to JSON objects
				var Data =  JSON.parse(response);
				//construct JSON Object string
				var mystring = "[";
				
				for(var i = 0; i < Data.length; i++) {
					var obj = Data[i];
					//console.log(obj.first);
					mystring += '{ "email": \"'+obj.email+'\", "visits": '+obj.visits+' },'
									
				}
				
				mystring = mystring.substring(0, mystring.length-1);
				mystring += "]";
				
				//console.log(mystring);

				var Pie = AmCharts.makeChart( "pieChart", {
				"type": "pie",
				"labelsEnabled": false,
				"autoMargins": false,
				"marginTop": 20,
				"marginBottom": 20,
				"marginLeft": 20,
				"marginRight": 20,
				"pullOutRadius": 10,
				"startDuration": 0,
				"theme": "light",
				  "addClassNames": true,
				 "legend":{
					"position":"bottom",
				//	"marginRight":20,
				//	"autoMargins":false
				  },
				  "innerRadius": "20%",
				  "defs": {
					"filter": [{
					  "id": "shadow",
					  "width": "300%",
					  "height": "300%",
					  "feOffset": {
						"result": "offOut",
						"in": "SourceAlpha",
						"dx": 0,
						"dy": 0
					  },
					  "feGaussianBlur": {
						"result": "blurOut",
						"in": "offOut",
						"stdDeviation": 5
					  },
					  "feBlend": {
						"in": "SourceGraphic",
						"in2": "blurOut",
						"mode": "normal"
					  }
					}]
				  },
				  "dataProvider": JSON.parse(mystring),
				  "valueField": "visits",
                  "titleField": "email",
                  "export": {
				  "enabled": true
					}
			});

	    
										
      }});

			Pie.addListener("init", handleInit);

			Pie.addListener("rollOverSlice", function(e) {
			  handleRollOver(e);
			});

			function handleInit(){
			  Pie.legend.addListener("rollOverItem", handleRollOver);
			}

			function handleRollOver(e){
			  var wedge = e.dataItem.wedge.node;
			  wedge.parentNode.appendChild(wedge);
			}	  
		
});

$('#submitquery').on('click', function(e){
	
e.preventDefault();
	
	//query = "\""; 
	var query = document.getElementById("query").value;
    //query += "\"";
	
	//alert(query);
	
	$.ajax({
		type: "GET",
		url:  "../php/admin.php",
		datatype: 'json', 
		data: {functionName:'sql' , query: query},
		success: function(response)                    
				{
					//alert("success");
					alert(response);
					
					
					
					
				}
		
		
		
		
	})
	
	
	
	
});

