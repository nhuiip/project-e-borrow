@extends('layouts.bo')
@if (!empty($breadcrumb))
	@section('title', $breadcrumb[count($breadcrumb) - 1]['name'])
	@section('breadcrumb')
		@include('layouts.component.breadcrumb._breadcrumb', ['breadcrumb' => $breadcrumb])
	@endsection
@endif
@section('content')

	<div class="card">
		{{ Form::model($parcel, ['novalidate', 'route' => ['parcel.update', $parcel->id], 'class' => $errors->any() ? 'was-validated form-horizontal' : 'needs-validation form-horizontal', 'id' => 'account-form', 'method' => 'put', 'files' => true]) }}
		{{ Form::hidden('updated_userId', Auth::user()->id) }}
		<div class="card-body">
			<div class="row">
				<div class="col-sm-6">
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>เลขที่พัสดุ:</label>
					<div class="col-sm-12">
						<div class="form-group">
							{{ Form::text('reference', old('reference'), ['class' => 'form-control', 'disabled', 'placeholder' => 'กรุณากรอกเลขที่พัสดุ']) }}
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
							{{ Form::text('name', old('name'), ['class' => 'form-control', 'disabled', 'placeholder' => 'กรุณากรอกชื่อ']) }}
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
							{{ Form::select('departmentId', $department, old('departmentId'), ['id' => 'departmentId', 'disabled', 'class' => 'form-control']) }}
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
							{{ Form::select('locationId', $location, old('locationId'), ['id' => 'locationId', 'disabled', 'class' => 'form-control']) }}
							@error('locationId')
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>
					</div>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
		{{ Form::open(['novalidate', 'route' => 'history.store', 'class' => $errors->any() ? 'was-validated form-horizontal' : 'needs-validation form-horizontal', 'id' => 'account-form', 'method' => 'post', 'files' => true]) }}
		{{ Form::hidden('parcelId', $parcel->id) }}
		{{ Form::hidden('typeId', App\Models\HistoryType::Type_Parcel) }}
		{{ Form::hidden('statusId', App\Models\HistoryStatus::Status_Pending_Approval) }}
		{{ Form::hidden('created_userId', Auth::user()->id) }}
		<div class="card-body pt-0">
			<hr class="mt-0 mb-0">
			<div class="row">
				<div class="col-sm-3">
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>จำนวนคงเหลือ:</label>
					<div class="col-sm-12">
						<div class="form-group">
							{{ Form::number('stock', $parcel->stock, ['class' => 'form-control', 'disabled', 'placeholder' => 'กรุณากรอกจำนวน']) }}
						</div>
					</div>
				</div>

				<div class="col-sm-3">
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>หน่วยนับ:</label>
					<div class="col-sm-12">
						<div class="form-group">
							{{ Form::text('stock_unit', $parcel->stock_unit, ['class' => 'form-control', 'disabled', 'placeholder' => 'กรุณากรอกหน่วยนับ']) }}

						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>จำนวนที่ต้องการเบิก:</label>
					<div class="col-sm-12">
						<div class="form-group">
							{{ Form::number('unit', old('unit'), ['class' => 'form-control', 'required', 'placeholder' => 'กรุณากรอกจำนวน', 'max' => $parcel->stock]) }}
							@error('unit')
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>
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
						value="parcel">บันทึกข้อมูล</button>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
	</div>
@endsection
