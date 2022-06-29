
<a href="javascript:;" data-form="status-form-{{ $id }}" data-color="#28a745" data-label="รับคืน" onclick="fncAction(this)" class="btn btn-default btn-block">รับคืน</a>

<form id="status-form-{{ $id }}" method="post" action="{{ route('history.update', $id) }}">
	@csrf
	@method('PUT')
	<input type="hidden" id="action-{{ $id }}" name="action" value="return durablegood">
    <input type="hidden" id="returned_userId_{{ $id }}" name="returned_userId" value="{{ Auth::user()->id }}">
    <input type="hidden" id="returned_at{{ $id }}" name="returned_at" value="{{ Carbon\Carbon::now(); }}">
    <input type="hidden" id="updated_at_{{ $id }}" name="updated_at" value="{{ Carbon\Carbon::now(); }}">
	<input type="hidden" id="statusId-input-{{ $id }}" name="statusId" value="{{ App\Models\HistoryStatus::Status_Returned }}">
</form>
