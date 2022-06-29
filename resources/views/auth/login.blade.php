@extends('layouts.app')

@section('content')
	<div class="login-logo">
		<a href="../../index2.html"><b>E-Borrow</b> <br>System Management</a>
	</div>
	<div class="card">
		<div class="card-body login-card-body">
			<form method="POST" action="{{ route('login') }}">
				@csrf
				<div class="input-group mb-3">
					<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
						value="{{ old('email') }}" required autocomplete="email" autofocus>
					<div class="input-group-append">
						<div class="input-group-text">
							<span class="fas fa-envelope"></span>
						</div>
					</div>
				</div>
				<div class="input-group mb-3">
					<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"
						required autocomplete="current-password">
					<div class="input-group-append">
						<div class="input-group-text">
							<span class="fas fa-lock"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-8">
						@error('email')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
						@error('password')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
					</div>
					<!-- /.col -->
					<div class="col-4">
						<button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
					</div>
					<!-- /.col -->
				</div>
			</form>
		</div>
		<!-- /.login-card-body -->
	</div>
@endsection
