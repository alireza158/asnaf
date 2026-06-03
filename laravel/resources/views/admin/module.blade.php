@extends('layouts.admin')
@section('title', $title.' | پنل مدیریت')
@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <span class="badge text-bg-secondary mb-2">{{ $meta['group'] ?? 'پنل مدیریت' }}</span>
            <h1 class="h3 fw-black mb-1">{{ $title }}</h1>
            <p class="text-muted mb-0">{{ $meta['description'] ?? 'این بخش برای مدیریت رکوردهای مرتبط طراحی شده است.' }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2"><a class="btn btn-primary rounded-pill px-4" href="{{ route('admin.module.create', $module) }}">افزودن {{ $title }}</a><a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('admin.dashboard') }}">بازگشت</a></div>
    </div>
    @if(session('status'))<div class="alert alert-success rounded-4 shadow-sm">{{ session('status') }}</div>@endif

    @if($module === 'menus')
        <div class="row g-3 mb-4">
            @foreach(collect($rows)->groupBy('location') as $location => $menuRows)
                <div class="col-lg-4"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body">
                    <h2 class="h6 fw-bold mb-3">{{ adminFieldConfig('location')['options'][$location] ?? $location }}</h2>
                    <div class="list-group list-group-flush">
                        @foreach($menuRows as $menuRow)
                            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-start" href="{{ route('admin.module.edit', [$module, $menuRow['id']]) }}">
                                <span><strong>{{ $menuRow['title'] }}</strong><small class="d-block text-muted">{{ $menuRow['url'] }} @if($menuRow['parent_id']) · زیرمنوی #{{ $menuRow['parent_id'] }} @endif</small></span>
                                <span class="badge {{ $menuRow['enabled'] ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $menuRow['enabled'] ? 'فعال' : 'غیرفعال' }}</span>
                            </a>
                        @endforeach
                    </div>
                </div></div></div>
            @endforeach
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 admin-bootstrap-table">
                <thead class="table-light"><tr>@foreach($columns as $column)<th>{{ adminFieldConfig($column)['label'] ?? $labels[$column] ?? $column }}</th>@endforeach<th class="text-end">عملیات</th></tr></thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            @foreach($columns as $column)
                                @php($fieldConfig = adminFieldConfig($column))
                                @php($cellValue = $row[$column] ?? null)
                                <td>@if(isset($fieldConfig['options'][$cellValue]))<span class="badge text-bg-info">{{ $fieldConfig['options'][$cellValue] }}</span>@elseif(is_bool($cellValue) || in_array($cellValue, [0, 1, '0', '1'], true) && in_array($column, ['enabled','active','important','complaints_enabled','sms_enabled','is_external'], true))<span class="badge {{ (bool) $cellValue ? 'text-bg-success' : 'text-bg-secondary' }}">{{ (bool) $cellValue ? 'بله' : 'خیر' }}</span>@else{{ is_scalar($cellValue) ? \Illuminate\Support\Str::limit((string) $cellValue, 90) : json_encode($cellValue ?? '', JSON_UNESCAPED_UNICODE) }}@endif</td>
                            @endforeach
                            <td class="text-end"><a class="btn btn-sm btn-primary rounded-pill px-3" href="{{ route('admin.module.edit', [$module, $row['id']]) }}">ویرایش</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($columns) + 1 }}" class="text-center text-muted py-5">هنوز رکوردی در این بخش ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
