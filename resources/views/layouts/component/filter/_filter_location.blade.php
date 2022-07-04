<select name="locationId" id="locationId" class="form-control form-control-border border-width-2"
	onchange="loadFilter(this)">
	<option value="0">ที่จัดเก็บ</option>
	@foreach ($location as $key => $value)
		<option value="{{ $value->id }}">{{ $value->name }}</option>
	@endforeach
</select>
