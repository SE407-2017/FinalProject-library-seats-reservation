<!DOCTYPE html>
<html lang="en" ng-app="app">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SJTU Library reservation dashboard">
    <meta name="author" content="Boar">
    <meta name="keyword" content="SJTU">
    <link rel="shortcut icon" href="img/favicon.png">

    <title>图书馆预约</title>

    <!-- Main styles for this application -->
    <link href="{{ url('reserve/css/style.css') }}" rel="stylesheet">
    <link href="{{ url('reserve/css/toastr.min.css') }}" rel="stylesheet">

</head>

<!-- BODY options, add following classes to body to change options

	// Header options
	1. '.header-fixed'					- Fixed Header

	// Sidebar options
	1. '.sidebar-fixed'					- Fixed Sidebar
	2. '.sidebar-hidden'				- Hidden Sidebar
	3. '.sidebar-off-canvas'		- Off Canvas Sidebar
  4. '.sidebar-minimized'			- Minimized Sidebar (Only icons)
  5. '.sidebar-compact'			  - Compact Sidebar

	// Aside options
	1. '.aside-menu-fixed'			- Fixed Aside Menu
	2. '.aside-menu-hidden'			- Hidden Aside Menu
	3. '.aside-menu-off-canvas'	- Off Canvas Aside Menu

  // Breadcrumb options
  1. '.breadcrumb-fixed'			- Fixed Breadcrumb

	// Footer options
	1. 'footer-fixed'						- Fixed footer

	-->

<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">

<!-- User Interface -->
<ui-view></ui-view>

<!-- Bootstrap and necessary plugins -->
<script src="{{ url('reserve/bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ url('reserve/bower_components/tether/dist/js/tether.min.js') }}"></script>
<script src="{{ url('reserve/js/popper.min.js') }}"></script>
<script src="{{ url('reserve/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<!-- AngularJS -->
<script src="{{ url('reserve/bower_components/angular/angular.min.js') }}"></script>

<!-- AngularJS plugins -->
<script src="{{ url('reserve/bower_components/angular-ui-router/release/angular-ui-router.min.js') }}"></script>
<script src="{{ url('reserve/bower_components/oclazyload/dist/ocLazyLoad.min.js') }}"></script>
<script src="{{ url('reserve/bower_components/angular-breadcrumb/dist/angular-breadcrumb.min.js') }}"></script>
<script src="{{ url('reserve/bower_components/angular-loading-bar/build/loading-bar.min.js') }}"></script>

<!-- AngularJS CoreUI App scripts -->

<script src="{{ url('reserve/js/app.js') }}"></script>

<script src="{{ url('reserve/js/routes.js') }}"></script>

<script src="{{ url('reserve/js/controllers.js') }}"></script>
<script src="{{ url('reserve/js/directives.js') }}"></script>
<script src="{{ url('reserve/js/toastr.min.js') }}"></script>

</body>

</html>
