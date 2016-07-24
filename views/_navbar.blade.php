<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">AssettoStig.com</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="/mgmt/">Home</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        Manage Models <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/mgmt/User">Users</a></li>
                        <li><a href="/mgmt/Role">Roles</a></li>
                        <li><a href="/mgmt/Article">Articles</a></li>
                        <li><a href="/mgmt/ArticleCategory">Article Categories</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                @if($user = Auth::user())
                    <li class="navbar-text">
                        Hello, {{ $user->name }}
                    </li>
                    <li><a href="{{ route('auth.logout') }}">Logout</a></li>
                @else
                    <li><a href="{{ route('auth.login') }}">Login</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>