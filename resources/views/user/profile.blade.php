@extends('layouts.bo')
@if (!empty($breadcrumb))
	@section('title', $breadcrumb[count($breadcrumb) - 1]['name'])
	@section('breadcrumb')
		@include('layouts._breadcrumb', ['breadcrumb' => $breadcrumb])
	@endsection
@endif
@section('content')
	<div class="container-fluid">
		<div class="row mb-3">
			<blockquote class="blockquote m-0" style="width: 100%">
				<footer class="blockquote-footer">บทบาทผู้ใช้: {{ Auth::user()->roles[0]->name_th }}</footer>
				@role('Officer|Instructor|Student')
					<footer class="blockquote-footer">คณะ: {{ $user->department->faculty->name }}</footer>
					<footer class="blockquote-footer">สาขาวิชา: {{ $user->department->name }}</footer>
				@endrole
			</blockquote>
		</div>
	</div>
	<nav>
		<div class="nav nav-tabs nav-pills nav-fill" id="nav-tab" role="tablist">
			<a class="nav-link active" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab"
				aria-controls="nav-profile" aria-selected="true">ข้อมูลส่วนตัว</a>
			<a class="nav-link" id="nav-password-tab" data-toggle="tab" href="#nav-password" role="tab"
				aria-controls="nav-password" aria-selected="false">เปลี่ยนรหัสผ่าน</a>
		</div>
	</nav>
	<div class="tab-content" id="nav-tabContent">
		<div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
			{{ Form::model($user, ['novalidate', 'route' => ['user.update', $user->id], 'class' => $errors->any() ? 'was-validated form-horizontal' : 'needs-validation form-horizontal', 'id' => 'account-form', 'method' => 'put', 'files' => true]) }}
			<div class="card mt-3">
				<div class="card-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="row">
								@role('Officer|Instructor|Student')
									<div class="col-sm-12">
										<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>สาขาวิชา
											:</label>
										<div class="col-sm-12">
											<div class="form-group">
												@if (old('roleId') == 1)
													{{ Form::select('departmentId', $department, old('departmentId'), ['id' => 'departmentId', 'class' => 'form-control', 'disabled']) }}
												@else
													{{ Form::select('departmentId', $department, old('departmentId'), ['id' => 'departmentId', 'required', 'class' => 'form-control', 'disabled']) }}
												@endif
												@error('departmentId')
													<small class="text-danger">{{ $message }}</small>
												@enderror
											</div>
										</div>
									</div>
								@endrole
								<div class="col-md-6">
									<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>ชื่อ - สกุล :</label>
									<div class="col-sm-12">
										<div class="form-group">
											{{ Form::text('name', old('name'), ['class' => 'form-control', 'required', 'placeholder' => 'กรุณากรอกชื่อ']) }}
											@error('name')
												<small class="text-danger">{{ $message }}</small>
											@enderror
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>อีเมล
										:</label>
									<div class="col-sm-12">
										<div class="form-group">
											{{ Form::text('email', old('email'), ['class' => 'form-control', 'required', 'placeholder' => 'กรุณากรอกอีเมล']) }}
											@error('email')
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
								value="profile">บันทึกข้อมูล</button>
						</div>
					</div>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
		<div class="tab-pane fade" id="nav-password" role="tabpanel" aria-labelledby="nav-password-tab">
			{{ Form::model($user, ['novalidate', 'route' => ['user.update', $user->id], 'class' => $errors->any() ? 'was-validated form-horizontal' : 'needs-validation form-horizontal', 'id' => 'account-form', 'method' => 'put', 'files' => true]) }}
			<div class="card mt-3">
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
								value="profile change password">บันทึกข้อมูล</button>
						</div>
					</div>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
@endsection
