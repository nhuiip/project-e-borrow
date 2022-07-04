@if (!empty($breadcrumb))
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{$breadcrumb[count($breadcrumb) - 1]['name']}}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    @foreach ($breadcrumb as $index => $item)
                    @if ($index !== count($breadcrumb) - 1)
                    <li class="breadcrumb-item"><a href="{{ $item['route'] }}">{{ $item['name'] }}</a></li>
                    @else
                    <li class="breadcrumb-item active">{{ $item['name'] }}</li>
                    @endif
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</section>
@endif
