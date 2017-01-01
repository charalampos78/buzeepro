$("#formlink").click(function(){
		$("form").toggleClass("hidden");
		//alert("JO");
});



	 
$("#submitlogin").click(function(e){

e.preventDefault();

     email = $("#email").val();
	 //alert(email);
	 //alert("sdfgsdfg");
     pwd = $("#pwd").val();
        
    $.ajax({
          type: "POST",
           url: "./php/loginphp.php",
         data:{
                email : email, 
                pwd : pwd,
		        action : "login"
              },
         success:  function(data)  { 
                 //alert("Hi");
                // alert(data);
                 if ($.trim(data) === "success")
                         {
                    
			 sessionStorage.setItem("email", $('#email').val());
			 $('#email').val('');  
             $('#pwd').val(''); 
                 window.location.href  = "./admin/index.php";
                         }

            else if ($.trim(data) === "error")
                        {
                        alert("Login Failed");
                         $('#email').val('');  
                         $('#pwd').val('');
                      }
                }
        });
    
}); 

$(document).ready(function() {
			$("#loginform").submit(function(){
				//alert("JO");
				// alert(event);
				login(event);
			});
});

