@extends('layouts.admin')
@section('title', $title.' | پنل مدیریت')
@section('content')
<header class="admin-header">
    <div><span>{{ $jalaliDate }}</span><h1>{{ $title }}</h1><p>فیلدها را تغییر دهید و ذخیره کنید تا در دیتابیس ثبت شود.</p></div>
    <a class="admin-primary" href="{{ route('admin.module', $module) }}">بازگشت به لیست</a>
</header>
<section class="admin-panel">
    <form class="admin-form admin-edit-form" method="post" action="{{ route('admin.module.update', [$module, $row['id']]) }}">@csrf @method('PUT')
        @foreach($editable as $field)
            <label>{{ $labels[$field] ?? $field }}
                @php($value = old($field, $row[$field] ?? ''))
                @if(\Illuminate\Support\Str::contains($field, ['content', 'summary', 'body', 'value', 'permissions', 'features', 'gallery', 'settings']))
                    <textarea name="{{ $field }}" rows="5">{{ $value }}</textarea>
                @elseif(\Illuminate\Support\Str::contains($field, ['enabled', 'active', 'important', 'complaints_enabled', 'sms_enabled', 'is_external']))
                    <select name="{{ $field }}"><option value="1" @selected((bool) $value)>فعال / بله</option><option value="0" @selected(! (bool) $value)>غیرفعال / خیر</option></select>
                @else
                    <input name="{{ $field }}" value="{{ $value }}">
                @endif
            </label>
        @endforeach
        <button class="admin-primary">ذخیره تغییرات</button>
    </form>
</section>
@endsection
