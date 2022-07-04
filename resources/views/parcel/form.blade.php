@extends('layouts.bo')
@if (!empty($breadcrumb))
	@section('title', $breadcrumb[count($breadcrumb) - 1]['name'])
	@section('breadcrumb')
		@include('layouts.component.breadcrumb._breadcrumb', ['breadcrumb' => $breadcrumb])
	@endsection
@endif
@section('content')
	@if (empty($parcel))
		{{ Form::open(['novalidate', 'route' => 'parcel.store', 'class' => $errors->any() ? 'was-validated form-horizontal' : 'needs-validation form-horizontal', 'id' => 'account-form', 'method' => 'post', 'files' => true]) }}
		{{ Form::hidden('statusId', App\Models\ParcelStatus::Active) }}
		{{ Form::hidden('created_userId', Auth::user()->id) }}
		{{ Form::hidden('updated_userId', Auth::user()->id) }}
	@else
		{{ Form::model($parcel, ['novalidate', 'route' => ['parcel.update', $parcel->id], 'class' => $errors->any() ? 'was-validated form-horizontal' : 'needs-validation form-horizontal', 'id' => 'account-form', 'method' => 'put', 'files' => true]) }}
		{{ Form::hidden('updated_userId', Auth::user()->id) }}
	@endif
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-sm-6">
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>เลขที่พัสดุ:</label>
					<div class="col-sm-12">
						<div class="form-group">
							{{ Form::text('reference', old('reference'), ['class' => 'form-control', 'required', 'placeholder' => 'กรุณากรอกเลขที่พัสดุ']) }}
							@error('reference')
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>ชื่อ:</label>
					<div class="col-sm-12">
						<div class="form-group">
							{{ Form::text('name', old('name'), ['class' => 'form-control', 'required', 'placeholder' => 'กรุณากรอกชื่อ']) }}
							@error('name')
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>สาขาวิชา
						:</label>
					<div class="col-sm-12">
						<div class="form-group">
							{{ Form::select('departmentId', $department, old('departmentId'), ['id' => 'departmentId', 'required', 'class' => 'form-control']) }}
							@error('departmentId')
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>สถานที่จัดเก็บ
						:</label>
					<div class="col-sm-12">
						<div class="form-group">
							{{ Form::select('locationId', $location, old('locationId'), ['id' => 'locationId', 'required', 'class' => 'form-control']) }}
							@error('locationId')
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>
					</div>
				</div>
				@if (empty($parcel))
					<div class="col-sm-3">
						<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>จำนวน:</label>
						<div class="col-sm-12">
							<div class="form-group">
								{{ Form::number('stock', old('stock'), ['class' => 'form-control', 'required', 'placeholder' => 'กรุณากรอกจำนวน']) }}
								@error('stock')
									<small class="text-danger">{{ $message }}</small>
								@enderror
							</div>
						</div>
					</div>
				@else
					<div class="col-sm-3">
						<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>จำนวน:</label>
						<div class="col-sm-12">
							<div class="form-group">
								{{ Form::number('stock', old('stock'), ['class' => 'form-control', 'disabled', 'placeholder' => 'กรุณากรอกจำนวน']) }}
								@error('stock')
									<small class="text-danger">{{ $message }}</small>
								@enderror
							</div>
						</div>
					</div>
				@endif
				<div class="col-sm-3">
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>หน่วยนับ:</label>
					<div class="col-sm-12">
						<div class="form-group">
							{{ Form::text('stock_unit', old('stock_unit'), ['class' => 'form-control', 'required', 'placeholder' => 'กรุณากรอกหน่วยนับ']) }}
							@error('stock_unit')
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>รูปภาพ
						:</label>
					<div class="col-sm-12">
						<div class="form-group">
							<input type="file" class="form-control" id="file_images" accept="image/*"
								name="file_images[]" multiple>
							@error('file_images')
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-6 col-sm-6">
						<button type="reset" class="btn btn-secondary">ล้างข้อมูล</button>
					</div>
					<div class="col-6 col-sm-6">
						<button type="submit" class="btn btn-success float-right" name="action"
							value="parcel form">บันทึกข้อมูล</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
	@if (!empty($image))
		<div class="row">
			@foreach ($image as $key => $img)
				<div class="col-sm-2">
					<div class="card">
						<div
							style="background-image: url({{ asset('storage/ParcelImage/' . $img->name) }});height: 200px;width: 100%;background-repeat: no-repeat;background-size: cover;">
						</div>
						<div class="card-body p-0">
							<a class="btn btn-danger btn-block" href="javascript:;"
								data-form="delete-form-{{ $img->id }}" data-color="#dc3545"
								data-label="ลบรูปภาพ" onclick="fncAction(this)"
								style="border-radius: 0">ลบรูปภาพ</a>
						</div>
					</div>
					<form id="delete-form-{{ $img->id }}" method="post"
						action="{{ route('parcelimage.destroy', $img->id) }}">
						@csrf
						@method('DELETE')
					</form>
				</div>
			@endforeach
		</div>
	@endif
@endsection

@section('javascript')
	<script>
	 function fncAction(e) {
	  let action = $(e).attr('data-action');
	  let input = $(e).attr('data-input');
	  let value = $(e).attr('data-value');
	  let form = $(e).attr('data-form')
	  let color = $(e).attr('data-color')
	  let label = $(e).attr('data-label')

	  Swal.fire({
	   title: 'คุณแน่ใจไหม?',
	   text: "เมื่อยืนยันแล้วคุณจะไม่สามารถเปลี่ยนกลับได้!",
	   icon: 'warning',
	   showCancelButton: true,
	   confirmButtonColor: color,
	   cancelButtonColor: '#6c757d',
	   confirmButtonText: label,
	   cancelButtonText: 'ยกเลิก'
	  }).then((result) => {
	   if (result.value) {
	    console.log(value);
	    switch (action) {
	     case "status":
	      $('#' + input).val(value);
	      $('#' + form).submit();
	      break;
	     default:
	      $('#' + form).submit();
	      break;
	    }
	   }
	  }).catch(swal.noop);
	 }
	</script>
@endsection
