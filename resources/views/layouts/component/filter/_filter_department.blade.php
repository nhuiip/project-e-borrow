<select name="departmentId" id="departmentId" class="form-control form-control-border border-width-2"
	@if (!Auth::user()->isAdmin()) disabled @endif
	onchange="loadFilter(this)">
	@if (Auth::user()->isAdmin())
		<option value="0">สาขาวิชา</option>
		@foreach ($department as $key => $value)
			<optgroup label="{{ $value->name }}">
				@foreach ($value->departments as $key => $item)
					<option value="{{ $item->id }}">{{ $item->name }}</option>
				@endforeach
			</optgroup>
		@endforeach
	@else
		@foreach ($department as $key => $value)
			<option value="{{ $value->id }}">{{ $value->name }}</option>
		@endforeach
	@endif
</select>
