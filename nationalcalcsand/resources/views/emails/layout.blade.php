<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Basic Page Needs
	================================================== -->
    <meta charset="utf-8"/>

    <title>National Calculator</title>

    <meta name="keywords" content=""/>
    <meta name="author" content=""/>
    <meta name="description" content=""/>

    <!-- Mobile Specific Metas
	================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body style="padding:0; margin:0;">

<div style="background-color:#242400;">
<!--[if gte mso 9]>
<v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
    <v:fill type="tile" src="http://ec2-54-148-194-23.us-west-2.compute.amazonaws.com/assets/media/bg_body.png" color="#242400"/>
</v:background>
<![endif]-->
<table height="100%" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; color:#333;">
<tr>
<td valign="top" align="left" background="http://ec2-54-148-194-23.us-west-2.compute.amazonaws.com/assets/media/bg_body.png" style="padding:20px;">

    <div id="wrap" style="margin:auto; max-width:788px;">

        <div class="container" style="margin-bottom:-10px;">
            <div class="row">
                <div id="logo-wrapper" class="col-xs-12">
                    <div id="logo-box">
                        <div class="logo-container" style="text-align: center; font-family: monospace; font-size:3em;">
                            <a href="http://nationalCalculator.com" style="color:white; background:transparent; text-decoration: none; text-transform: uppercase;">
                                <img alt="National Calculator" title="National Calculator"
                                     src="http://ec2-54-148-194-23.us-west-2.compute.amazonaws.com/assets/media/logo.png"
                                     style="width:100%;"
                                >
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ./ navbar -->

        <!-- Container -->
        <div class="container" style="">
            <!-- Content -->
            <div class="row page-body" id="page-body" style="background: rgba(255, 255, 168, 0.55); border: 1px solid rgba(255, 255, 46, 0.6); border-radius: 10px; padding: 15px;">
                <div class="col-xs-12" id="page-body-content-wrapper" style="border: 1px solid #636363; border-radius: 7px; background: white; padding: 25px;">

                    @yield('content')

                </div>
            </div>
            <!-- ./ content -->
        </div>
        <!-- ./ container -->
        <!-- the following div is needed to make a sticky footer -->
        <div id="push"></div>
    </div>
    <!-- ./wrap -->

    <div id="wrap" style="margin:10px auto; max-width:788px;">
        <div class="container">
            <!-- Content -->
            <div class="row page-body" id="page-body" style="background: rgba(255, 255, 168, 0.55); border: 1px solid rgba(255, 255, 46, 0.6); border-radius: 10px; padding: 5px;">
                <div class="col-xs-12" id="page-body-content-wrapper" style="border: 1px solid #636363; border-radius: 7px; background: white; padding: 5px 20px;">
                    <div id="footer">
                        <div>
                            {{ HTML::content('footer') }}
                            <p class="muted credit" style="margin: 5px 0;">&copy; National Calculator 2016 </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</td>
</tr>
</table>
</div>
</body>
</html>
