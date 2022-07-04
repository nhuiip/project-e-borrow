<select name="statusId" id="statusId" class="form-control form-control-border border-width-2"
	onchange="loadFilter(this)">
	<option value="0">สถานะ</option>
	@foreach ($status as $key => $value)
		<option value="{{ $value->id }}">{{ $value->name }}</option>
	@endforeach
</select>
