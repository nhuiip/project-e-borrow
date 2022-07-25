@extends('layouts.bo')
@if (!empty($breadcrumb))
	@section('title', $breadcrumb[count($breadcrumb) - 1]['name'])
	@section('breadcrumb')
		@include('layouts.component.breadcrumb._breadcrumb', ['breadcrumb' => $breadcrumb])
	@endsection
@endif
@section('css')
	<!-- DataTables -->
	<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
	<link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('content')
	<div class="row">
		<div class="col-md-6">
			<div class="info-box h-100">
				<span class="info-box-icon bg-info" style="width: 30%"><i class="fas fa-boxes fa-2x"></i></span>
				<div class="info-box-content" style="justify-content:start">
					<span class="info-box-text" style="font-size: 1.5rem;">ครุภัณฑ์</span>
					<span class="info-box-number" style="font-size: 2rem;">{{ $durable_goods->count }} รายการ</span>
					<br>
					@foreach ($durable_goods->status as $key => $value)
						<div class="progress">
							<div class="progress-bar bg-info" style="width: {{ $value['preset'] }}%"></div>
						</div>
						<span class="progress-description">
							@if ($value['label'] ==
							    App\Models\DurableGoodsStatus::statuslabel(App\Models\DurableGoodsStatus::Pending_Approval))
								@can('approve return')
									{{ $value['label'] }}:
									<a href="{{ route('history.durablegood_approve') }}"><u>{{ $value['count'] }} รายการ</u></a>
								@endcan
								@cannot('approve return')
									{{ $value['label'] }}: {{ $value['count'] }} รายการ
								@endcannot
							@elseif ($value['label'] == App\Models\DurableGoodsStatus::statuslabel(App\Models\DurableGoodsStatus::Waiting_Return))
								@can('approve return')
									{{ $value['label'] }}:
									<a href="{{ route('history.durablegood_return') }}"><u>{{ $value['count'] }} รายการ</u></a>
								@endcan
								@cannot('approve return')
									{{ $value['label'] }}: {{ $value['count'] }} รายการ
								@endcannot
							@else
								{{ $value['label'] }}: {{ $value['count'] }} รายการ
							@endif

						</span>
					@endforeach
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="info-box h-100">
				<span class="info-box-icon bg-warning" style="width: 30%"><i class="fas fa-scroll fa-2x text-white"></i></span>
				<div class="info-box-content" style="justify-content:start">
					<span class="info-box-text" style="font-size: 1.5rem;"> พัสดุ</span>
					<span class="info-box-number" style="font-size: 2rem;">{{ $parcel->count }} รายการ</span>
					<br>
					@foreach ($parcel->status as $key => $value)
						<div class="progress">
							<div class="progress-bar bg-warning" style="width: {{ $value['preset'] }}%"></div>
						</div>
						<span class="progress-description">
							{{ $value['label'] }}: {{ $value['count'] }} รายการ
						</span>
					@endforeach
				</div>
			</div>
		</div>
		{{-- <div class="col-md-12">
			<div class="card mt-3">
				<div class="card-header">
					<div class="row">
						<div class="col-md-4">
							<h4 class="mt-1">ประวัติการทำรายการ</h4>
						</div>
						<div class="col-md-2">
							@include('layouts.component.filter._filter_department', ['department' => $department])
						</div>
						<div class="col-md-2">
                            @include('layouts.component.filter._filter_location', ['location' => $location])
						</div>
						<div class="col-md-2">
                             @include('layouts.component.filter._filter_type', ['type' => $type])
						</div>
						<div class="col-md-2">
                            @include('layouts.component.filter._filter_status', ['status' => $status])
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table-hover table-bordered table"
							id="data-table"
							width="100%"
							data-url="{{ route('history.jsontable') }}">
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
		</div> --}}
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
	  dom: "<'row'<'col-sm-6 col-md-6'l><'col-sm-6 col-md-6'>>" +
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
	    d.typeId = $('#typeId').val();
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
	 function loadFilter(e) {
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
