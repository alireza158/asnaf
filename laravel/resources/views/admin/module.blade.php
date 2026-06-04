@extends('layouts.admin')
@section('title', $title.' | پنل مدیریت')
@section('content')
@php($guide = adminModuleGuide($module))
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <span class="badge text-bg-secondary mb-2">{{ $meta['group'] ?? 'پنل مدیریت' }}</span>
            <h1 class="h3 fw-black mb-1">{{ $title }}</h1>
            <p class="text-muted mb-0">{{ $guide['intro'] }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary rounded-pill px-4" href="{{ route('admin.module.create', $module) }}">+ افزودن مورد جدید</a>
            <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('admin.dashboard') }}">بازگشت به شروع</a>
        </div>
    </div>

    @if(session('status'))<div class="alert alert-success rounded-4 shadow-sm">{{ session('status') }}</div>@endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-3">راهنمای خیلی ساده</h2>
            <div class="row g-3">
                @foreach($guide['steps'] as $step)
                    <div class="col-md-4"><div class="d-flex gap-3 align-items-start bg-light rounded-4 p-3 h-100"><span class="badge text-bg-primary rounded-pill">{{ \App\Support\JalaliDate::faNumber($loop->iteration) }}</span><p class="mb-0 small">{{ $step }}</p></div></div>
                @endforeach
            </div>
        </div>
    </div>

    @if($module === 'menus')
        <div class="row g-3 mb-4">
            @foreach(collect($rows)->groupBy('location') as $location => $menuRows)
                <div class="col-lg-4"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body">
                    <h2 class="h6 fw-bold mb-3">{{ adminFieldConfig('location')['options'][$location] ?? $location }}</h2>
                    <div class="list-group list-group-flush">
                        @foreach($menuRows as $menuRow)
                            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-start" href="{{ route('admin.module.edit', [$module, $menuRow['id']]) }}">
                                <span><strong>{{ $menuRow['title'] }}</strong><small class="d-block text-muted">{{ $menuRow['url'] }} @if($menuRow['parent_id']) · زیرمنو @endif</small></span>
                                <span class="badge {{ $menuRow['enabled'] ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $menuRow['enabled'] ? 'نمایش' : 'مخفی' }}</span>
                            </a>
                        @endforeach
                    </div>
                </div></div></div>
            @endforeach
        </div>
    @endif

    @if($module === 'guild_page_blocks')
        <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4">
            <strong>یادآوری:</strong> هر اتحادیه می‌تواند برای صفحه خودش بخش جدا داشته باشد؛ مثلا یک رکورد برای قوانین، یک رکورد برای نرخ‌نامه و یک رکورد برای گالری.
        </div>
        <div class="row g-3 mb-4">
            @foreach(collect($rows)->groupBy('guild_id') as $guildId => $blockRows)
                <div class="col-lg-6"><div class="card border-0 shadow-sm rounded-4 h-100"><div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                        <div><span class="badge text-bg-light mb-2">{{ $blockRows->first()['guild_title'] ?? ('اتحادیه #'.$guildId) }}</span><h2 class="h6 fw-bold mb-0">بخش‌های آماده صفحه اتحادیه</h2></div>
                        <span class="badge text-bg-primary">{{ \App\Support\JalaliDate::faNumber(count($blockRows)) }} بخش</span>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($blockRows->sortBy('sort_order') as $blockRow)
                            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-start" href="{{ route('admin.module.edit', [$module, $blockRow['id']]) }}">
                                <span><strong>{{ adminFieldConfig('block_type')['options'][$blockRow['block_type']] ?? $blockRow['block_type'] }}</strong><small class="d-block text-muted">{{ $blockRow['title'] }} · ترتیب {{ \App\Support\JalaliDate::faNumber($blockRow['sort_order'] ?? 0) }}</small></span>
                                <span class="badge {{ $blockRow['enabled'] ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $blockRow['enabled'] ? 'نمایش' : 'مخفی' }}</span>
                            </a>
                        @endforeach
                    </div>
                </div></div></div>
            @endforeach
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
            <div><h2 class="h5 fw-bold mb-1">لیست موارد ثبت‌شده</h2><p class="small text-muted mb-0">برای تغییر هر مورد روی دکمه «ویرایش» کلیک کنید.</p></div>
            <span class="badge text-bg-light">{{ \App\Support\JalaliDate::faNumber(count($rows)) }} مورد</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 admin-bootstrap-table">
                <thead class="table-light"><tr>@foreach($columns as $column)<th>{{ adminFieldConfig($column)['label'] ?? $labels[$column] ?? $column }}</th>@endforeach<th class="text-end">کارهای ساده</th></tr></thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            @foreach($columns as $column)
                                @php($fieldConfig = adminFieldConfig($column))
                                @php($cellValue = $row[$column] ?? null)
                                <td>@if(isset($fieldConfig['options'][$cellValue]))<span class="badge text-bg-info">{{ $fieldConfig['options'][$cellValue] }}</span>@elseif(is_bool($cellValue) || in_array($cellValue, [0, 1, '0', '1'], true) && in_array($column, ['enabled','active','important','complaints_enabled','sms_enabled','is_external'], true))<span class="badge {{ (bool) $cellValue ? 'text-bg-success' : 'text-bg-secondary' }}">{{ (bool) $cellValue ? 'بله' : 'خیر' }}</span>@else{{ is_scalar($cellValue) ? \Illuminate\Support\Str::limit((string) $cellValue, 90) : json_encode($cellValue ?? '', JSON_UNESCAPED_UNICODE) }}@endif</td>
                            @endforeach
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-primary" href="{{ route('admin.module.edit', [$module, $row['id']]) }}">ویرایش</a>
                                    <form method="post" action="{{ route('admin.module.destroy', [$module, $row['id']]) }}" onsubmit="return confirm('این مورد حذف شود؟');">@csrf @method('DELETE')<button class="btn btn-outline-danger" type="submit">حذف</button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($columns) + 1 }}" class="text-center text-muted py-5">هنوز چیزی ثبت نشده است. از دکمه «افزودن مورد جدید» شروع کنید.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
