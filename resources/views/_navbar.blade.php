<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('mgmt.index') }}">Mgmt Admin</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            @if(!empty($user))
            <ul class="nav navbar-nav">
                <li><a href="{{ route('mgmt.index') }}">Home</a></li>
                @if($user->hasPermission('view_od'))
                    <li><a href="https://www.google.com" target="_blank">Order Direct</a></li>
                @endif
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        Manage Models <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('mgmt.index') }}/User">Users</a></li>
                        <li><a href="{{ route('mgmt.index') }}/Olorin-Auth-Role">Roles</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="navbar-text">
                    Hello, {{ $user->name }}
                </li>
                <li><a href="{{ route('auth.logout') }}">Logout</a></li>
            </ul>
            @endif
        </div>
    </div>
</nav>