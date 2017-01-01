<?php require_once "/PHPMailer/gmail.php";   ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta property="og:image" content="img/logo.png"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>DEV - BuZee - Innovation - Leadership - Performance</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">

    <!-- Custom Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css" type="text/css">

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="css/animate.min.css" type="text/css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/creative.css" type="text/css">
    <link rel="stylesheet" href="css/mycss.css" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body id="page-top">
<!--logomakr.com/94xGxS
color: ff4a00   
    Flow graphic by <a href="http://www.typicons.com">Stephen Hutchings</a> and Universal Account Business graphic by <a href="http://www.freepik.com/">Freepik</a> from <a href="http://www.flaticon.com/">Flaticon</a> are licensed under <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0">CC BY 3.0</a>.Gear,Arrow graphics by <a href="undefined">undefined</a> from <a href="http://logomakr.com">Logomakr</a> are licensed under <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0">CC BY 3.0</a>.Direction graphic by <a href="http://www.freepik.com">Freepik</a> from <a href="http://www.flaticon.com/">Flaticon</a> is licensed under <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0">CC BY 3.0</a>. Made with <a href="http://logomakr.com" title="Logo Maker">Logo Maker</a>-->
    
    
    
    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
              <a class="navbar-brand page-scroll" href="#page-top">
                <img style="width:70px;" src="img/logo.png"></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a class="page-scroll" href="#about">What is BuZee</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#services">In a Nutshell</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#portfolio">Our Services</a>
                    </li>                           
                    <li>
                        <a class="page-scroll" href="#contact">Contact Us</a>
                    </li>
					 <li>
                        <a id = "formlink" href="#">Client Login</a>
					
						<form method="post" id = "loginform" class= "hidden">
							  <div class="form-group">
								<label style = "color:white;" for="email">Email address:</label>
								<input type="email" class="form-control" id="email" name = "email">
							  </div>
							  <div class="form-group">
								<label style = "color:white;" for="pwd">Password:</label>
								<input type="password" class="form-control" id="pwd" name = "pwd">
							  </div>
							  <div class="checkbox">
								<label style = "color:white;"><input  type="checkbox"> Remember me</label>
							  </div>
							  <button id="submitlogin" type="submit" class="btn btn-default">Submit</button>
							</form>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    <header>
        <div class="header-content">
            <div class="header-content-inner">
   
                <h1 id = "front"><img style="width:300px;" src="img/logo.png"></h1>
<!--                <hr>-->
                <h2>Innovation - Leadership - Performance</h2>
                <p></p>
                <a href="#about" class="btn btn-primary btn-xl page-scroll">Find Out More</a>
            </div>
        </div>

    </header>

    <section class="bg-primary" id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 text-center">
                    <h2 class="section-heading">What is buZee!</h2>
                    <hr class="light">
                    <p class="text-faded">Business Intelligence, data management and visualization solutions, operation flow analysis.
                         <hr class="light">
                    <a href="#" class="btn btn-default btn-xl">Get Started Today!</a>
                </div>
            </div>
        </div>
    </section>

    <section id="services">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">In A Nutshell</h2>
                    <hr class="primary">
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-md-3 text-center">
                    <div class="service-box">
<!--                        <i class="fa fa-4x fa-diamond wow bounceIn text-primary"></i>-->
                        <img class="service-img" src="img/dash.png">
                        <h3>Data Management & Visualization</h3>
                        <p class="text-muted">Full range of data services available at your fingertips. Data arhitecture, data mining, data cleansing, ETL, scheduling and management.
						                      Empowering the business to make the right decisions using modern visualization solutions.</p>
                    </div>
                </div>
              
                <div class="col-lg-4 col-md-3 text-center">
                    <div class="service-box">
<!--                        <i class="fa fa-4x fa-newspaper-o wow bounceIn text-primary" data-wow-delay=".2s"></i>-->
                        <img class="service-img" src="img/process.png">
                        <h3>Process Improvement</h3>
                        <p class="text-muted">With a proven record in performance improvement, Buzee will help you analyze your current processes and 
						             workflows and provide you with innovative solutions in order to maximize efficiency.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-3 text-center">
                    <div class="service-box">
<!--                        <i class="fa fa-4x fa-heart wow bounceIn text-primary" data-wow-delay=".3s"></i>-->
                        <img class="service-img" src="img//management_icon.png">
                        <h3>Innovation </h3>
                        <p class="text-muted">Let us help you build and execute the right plan your business. 
						 BuZee will do the footwork for you and help you in areas that your business needs insight, discovery and solutioning. </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="no-padding" id="portfolio">
        <div class="container-fluid">
            <div class="row no-gutter">
               
                <div class="col-lg-4 col-sm-6">
                    <a href="#" class="portfolio-box">
                        <img src="img/portfolio/1.png" class="img-responsive" alt="">
                        <div class="portfolio-box-caption">
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">
                                    
                                </div>
                                <div class="project-name">
                                    Data Management & Visualization
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <a href="#" class="portfolio-box">
                        <img src="img/portfolio/3.jpg" class="img-responsive" alt="">
                        <div class="portfolio-box-caption">
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">
                                  
                                </div>
                                <div class="project-name">
                                    Process Improvement
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
               
              
                <div class="col-lg-4 col-sm-6">
                    <a href="#" class="portfolio-box">
                        <img src="img/portfolio/6.jpg" class="img-responsive" alt="">
                        <div class="portfolio-box-caption">
                            <div class="portfolio-box-caption-content">
                                <div class="project-category text-faded">
                              
                                </div>
                                <div class="project-name">
                                    Innovation
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    
    
<!--
     <section class="no-padding" id="pricing">
        
         <div class="container">
               <div class="row">    
                   <div class="col-lg-8 col-lg-offset-2 text-center">
                       <h2 class="section-heading">Pricing Matrix</h2>
          
                       <div class="col-lg-4 text-center">
                     
                                <img src="img/d.jpg">  
                                <div>$600</div>
                                <div>Delivery and maintainance of 2 reports per month</div>
                        </div>
                      
                       <div class="col-lg-4 text-center">
                          
                           <img src="img/d.jpg"> 
                               <div>$1200</div>
                               <div>Delivery maintainance and data analysis of 3 reports</div> 
                           </div>                      
                    
                             <div class="col-lg-4 text-center">
                       
                           <img src="img/d.jpg"> 
                               <div>$1800</div>
                               <div>Delivery maintainance and data analysis of 3 reports</div> 
                          
                               </div>
                    
                            <div class="col-lg-4 text-center">                    
                           <img src="img/d.jpg"> 
                               <div>Custom Pricing</div>
                               <div>Custom services</div>                
                                </div>
                    </div>
    </section>
    
-->

<!--

    <aside class="bg-dark">
        <div class="container text-center">
            <div class="call-to-action">
                <h2>Free Download at Start Bootstrap!</h2>
                <a href="#" class="btn btn-default btn-xl wow tada">Download Now!</a>
            </div>
        </div>
    </aside>
-->

    <section id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 text-center">
                    <h2 class="section-heading">Let's Get In Touch!</h2>
                    <hr class="primary">
                    <p>Give us a call or send us an email let's discuss how Buzee can help you in your organization !</p>
                </div>
                <div class="col-lg-4 col-lg-offset-2 text-center">
                    <i class="fa fa-phone fa-3x wow bounceIn"></i>
                    <p>954-599-3001</p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fa fa-envelope-o fa-3x wow bounceIn" data-wow-delay=".1s"></i>
                    <p><a href="mailto:your-email@your-domain.com">buzee@buzeepro.com</a></p>
                </div>
                    
                    




  			<div class="col-md-6 col-md-offset-3">
  				<h1 class="page-header text-center">Contact Form</h1>
				<form class="form-horizontal" role="form" method="post" action="index.php">
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label">Name</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="name" name="name" placeholder="First & Last Name" required value="<?php if (isset($_POST['name'])) {echo $_POST['name'];}?>">
							<?php if (isset($errMessage)) echo "<p class='text-danger'>error</p>";?>
				</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-sm-2 control-label">Phone</label>
						<div class="col-sm-10">
							<input type="phone" class="form-control" id="phone" name="phone" required placeholder="(XXX)XXX-XXXX" value="<?php if (isset($_POST['phone'])) {echo $_POST['phone'];}?>">
							<?php if (isset($errMessage)) echo "<p class='text-danger'>error</p>";?>					
					</div>
					</div>

					<div class="form-group">
						<label for="email" class="col-sm-2 control-label">Email</label>
						<div class="col-sm-10">
							<input type="email" class="form-control" id="email" name="email" required placeholder="example@domain.com" value="<?php if (isset($_POST['email'])) {echo $_POST['email'];}?>">
							<?php if (isset($errMessage)) echo "<p class='text-danger'>error</p>";?>					
					</div>
					</div>
					<div class="form-group">
						<label for="message" class="col-sm-2 control-label">Message</label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="4" name="message"></textarea>
													</div>
					</div>

<!--
					<div class="form-group">
						<label for="human" class="col-sm-2 control-label">2 + 3 = ?</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="human" name="human" placeholder="Your Answer">
												</div>
					</div>
-->
					<div class="form-group">
						<div class="col-sm-10 col-sm-offset-2">
							<input id="submit" name="submit" type="submit" value="Send" class="btn btn-primary">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-10 col-sm-offset-2">
							
						</div>
					</div>
				</form> 
			
		
                    

                    
                    
                    
                    
            </div>
        </div>
    </section>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>
	<script src="js/myjs.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/jquery.fittext.js"></script>
    <script src="js/wow.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/creative.js"></script>

</body>

</html>
