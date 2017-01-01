<!DOCTYPE html>
<html lang="en">

    @include('layouts.head')

    <body>
        <!-- Notifications -->
        <div id="flash-notifications">
        	{{ Flash::get_flash_html() }}
        </div>
        <!-- ./ notifications -->

        <!-- To make sticky footer need to wrap in a div -->
        <div id="wrap">
            <!-- Navbar -->
            @section('navtop')
				@include('layouts.navtop')
            @show

            <!-- ./ navbar -->

            <!-- Container -->
            <div class="container">
                <!-- Content -->
                @section('content-wrapper')
                    @section('content')
                        Nothing to see here, please move along.
                    @show
                @show
                <!-- ./ content -->

            </div>
            <!-- ./ container -->

            <!-- the following div is needed to make a sticky footer -->
            <div id="push"></div>
        </div>
        <!-- ./wrap -->


        <div class="container">
            <div id="footer">
                <div>
                    @yield('footer')
                    <p class="muted credit">&copy; {{Config::get('app.name')}} {{ date('Y') }} </p>
                </div>
            </div>
        </div>

        <!-- Javascripts
        ================================================== -->
        <!-- yield('scripts', \View::make('layouts.defaultJS')) -->
		@section('scripts')
			@include('layouts.defaultJS')
		@show

		@yield('inlineJS')

    </body>
</html>