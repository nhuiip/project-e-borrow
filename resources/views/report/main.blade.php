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
	<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
@endsection
@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="card mt-3">
				<div class="card-header">
					<div class="row">
						<div class="col-md-4">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">
										<i class="far fa-calendar-alt"></i>
									</span>
								</div>
								<input type="text" class="form-control float-right" id="filterDate" onchange="loadFilter(this)"
									value="">
							</div>
							<input type="hidden" name="startDate" id="startDate" value="{{ null }}">
							<input type="hidden" name="endDate" id="endDate" value="{{ null }}">
						</div>
						<div class="col-md-2"></div>
						<div class="col-md-2">
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
						<div class="col-md-2">
							<select name="locationId" id="locationId" class="form-control form-control-border border-width-2"
								onchange="loadFilter(this)">
								<option value="0">ที่จัดเก็บ</option>
								@foreach ($location as $key => $value)
									<option value="{{ $value->id }}">{{ $value->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-1">
							<select name="typeId" id="typeId" class="form-control form-control-border border-width-2"
								onchange="loadFilter(this)">
								<option value="0">ประเภท</option>
								@foreach ($type as $key => $value)
									<option value="{{ $value->id }}">{{ $value->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-1">
							<select name="statusId" id="statusId" class="form-control form-control-border border-width-2"
								onchange="loadFilter(this)">
								<option value="0">สถานะ</option>
								@foreach ($status as $key => $value)
									<option value="{{ $value->id }}">{{ $value->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table-hover table-bordered table"
							id="data-table"
							width="100%"
							data-url="{{ route('history.jsontable') }}"
							data-export="{{ route('report.export') }}">
							<thead>
								<tr>
									<th>เลขที่</th>
									<th>ประเภท</th>
									<th>สถานะ</th>
									<th>รายการ</th>
									<th>จำนวน</th>
									<th>สาขาวิชา</th>
									<th>ที่จัดเก็บ</th>
									<th>เบิก</th>
									<th>อนุมัติ</th>
									<th>รับคืน</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
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
	<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
	<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>

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
	  dom: "<'row'<'col-sm-6 col-md-6'Bl><'col-sm-6 col-md-6'>>" +
	   "<'row'<'col-sm-12'tr>>" +
	   "<'row'<'col-sm-6 col-md-6'i><'col-sm-6 col-md-6'p>>",
	  buttons: [{
	   text: '<i class="fas fa-file-export"></i>&nbsp;&nbsp;Export',
	   className: 'btn-flat btn-outline-secondary',
	   init: function(api, node, config) {
	    $(node).removeClass('btn-secondary')
	    $(node).prop('id', 'btnExport');
	    $(node).attr('data-export', $('#data-table').attr('data-export'));
	   },
	   action: function(e, dt, node, config) {
	    location.href = $('#btnExport').attr('data-export');
	   }
	  }, ],
	  ajax: {
	   url: $('#data-table').attr('data-url'),
	   type: "GET",
	   data: function(d) {
	    d.departmentId = $('#departmentId').val();
	    d.locationId = $('#locationId').val();
	    d.statusId = $('#statusId').val();
	    d.typeId = $('#typeId').val();
	    d.startDate = $('#startDate').val();
	    d.endDate = $('#endDate').val();
	   },
	  },
	  ordering: false,
	  columnDefs: [{
	    'targets': [0],
	    'width': '6%',
	   },
	   {
	    'targets': [4],
	    'width': '4%',
	    'className': 'text-center',
	   },
	   {
	    'targets': [1, 2],
	    'width': '5%',
	    'className': 'text-center',
	   }
	  ],
	  columns: [{
	    data: 'reference'
	   },
	   {
	    data: 'history_type.name'
	   },
	   {
	    data: 'history_status.name'
	   },
	   {
	    data: 'name'
	   },
	   {
	    data: 'unit'
	   },
	   {
	    data: 'department_info'
	   },
	   {
	    data: 'location_info'
	   },
	   {
	    data: 'created_at'
	   },
	   {
	    data: 'approved_at'
	   },
	   {
	    data: 'returned_at'
	   }
	  ]
	 });
	</script>

	<script>
	 $('#filterDate').daterangepicker()

	 function loadFilter(e) {
	  let departmentId = $("#departmentId").val();
	  let locationId = $("#locationId").val();
	  let statusId = $("#statusId").val();
	  let typeId = $("#typeId").val()
	  let startDate = null;
	  let endDate = null;

	  let daterang = $('#filterDate').val().split(' - ');
	  if (daterang.length != 0) {
	   startDate = moment(new Date(daterang[0])).format('YYYY-MM-DD');
	   endDate = moment(new Date(daterang[1])).format('YYYY-MM-DD');
	   if (startDate == endDate) {
	    endDate = null
	   }
	   $('#startDate').val(startDate);
	   $('#endDate').val(endDate);
	  }

	  let url = '{!! route('report.export', ['departmentId' => ':departmentId', 'locationId' => ':locationId', 'statusId' => ':statusId', 'typeId' => ':typeId', 'startDate' => ':startDate', 'endDate' => ':endDate']) !!}'
	  url = url.replace(':departmentId', departmentId != "" ? departmentId : null)
	  url = url.replace(':locationId', locationId != "" ? locationId : null)
	  url = url.replace(':statusId', statusId != "" ? statusId : null)
	  url = url.replace(':typeId', typeId != "" ? typeId : null)
	  url = url.replace(':startDate', startDate)
	  url = url.replace(':endDate', endDate)

	  $('#btnExport').attr('data-export', url)

	  DataTable.ajax.reload();
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
