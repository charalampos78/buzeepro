@section('navtop_sub')
<ul class="nav navbar-nav">
    <li>{{ HTML::menuItem('Member Main', '/members') }}</li>
    <li>{{ HTML::menuItem('Subscription', '/members/subscribe') }}</li>
    <li>{{ HTML::menuItem('Calculator', '/members/calculator') }}</li>
    <li>{{ HTML::menuItem('Notebook', '/members/notebook') }}</li>
</ul>
@show