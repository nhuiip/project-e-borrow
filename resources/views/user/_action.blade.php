<div class="btn-group">
	<button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown"
		aria-expanded="false">
		<span class="sr-only">Toggle Dropdown</span>
	</button>
	<div class="dropdown-menu" role="menu" style="">
		<a class="dropdown-item" href="{{ route('user.edit', $id) }}">แก้ไขข้อมูล</a>
		<a class="dropdown-item" href="{{ route('user.password', $id) }}">เปลี่ยนรหัสผ่าน</a>
		<div class="dropdown-divider"></div>
		<a class="dropdown-item text-danger" href="javascript:;" data-form="delete-form-{{ $id }}"
			data-color="#dc3545" data-label="ลบข้อมูล" onclick="fncAction(this)">ลบข้อมูล</a>
	</div>
</div>
<form id="delete-form-{{ $id }}" method="post" action="{{ route('user.destroy', $id) }}">
	@csrf
	@method('DELETE')
</form>
