<div class="navbar navbar-default navbar-inverse navbar-static-top" id="nav-top">
    <div class="container">
        <div class="collapse-box">
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav navbar-right" id="utility-menu">
                    @if (Auth::check())
                        @if (Auth::user()->hasRole('admin'))
                            {{ HTML::menuItem('Admin', 'manage', ['is'=>['manage', 'manage/*']]) }}
                        @endif
                        {{ HTML::menuItem('Members Area', 'members', ['is'=>['members', 'members/*']]) }}
                        {{ HTML::menuItem("Hi ".Auth::user()->profile->first_name, '/members/account') }}
                        {{--<a href="{{{ URL::route('memberAccount') }}}">Logged in as {{{ Auth::user()->username }}}</a>--}}
                        {{ HTML::menuItem('Logout', 'logout') }}
                    @else
                        {{ HTML::menuItem('Login', 'login') }}
                        {{ HTML::menuItem(Lang::get('site.sign_up'), 'register') }}
                    @endif
                </ul>
            </div><!-- ./ nav-collapse -->
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div id="logo-wrapper" class="col-xs-12">
            <div id="logo-box">
                <div class="logo-container">
                    <a href="{{{ URL::to('') }}}">National Calculator</a>
                </div>
                <div class="logo-bottom"></div>
            </div>
        </div>
        <div class="navbar navbar-default navbar-inverse col-xs-12" id="nav-main">
            <div class="container-fluid">

                <div class="navbar-header">
                    <button type="button" class="navbar-toggle left" data-toggle="collapse" data-target=".navbar-sections-collapse" style="">
                        <div>
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </div>
                        <a class="navbar-toggle-label" href="javascript:void(0)">{{ (Request::is('members') || Request::is('members/*')) ? "Members Nav" : "Menu" }}</a>
                    </button>
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <a class="navbar-toggle-label" href="javascript:void(0)">Account</a>
                        <div>
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </div>
                    </button>
                </div>

                <div class="collapse navbar-collapse navbar-sections-collapse">
                    @section('navtop_sub')
                    <ul class="nav navbar-nav">
                        {{ HTML::menuItem('Home', '/', ['is'=>['/','home']]) }}
                        {{ HTML::menuItem('About Us', '/about') }}
                        {{ HTML::menuItem('Calculators', '/calculators') }}
                        {{ HTML::menuItem('Membership', '/membership') }}
                        @if (!Auth::check())
                            {{ HTML::menuItem(Lang::get('site.sign_up'), 'register') }}
                        @endif
                        {{ HTML::menuItem('Contact Us', '/contact') }}
                    </ul>
                    @show
                </div>

            </div>
        </div>
    </div>
</div>