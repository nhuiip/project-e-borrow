@extends('layouts.bo')
@if (!empty($breadcrumb))
	@section('title', $breadcrumb[count($breadcrumb) - 1]['name'])
	@section('breadcrumb')
		@include('layouts.component.breadcrumb._breadcrumb', ['breadcrumb' => $breadcrumb])
	@endsection
@endif
@section('content')
	{{ Form::model($user, ['novalidate', 'route' => ['user.update', $user->id], 'class' => $errors->any() ? 'was-validated form-horizontal' : 'needs-validation form-horizontal', 'id' => 'account-form', 'method' => 'put', 'files' => true]) }}
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-sm-12">
					<div class="row">
						<div class="col-md-6">
							<label class="col-sm-12 col-form-label"><span
									class="text-danger">*</span>รหัสผ่าน:</label>
							<div class="col-sm-12">
								<div class="form-group">
									{{ Form::password('password', ['class' => 'form-control password', 'required', 'placeholder' => 'รหัสผ่าน']) }}
									@error('password')
										<small class="text-danger">{{ $message }}</small>
									@enderror
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<label class="col-sm-12 col-form-label"><span
									class="text-danger">*</span>ยืนยันรหัสผ่าน:</label>
							<div class="col-sm-12">
								<div class="form-group">
									{{ Form::password('password_confirmation', ['class' => 'form-control password', 'required', 'placeholder' => 'ยืนยันรหัสผ่าน']) }}
									@error('password_confirmation')
										<small class="text-danger">{{ $message }}</small>
									@enderror
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<div class="row">
				<div class="col-6 col-md-6">
					<button type="reset" class="btn btn-secondary">ล้างข้อมูล</button>
				</div>
				<div class="col-6 col-md-6">
					<button type="submit" class="btn btn-success float-right" name="action"
						value="change password">บันทึกข้อมูล</button>
				</div>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
@endsection
