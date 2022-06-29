@extends('layouts.bo')
@if (!empty($breadcrumb))
	@section('title', $breadcrumb[count($breadcrumb) - 1]['name'])
	@section('breadcrumb')
		@include('layouts._breadcrumb', ['breadcrumb' => $breadcrumb])
	@endsection
@endif
@section('css')
	<!-- DataTables -->
	<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection
@section('content')
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-md-3">
					<select name="departmentId" id="departmentId" class="form-control form-control-border border-width-2"
						@if (!Auth::user()->isAdmin()) disabled @endif
						onchange="loadFilter(this)">
						@if (Auth::user()->isAdmin())
							<option value="">สาขาวิชา</option>
							@foreach ($department as $key => $value)
								<optgroup label="{{ $value->name }}">
									@foreach ($value->departments as $key => $item)
										<option value="{{ $item->id }}">{{ $item->name }}</option>
									@endforeach
								</optgroup>
							@endforeach
						@endif
						@foreach ($department as $key => $value)
							<option value="{{ $value->id }}">{{ $value->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-3">
					<select name="locationId" id="locationId" class="form-control form-control-border border-width-2"
						onchange="loadFilter(this)">
						<option value="">ที่จัดเก็บ</option>
						@foreach ($location as $key => $value)
							<option value="{{ $value->id }}">{{ $value->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-3">
					<input type="hidden" name="statusId" id="statusId" value="{{ $status->id }}">
				</div>
			</div>
		</div>
	</div>
	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table-hover table-bordered table"
					id="data-table"
					width="100%"
					data-url="{{ route('parcel.jsontable_withdraw') }}">
					<thead>
						<tr>
							<th>ลำดับ</th>
							<th>รูปภาพ</th>
							<th>เลขที่พัสดุ</th>
							<th>รายการ</th>
							<th>จำนวน</th>
							<th>สาขาวิชา</th>
							<th>ที่จัดเก็บ</th>
							<th>สถานะ</th>
							<th>เพิ่มข้อมูล</th>
							<th>แก้ไขข้อมูล</th>
							<th class="text-center">จัดการ</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection

@section('javascript')
	<!-- DataTables  & Plugins -->
	<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
	<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
	<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
	<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
	<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
	<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
	<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
	<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
	<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
	<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
	<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

	<script>
	 var DataTable = $('#data-table').DataTable({
	  autoWidth: false,
	  responsive: true,
	  lengthChange: false,
	  processing: true,
	  serverSide: true,
	  destroy: true,
	  paging: true,
	  pageLength: 10,
	  language: {
	   search: 'ค้นหา',
	   processing: '<i class="fa fa-spinner fa-spin fa-lg"></i><span class="ml-2">กำลังโหลดข้อมูล...</span> ',
	   info: "แสดง หน้า _PAGE_ จาก _PAGES_",
	   infoEmpty: "",
	   zeroRecords: "ไม่พบข้อมูล",
	   infoFiltered: "(ค้นหา จาก _MAX_ รายการ)",
	   paginate: {
	    first: '<i class="fas fa-angle-double-left"></i>',
	    last: '<i class="fas fa-angle-double-right">',
	    next: '<i class="fas fa-angle-right"></i>',
	    previous: '<i class="fas fa-angle-left"></i>'
	   },
	  },
	  dom: "<'row'<'col-sm-6 col-md-6'l><'col-sm-6 col-md-6'f>>" +
	   "<'row'<'col-sm-12'tr>>" +
	   "<'row'<'col-sm-6 col-md-6'i><'col-sm-6 col-md-6'p>>",
	  buttons: [],
	  ajax: {
	   url: $('#data-table').attr('data-url'),
	   type: "GET",
	   data: function(d) {
	    d.departmentId = $('#departmentId').val();
	    d.locationId = $('#locationId').val();
	    d.statusId = $('#statusId').val();
	    d.listType = $('#listType').val();
	   },
	  },
	  ordering: false,
	  columnDefs: [{
	    'targets': [0],
	    'width': '5%',
	    'className': 'text-center',
	   },
       {
	    'targets': [1],
	    'width': '10%',
	    'className': 'text-center',
	   },
	   {
	    'targets': [2,3],
	   },
	   {
	    'targets': [4],
	    'width': '5%',
	   },
	   {
	    'targets': [5, 6,7],
	   },
	   {
	    'targets': [8,9],
	    'width': '10%',
	    'className': 'text-center',
	   },
	   {
	    'targets': [10],
	    'width': '5%',
	    'className': 'text-center',
	   }
	  ],
	  columns: [{
	    data: 'DT_RowIndex'
	   },
       {
	    data: 'image'
	   },
	   {
	    data: 'reference'
	   },
	   {
	    data: 'name'
	   },
	   {
	    data: 'stock'
	   },
	   {
	    data: 'department_info'
	   },
	   {
	    data: 'location_info'
	   },
	   {
	    data: 'parcel_status.name'
	   },
	   {
	    data: 'created_at'
	   },
	   {
	    data: 'updated_at'
	   },
	   {
	    data: 'actions'
	   }
	  ]
	 });
	</script>

	<script>
	 function loadFilter(e) {
	  DataTable.ajax.reload();
	 }

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
