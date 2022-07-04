<img src="{{ $cover }}" alt="" class="img-responsive img-thumbnail" width="100%" data-toggle="modal"
	data-target="#example-{{ $id }}">

<div class="modal fade" id="example-{{ $id }}" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<div id="carousel-image-{{$id}}" class="carousel slide" data-ride="carousel">
					<ol class="carousel-indicators">
						@foreach ($image as $key => $value)
							<li data-target="#carousel-image-{{$id}}" data-slide-to="{{ $key }}"
								@if ($key == 0) class="active" @endif></li>
						@endforeach
					</ol>
					<div class="carousel-inner">
						@foreach ($image as $key => $value)
							<div class="carousel-item @if ($key == 0) active @endif">
								<img class="d-block w-100" src="{{ asset('storage/ParcelImage/' . $value->name) }}"
									alt="{{ $value->name }}">
							</div>
						@endforeach
					</div>
					<a class="carousel-control-prev" href="#carousel-image-{{$id}}" role="button" data-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					</a>
					<a class="carousel-control-next" href="#carousel-image-{{$id}}" role="button" data-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
			</div>
		</div>
	</div>
</div>
