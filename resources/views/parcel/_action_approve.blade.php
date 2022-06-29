<a href="javascript:;" data-form="status-form-{{ $id }}" data-color="#28a745" data-label="อนุมัติ" onclick="fncAction(this)" class="btn btn-default btn-block">อนุมัติ</a>

<form id="status-form-{{ $id }}" method="post" action="{{ route('history.update', $id) }}">
	@csrf
	@method('PUT')
	<input type="hidden" id="action-{{ $id }}" name="action" value="approval parcel">
    <input type="hidden" id="approved_userId_{{ $id }}" name="approved_userId" value="{{ Auth::user()->id }}">
    <input type="hidden" id="approved_at_{{ $id }}" name="approved_at" value="{{ Carbon\Carbon::now(); }}">
    <input type="hidden" id="updated_at_{{ $id }}" name="updated_at" value="{{ Carbon\Carbon::now(); }}">
	<input type="hidden" id="statusId-input-{{ $id }}" name="statusId" value="{{ App\Models\HistoryStatus::Status_Approval }}">
</form>

