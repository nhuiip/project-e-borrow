@extends('layouts.bo')
@if (!empty($breadcrumb))
	@section('title', $breadcrumb[count($breadcrumb) - 1]['name'])
	@section('breadcrumb')
		@include('layouts.component.breadcrumb._breadcrumb', ['breadcrumb' => $breadcrumb])
	@endsection
@endif
@section('content')
	<input type="hidden" name="isAdmin" id="isAdmin" value="{{ Auth::user()->isAdmin() }}">
	@if (empty($user))
		{{ Form::open(['novalidate', 'route' => 'user.store', 'class' => $errors->any() ? 'was-validated form-horizontal' : 'needs-validation form-horizontal', 'id' => 'account-form', 'method' => 'post', 'files' => true]) }}
	@else
		{{ Form::model($user, ['novalidate', 'route' => ['user.update', $user->id], 'class' => $errors->any() ? 'was-validated form-horizontal' : 'needs-validation form-horizontal', 'id' => 'account-form', 'method' => 'put', 'files' => true]) }}
	@endif
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-sm-3">
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>ระดับผู้ใช้
						:</label>
					<div class="col-sm-12">
						<div class="form-group">
							{{ Form::select('roleId', $role, old('roleId'), ['id' => 'roleId', 'class' => 'form-control', 'required', 'onchange' => 'IsAdmin(this)']) }}
							@error('roleId')
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>
					</div>
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>สิทธิ์การเข้าถึง
						:
					</label>
					<div class="form-group col-sm-12">
						<div class="custom-control custom-checkbox mb-3">
							<input class="custom-control-input permission" type="checkbox" name=""
								id="permission_0"
								value="0" onclick="CheckAll(this)">
							<label for="permission_0" class="custom-control-label"
								style="font-weight:400">เลือกทั้งหมด</label>
						</div>
						<hr>
						@foreach ($permission as $key => $value)
							<div class="custom-control custom-checkbox">
								<input class="custom-control-input permission" type="checkbox" name="permission[]"
									id="permission_{{ $value->id }}"
									value="{{ $value->id }}" @if (!empty($user) && $user->can($value->name)) checked @endif>
								<label for="permission_{{ $value->id }}" class="custom-control-label"
									style="font-weight:400">{{ $value->name_th }}</label>
							</div>
						@endforeach

					</div>
				</div>
				<div class="col-sm-9">
					<div class="row">
						<div class="col-sm-12">
							<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>สาขาวิชา
								:</label>
							<div class="col-sm-12">
								<div class="form-group">
									@if (old('roleId') == 1)
										{{ Form::select('departmentId', $department, old('departmentId'), ['id' => 'departmentId', 'class' => 'form-control']) }}
									@else
										{{ Form::select('departmentId', $department, old('departmentId'), ['id' => 'departmentId', 'required', 'class' => 'form-control']) }}
									@endif
									@error('departmentId')
										<small class="text-danger">{{ $message }}</small>
									@enderror
								</div>
							</div>
						</div>
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
						@if (empty($user))
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
						@endif
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
						value="user form">บันทึกข้อมูล</button>
				</div>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
@endsection

@section('javascript')
	<script>
	 onloadPage();

	 function onloadPage() {
	  let roleId = $('#roleId').val()
	  if (roleId == 1) {
	   $(".permission").prop("checked", true);
	   $(".permission").prop("disabled", true);
	   $("#departmentId").prop("disabled", true);
	  }
	 }

	 function IsAdmin(e) {
	  if ($(e).val() == 1) {
	   $(".permission").prop("checked", true);
	   $(".permission").prop("disabled", true);
	   $("#departmentId").prop("disabled", true);
	  } else {
	   $(".permission").prop("checked", false);
	   $(".permission").prop("disabled", false);
	   $("#departmentId").prop("disabled", false);
	  }
	 }

	 function CheckAll(e) {
	  if (e.checked) {
	   $(".permission").prop("checked", true);
	  } else {
	   $(".permission").prop("checked", false);
	  }
	 }
	</script>
@endsection
