@extends('layouts.bo')
@if (!empty($breadcrumb))
	@section('title', $breadcrumb[count($breadcrumb) - 1]['name'])
	@section('breadcrumb')
		@include('layouts._breadcrumb', ['breadcrumb' => $breadcrumb])
	@endsection
@endif
@section('content')
	{{ Form::open(['novalidate', 'route' => 'parcelstock.store', 'class' => $errors->any() ? 'was-validated form-horizontal' : 'needs-validation form-horizontal', 'id' => 'account-form', 'method' => 'post', 'files' => true]) }}
	{{ Form::hidden('statusId', App\Models\ParcelStatus::Active) }}
	{{ Form::hidden('created_userId', Auth::user()->id) }}
	{{ Form::hidden('updated_userId', Auth::user()->id) }}
	{!! Form::hidden('parcelId', $parcel->id) !!}
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-sm-6">
					<label class="col-sm-12 col-form-label"><span class="text-danger">*</span>ประเภทรายการ
						:</label>
					<div class="col-sm-12">
						<div class="form-group">
							{{ Form::select('typeId', $type, old('typeId'), ['id' => 'typeId', 'required', 'class' => 'form-control']) }}
							@error('typeId')
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>
					</div>
				</div>
				<div class="col-sm-6">
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
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-6 col-sm-6">
						<button type="reset" class="btn btn-secondary">ล้างข้อมูล</button>
					</div>
					<div class="col-6 col-sm-6">
						<button type="submit" class="btn btn-success float-right" name="action"
							value="parcelstock form">บันทึกข้อมูล</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
@endsection
