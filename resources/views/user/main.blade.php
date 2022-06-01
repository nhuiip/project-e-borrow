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
					<select name="facultyId" id="facultyId" class="form-control form-control-border border-width-2"
						@if (!Auth::user()->isAdmin()) disabled @endif
						onchange="getDepartment(this)">
						@if (Auth::user()->isAdmin())
							<option value="">คณะ</option>
						@endif
						@foreach ($faculty as $key => $value)
							<option value="{{ $value->id }}">{{ $value->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-3">
					<select name="departmentId" id="departmentId" class="form-control form-control-border border-width-2"
						@if (!Auth::user()->isAdmin()) disabled @endif
						onchange="loadFilter(this)">
						@if (Auth::user()->isAdmin())
							<option value="">สาขาวิชา</option>
						@endif
						@if (!Auth::user()->isAdmin())
							@foreach ($department as $key => $value)
								<option value="{{ $value->id }}">{{ $value->name }}</option>
							@endforeach
						@endif
					</select>
				</div>
				<div class="col-md-2">
					<select name="roleId" id="roleId" class="form-control form-control-border border-width-2"
						onchange="loadFilter(this)">
						<option value="">บทบาทผู้ใช้</option>
						@foreach ($role as $key => $value)
							<option value="{{ $value->id }}">{{ $value->name_th }}</option>
						@endforeach
					</select>
				</div>
			</div>

		</div>
	</div>
	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table-hover table"
					id="data-table"
					width="100%"
					data-url="{{ route('user.jsontable') }}"
					data-create="{{ route('user.create') }}">
					<thead>
						<tr>
							<th>ลำดับ</th>
							<th>ชื่อ-นามสกุล</th>
							<th>อีเมล</th>
							<th>คณะ</th>
							<th>สาขาวิชา</th>
							<th>บทบาทผู้ใช้</th>
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
	  dom: "<'row'<'col-sm-6 col-md-6'Bl><'col-sm-6 col-md-6'f>>" +
	   "<'row'<'col-sm-12'tr>>" +
	   "<'row'<'col-sm-6 col-md-6'i><'col-sm-6 col-md-6'p>>",
	  buttons: [{
	   text: '<i class="fa fa-plus"></i>&nbsp;&nbsp;เพิ่มข้อมูล',
	   className: 'btn-flat btn-outline-success',
	   init: function(api, node, config) {
	    $(node).removeClass('btn-secondary')
	   },
	   action: function(e, dt, node, config) {
	    location.href = $('#data-table').attr('data-create');
	   }
	  }],
	  ajax: {
	   url: $('#data-table').attr('data-url'),
	   type: "GET",
	   data: function(d) {
	    d.facultyId = $('#facultyId').val();
	    d.departmentId = $('#departmentId').val();
	    d.roleId = $('#roleId').val();
	   },
	  },
	  ordering: false,
	  columnDefs: [{
	    'targets': [0],
	    'width': '5%',
	    'className': 'text-center',
	   },
	   {
	    'targets': [1, 2],
	    'width': '10%',
	   },
	   {
	    'targets': [3, 4],
	   },
	   {
	    'targets': [5],
	    'width': '10%',
	    'className': 'text-center',
	   },
	   {
	    'targets': [6, 7],
	    'width': '10%',
	    'className': 'text-center',
	   },
	   {
	    'targets': [8],
	    'width': '5%',
	    'className': 'text-center',
	   }
	  ],
	  columns: [{
	    data: 'DT_RowIndex'
	   },
	   {
	    data: 'name'
	   },
	   {
	    data: 'email'
	   },
	   {
	    data: 'facultyId'
	   },
	   {
	    data: 'departmentId'
	   },
	   {
	    data: 'roleId'
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

     function getDepartment(e) {
	  $.ajax({
	   type: "GET",
	   url: '{!! route('department.getdepartment') !!}',
	   data: {
	    facultyId: $(e).val()
	   },
	   cache: false,
	   beforeSend: function() {
	    loadFilter()
	    $("#departmentId").html('<option value="">สาขาวิชา</option>');
	   },
	   success: function(response) {
	    if (response.length != 0) {
	     $.each(response, function(index, item) {
	      $("#departmentId").append(
	       '<option value="' + item.id + '">' + item.name + "</option>"
	      );
	     });
	    }
	   },
	  });
	 }

	 function fncAction(e) {
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
	    $('#' + form).submit();
	   }
	  }).catch(swal.noop);
	 }
	</script>
@endsection
