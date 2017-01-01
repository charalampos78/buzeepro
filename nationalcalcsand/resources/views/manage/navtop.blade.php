@section('inlineCSS')
	@parent
	  .navbar-collapse:before, .navbar-collapse:after{display:inline; content:"";}
@show

@section('navtop')

<div class="navbar navbar-default navbar-inverse navbar-static-top" id="nav-top">
    <div class="container">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-base-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{{ URL::to('') }}}"><img style="width:245px;" src="{{ asset('assets/media/logo.png') }}" title="{{{ Config::get('app.name') }}}" alt="{{{ Config::get('app.name') }}}"></a>
        </div>

		<div class="collapse navbar-collapse navbar-base-collapse">
            <ul class="nav navbar-nav pull-right nav-pills">
                @if (Auth::check())
					<li>{{ HTML::menuItem("Logged in as ".Auth::user()->username, '/members/account') }}</li>
					{{--<li><a href="{{{ URL::route('memberAccount') }}}">Logged in as {{{ Auth::user()->username }}}</a></li>--}}
					<li><a href="{{{ URL::route('logout') }}}">Logout</a></li>
                @else
					<li {{ (Request::is('login') ? ' class="active"' : '') }}><a href="{{{ URL::route('login') }}}">Login</a></li>
                @endif
            </ul>
            <!-- ./ nav-collapse -->
        </div>

    </div>
</div>

<div class="container">
	<div class="navbar navbar-default" id="nav-main">
		<div class="container-fluid">

			<div class="navbar-header">
				<button type="button" class="navbar-toggle left" data-toggle="collapse" data-target=".navbar-sections-collapse" style="margin-left:15px;">
					<div>
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</div>
					<a class="navbar-toggle-label" href="javascript:void(0)">Section Menu</a>
				</button>
			</div>

			<div class="collapse navbar-collapse navbar-sections-collapse">
				<ul class="nav navbar-nav">
					<li>{{ HTML::menuItem('Dashboard', '/manage', ['is'=>['manage','manage/dashboard']]) }}</li>
					<li>{{ HTML::menuItem('Content', '/manage/content') }}</li>
					{{--<li>{{ HTML::menuItem('Form', '/manage/form') }}</li>--}}
					<li>{{ HTML::menuItem('States', '/manage/data/states') }}</li>
					<li>{{ HTML::menuItem('Counties', '/manage/data/counties') }}</li>
					<li>{{ HTML::menuItem('Zips', '/manage/data/zips') }}</li>
					<li>{{ HTML::menuItem('Import', '/manage/import') }}</li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li class="divider"></li>
					{{ HTML::menuItem('Users', '/manage/user', ['is'=>'*']) }}
				</ul>
				<!-- ./ nav-collapse -->
			</div>

		</div>
	</div>
</div>
@show