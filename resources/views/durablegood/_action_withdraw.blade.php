<a href="javascript:;" class="btn btn-default btn-block" data-action="delete"
	data-form="withdraw-form-{{ $id }}"
	data-color="#007bff" data-label="เบิก" onclick="fncAction(this)">เบิก</a>
<form id="withdraw-form-{{ $id }}" method="post" action="{{ route('history.store', $id) }}">
	@csrf
	@method('POST')
	<input type="hidden" id="action-{{ $id }}" name="action" value="durablegood">
	<input type="hidden" id="durablegoodsId-{{ $id }}"name="durablegoodsId" value="{{ $id }}">
	<input type="hidden" id="typeId-input-{{ $id }}" name="typeId" value="{{ App\Models\HistoryType::Type_DurableGoods }}">
	<input type="hidden" id="statusId-input-{{ $id }}" name="statusId" value="{{ App\Models\HistoryStatus::Status_Pending_Approval }}">
	<input type="hidden" id="created_userId_{{ $id }}" name="created_userId" value="{{ Auth::user()->id }}">
	<input type="hidden" id="created_at_{{ $id }}" name="created_at" value="{{ Carbon\Carbon::now() }}">
	<input type="hidden" id="updated_at_{{ $id }}" name="updated_at" value="{{ Carbon\Carbon::now() }}">
</form>
