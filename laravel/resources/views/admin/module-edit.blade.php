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
        <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('admin.module', $module) }}">بازگشت به لیست</a>
    </div>

    <div class="row g-4">
        <div class="col-xl-4 order-xl-2">
            <div class="card border-0 shadow-sm rounded-4 sticky-xl-top" style="top:24px">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">قبل از ذخیره</h2>
                    <ol class="small text-muted pe-3 mb-4">
                        @foreach($guide['steps'] as $step)<li class="mb-2">{{ $step }}</li>@endforeach
                    </ol>
                    <div class="alert alert-warning border-0 rounded-4 small mb-0">اگر فیلدی را نمی‌شناسید، خالی بگذارید یا فقط متن‌های واضح را تغییر دهید. فیلدهای انتخابی مثل اتحادیه و نوع بخش را از لیست انتخاب کنید.</div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 order-xl-1">
            <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4 p-lg-5">
                <form method="post" action="{{ ! empty($isCreate) ? route('admin.module.store', $module) : route('admin.module.update', [$module, $row['id']]) }}" class="row g-3">@csrf @if(empty($isCreate)) @method('PUT') @endif
                    @foreach($editable as $field)
                        @php($value = old($field, $row[$field] ?? ''))
                        @php($fieldConfig = adminFieldConfig($field))
                        @php($isLong = \Illuminate\Support\Str::contains($field, ['content', 'summary', 'body', 'value', 'permissions', 'features', 'gallery', 'settings', 'items', 'subtitle']))
                        @if(is_string($value) && in_array($field, ['items', 'permissions', 'features', 'gallery', 'settings', 'value'], true))
                            @php($decodedValue = json_decode($value, true))
                            @if(json_last_error() === JSON_ERROR_NONE)
                                @php($value = json_encode($decodedValue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))
                            @endif
                        @endif
                        <div class="col-12 {{ $isLong ? '' : 'col-lg-6' }}">
                            <label class="form-label fw-bold">{{ $fieldConfig['label'] ?? $labels[$field] ?? $field }}</label>
                            @if($field === 'guild_id' && adminSafeHasTable('guilds'))
                                <select class="form-select rounded-3" name="{{ $field }}">
                                    <option value="">انتخاب اتحادیه...</option>
                                    @foreach(\Illuminate\Support\Facades\DB::table('guilds')->orderBy('title')->get(['id', 'title']) as $optionRow)
                                        <option value="{{ $optionRow->id }}" @selected((string) $value === (string) $optionRow->id)>{{ $optionRow->title }}</option>
                                    @endforeach
                                </select>
                            @elseif($field === 'category_id' && adminSafeHasTable('categories'))
                                <select class="form-select rounded-3" name="{{ $field }}">
                                    <option value="">انتخاب دسته‌بندی...</option>
                                    @foreach(\Illuminate\Support\Facades\DB::table('categories')->orderBy('title')->get(['id', 'title', 'type']) as $optionRow)
                                        <option value="{{ $optionRow->id }}" @selected((string) $value === (string) $optionRow->id)>{{ $optionRow->title }} - {{ $optionRow->type }}</option>
                                    @endforeach
                                </select>
                            @elseif($field === 'commission_id' && adminSafeHasTable('commissions'))
                                <select class="form-select rounded-3" name="{{ $field }}">
                                    <option value="">انتخاب کمیسیون...</option>
                                    @foreach(\Illuminate\Support\Facades\DB::table('commissions')->orderBy('title')->get(['id', 'title']) as $optionRow)
                                        <option value="{{ $optionRow->id }}" @selected((string) $value === (string) $optionRow->id)>{{ $optionRow->title }}</option>
                                    @endforeach
                                </select>
                            @elseif(isset($fieldConfig['options']))
                                <select class="form-select rounded-3" name="{{ $field }}">
                                    <option value="">انتخاب کنید...</option>
                                    @foreach($fieldConfig['options'] as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @elseif($isLong)
                                <textarea class="form-control rounded-3" name="{{ $field }}" rows="{{ in_array($field, ['items', 'content'], true) ? 9 : 5 }}" placeholder="{{ $fieldConfig['placeholder'] ?? '' }}">{{ $value }}</textarea>
                            @elseif(\Illuminate\Support\Str::contains($field, ['enabled', 'active', 'important', 'complaints_enabled', 'sms_enabled', 'is_external']))
                                <select class="form-select rounded-3" name="{{ $field }}"><option value="1" @selected((bool) $value)>بله / نمایش داده شود</option><option value="0" @selected(! (bool) $value)>خیر / نمایش داده نشود</option></select>
                            @else
                                <input class="form-control rounded-3" name="{{ $field }}" value="{{ $value }}" placeholder="{{ $fieldConfig['placeholder'] ?? '' }}">
                            @endif
                            @if(! empty($fieldConfig['help']))<div class="form-text lh-lg">{{ $fieldConfig['help'] }}</div>@endif
                        </div>
                    @endforeach
                    <div class="col-12 d-flex flex-wrap gap-2 pt-3"><button class="btn btn-primary rounded-pill px-5">{{ ! empty($isCreate) ? 'ثبت مورد جدید' : 'ذخیره تغییرات' }}</button><a class="btn btn-light rounded-pill px-4" href="{{ route('admin.module', $module) }}">انصراف</a></div>
                </form>
            </div></div>
        </div>
    </div>
</div>
@endsection
