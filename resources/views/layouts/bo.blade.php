<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>E-Borrow: @yield('title')</title>
	{{-- <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" /> --}}
	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet"
		href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
	<!-- Theme style -->
	<link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
	@yield('css')
	<style>
		.brand-link {
			font-size: 1rem;
			padding: 0.99rem 0.5rem;
		}

	</style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">

	<div id="app" class="wrapper">
		<!-- Navbar -->
		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<!-- Left navbar links -->
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link" data-toggle="dropdown" href="#">
						{{ Auth::user()->name }}
					</a>
					<div class="dropdown-menu">
						<a href="#" class="dropdown-item">
							<i class="fas fa-user mr-2"></i> ข้อมูลส่วนตัว
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" onclick="$('#logout-form').submit();" class="dropdown-item">

							<i class="fas fa-sign-out-alt mr-2"></i> ออกจากระบบ
						</a>
					</div>
					<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
						@csrf
					</form>
				</li>
			</ul>
		</nav>
		<!-- /.navbar -->
		<!-- Main Sidebar Container -->
		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<!-- Brand Logo -->
			<a href="#" class="brand-link">
				<img src="{{ asset('img/Logo-RMUTR-1-218x300.png') }}" alt="AdminLTE Logo"
					class="brand-image img-circle elevation-3" style="opacity: .8">
				<span class="brand-text font-weight-light">ระบบจัดการพัสดุ-ครุภัณฑ์</span>
			</a>
			<div class="sidebar">
				<!-- Sidebar Menu -->
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
						@can('view dashboard')
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="nav-icon fas fa-tachometer-alt"></i>
									<p>
										สรุปผลภาพรวม
									</p>
								</a>
							</li>
						@endcan
						@role('Admin')
							<li class="nav-item">
								<a href="{{ route('faculty.index') }}" class="nav-link {{ Request::routeIs('faculty.*') || Request::routeIs('department.*')  ? 'active' : '' }}">
									<i class="nav-icon fas fa-building"></i>
									<p>
										คณะ
									</p>
								</a>
							</li>
						@endrole
						@can('manage location')
							<li class="nav-item">
								<a href="{{ route('location.index') }}" class="nav-link {{ Request::routeIs('location.*') ? 'active' : '' }}">
									<i class="nav-icon fas fa-box"></i>
									<p>
										ที่จัดเก็บพัสดุ-ครุภัณฑ์
									</p>
								</a>
							</li>
						@endcan

						<li class="nav-item">
							<a href="#" class="nav-link">
								<i class="nav-icon fas fa-scroll"></i>
								<p>
									พัสดุ
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>
							<ul class="nav nav-treeview">
								@can('manage parcel')
									<li class="nav-item">
										<a href="#" class="nav-link">
											<i class="far fa-circle nav-icon"></i>
											<p>จัดการข้อมูลพัสดุ</p>
										</a>
									</li>
								@endcan
								<li class="nav-item">
									<a href="#" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>เบิกพัสดุ</p>
									</a>
								</li>
								@can('approve return')
									<li class="nav-item">
										<a href="#" class="nav-link">
											<i class="far fa-circle nav-icon"></i>
											<p>รายการรออนุมัติ</p>
										</a>
									</li>
								@endcan
							</ul>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link">
								<i class="nav-icon fas fa-boxes"></i>
								<p>
									ครุภัณฑ์
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>
							<ul class="nav nav-treeview">
								@can('manage durable goods')
									<li class="nav-item">
										<a href="#" class="nav-link">
											<i class="far fa-circle nav-icon"></i>
											<p>จัดการข้อมูลครุภัณฑ์</p>
										</a>
									</li>
								@endcan

								<li class="nav-item">
									<a href="#" class="nav-link">
										<i class="far fa-circle nav-icon"></i>
										<p>เบิกครุภัณฑ์</p>
									</a>
								</li>
								@can('approve return')
									<li class="nav-item">
										<a href="#" class="nav-link">
											<i class="far fa-circle nav-icon"></i>
											<p>รายการรออนุมัติ</p>
										</a>
									</li>
								@endcan
							</ul>
						</li>
						@can('view report')
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="nav-icon fas fa-file-excel"></i>
									<p>
										รายงาน
									</p>
								</a>
							</li>
						@endcan
						@can('manage user')
							<li class="nav-item">
								<a href="{{ route('user.index') }}" class="nav-link {{ Request::routeIs('user.*') ? 'active' : '' }}">
									<i class="nav-icon fas fa-user-cog"></i>
									<p>
										ข้อมูลผู้ใช้งาน
									</p>
								</a>
							</li>
						@endcan
					</ul>
				</nav>
			</div>
		</aside>
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			@yield('breadcrumb')
			<section class="content">
				@yield('content')
			</section>
		</div>
	</div>
	<!-- Scripts -->
	<script src="{{ asset('js/app.js') }}"></script>
	<!-- jQuery -->
	<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
	<!-- Bootstrap 4 -->
	<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

	<script>
	 $.ajaxSetup({
	  headers: {
	   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	 });
	</script>
	@yield('javascript')
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.5/dist/sweetalert2.all.min.js"></script>
    @include('sweetalert::alert', ['cdn' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11'])
	<!-- AdminLTE App -->
	<script src="{{ asset('js/adminlte.min.js') }}"></script>
    
	<!-- AdminLTE for demo purposes -->
	{{-- <script src="{{ asset('js/demo.js') }}"></script> --}}
</body>

</html>
