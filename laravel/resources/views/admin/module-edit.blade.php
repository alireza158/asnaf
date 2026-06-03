@extends('layouts.admin')
@section('title', $title.' | پنل مدیریت')
@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div><span class="badge text-bg-secondary mb-2">{{ $jalaliDate }}</span><h1 class="h3 fw-black mb-1">{{ $title }}</h1><p class="text-muted mb-0">فرم ساده Bootstrap برای تغییر رکوردهای داینامیک پنل.</p></div>
        <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('admin.module', $module) }}">بازگشت به لیست</a>
    </div>
    <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4 p-lg-5">
        <form method="post" action="{{ ! empty($isCreate) ? route('admin.module.store', $module) : route('admin.module.update', [$module, $row['id']]) }}" class="row g-3">@csrf @if(empty($isCreate)) @method('PUT') @endif
            @foreach($editable as $field)
                @php($value = old($field, $row[$field] ?? ''))
                <div class="col-12 {{ \Illuminate\Support\Str::contains($field, ['content', 'summary', 'body', 'value', 'permissions', 'features', 'gallery', 'settings']) ? '' : 'col-lg-6' }}">
                    <label class="form-label fw-bold">{{ $labels[$field] ?? $field }}</label>
                    @if(\Illuminate\Support\Str::contains($field, ['content', 'summary', 'body', 'value', 'permissions', 'features', 'gallery', 'settings']))
                        <textarea class="form-control rounded-3" name="{{ $field }}" rows="6">{{ $value }}</textarea>
                    @elseif(\Illuminate\Support\Str::contains($field, ['enabled', 'active', 'important', 'complaints_enabled', 'sms_enabled', 'is_external']))
                        <select class="form-select rounded-3" name="{{ $field }}"><option value="1" @selected((bool) $value)>فعال / بله</option><option value="0" @selected(! (bool) $value)>غیرفعال / خیر</option></select>
                    @else
                        <input class="form-control rounded-3" name="{{ $field }}" value="{{ $value }}">
                    @endif
                </div>
            @endforeach
            <div class="col-12"><button class="btn btn-primary rounded-pill px-5">{{ ! empty($isCreate) ? 'ثبت رکورد' : 'ذخیره تغییرات' }}</button></div>
        </form>
    </div></div>
</div>
@endsection
