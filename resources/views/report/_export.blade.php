<table>
	<thead>
		<tr>
			<th>ลำดับ</th>
			<th>เลขที่อ้างอิง</th>
			<th>ประเภท</th>
			<th>สถานะ</th>
			<th>รายการ</th>
			<th>จำนวน</th>
			<th>คณะ</th>
			<th>สาขาวิชา</th>
			<th>ที่จัดเก็บ</th>
			<th>เบิกโดย</th>
			<th>วันที่เบิก</th>
			<th>อนุมัติโดย</th>
			<th>วันที่อนุมัติ</th>
			<th>รับคืนโดย</th>
			<th>วันที่รับคืน</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($data as $key => $value)
			<tr>
				<td>{{ $key + 1 }}</td>
				<td>
					@if ($value->typeId == App\Models\HistoryType::Type_DurableGoods)
						{{ $value->durable_good->reference }}
					@else
						{{ $value->parcel->reference }}
					@endif
				</td>
				<td>{{ $value->history_type->name }}</td>
				<td>{{ $value->history_status->name }}</td>
				<td>
					@if ($value->typeId == App\Models\HistoryType::Type_DurableGoods)
						{{ $value->durable_good->name }}
					@else
						{{ $value->parcel->name }}
					@endif
				</td>
				<td>{{ $value->unit }}</td>
				<td>
					@if ($value->typeId == App\Models\HistoryType::Type_DurableGoods)
						{{ $value->durable_good->department->faculty->name }}
					@else
						{{ $value->parcel->department->faculty->name }}
					@endif
				</td>
				<td>
					@if ($value->typeId == App\Models\HistoryType::Type_DurableGoods)
						{{ $value->durable_good->department->name }}
					@else
						{{ $value->parcel->department->name }}
					@endif
				</td>
				<td>
					@if ($value->typeId == App\Models\HistoryType::Type_DurableGoods)
						{{ $value->durable_good->location->name }}<br>
						คณะ: {{ $value->durable_good->department->faculty->name }}<br>
						สาขา: {{ $value->durable_good->department->name }}
					@else
						{{ $value->parcel->location->name }}<br>
						คณะ: {{ $value->parcel->department->faculty->name }}<br>
						สาขา: {{ $value->parcel->department->name }}
					@endif
				</td>
				<td>
					@php
						$user = App\Models\User::findOrFail($value->created_userId);
					@endphp
					{{ $user->name }}
				</td>
				<td>{{ date('d/m/Y h:i A', strtotime($value->created_at)) }}</td>
				<td>
					@if ($value->statusId == App\Models\HistoryStatus::Status_Approval)
						@php
							$user = App\Models\User::findOrFail($value->approved_userId);
						@endphp
						{{ $user->name }}
					@endif
				</td>
				<td>
					@if ($value->statusId == App\Models\HistoryStatus::Status_Approval)
						{{ date('d/m/Y h:i A', strtotime($value->approved_at)) }}
					@endif
				</td>
                <td>
					@if ($value->statusId == App\Models\HistoryStatus::Status_Returned)
						@php
							$user = App\Models\User::findOrFail($value->returned_userId);
						@endphp
						{{ $user->name }}
					@endif
				</td>
				<td>
					@if ($value->statusId == App\Models\HistoryStatus::Status_Returned)
						{{ date('d/m/Y h:i A', strtotime($value->returned_at)) }}
					@endif
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
