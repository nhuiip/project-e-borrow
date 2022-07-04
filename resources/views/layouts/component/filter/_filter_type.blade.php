<select name="typeId" id="typeId" class="form-control form-control-border border-width-2"
	onchange="loadFilter(this)">
	<option value="0">ประเภท</option>
	@foreach ($type as $key => $value)
		<option value="{{ $value->id }}">{{ $value->name }}</option>
	@endforeach
</select>
