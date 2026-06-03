@extends('layouts.admin')
@section('title', $title.' | پنل مدیریت')
@section('content')
<header class="admin-header">
    <div><span>{{ $jalaliDate }}</span><h1>{{ $title }}</h1><p>رکوردها از دیتابیس خوانده می‌شوند و با دکمه ویرایش قابل تغییر هستند.</p></div>
    <a class="admin-primary" href="{{ route('admin.dashboard') }}">بازگشت به داشبورد</a>
</header>
@if(session('status'))<div class="tracking-box">{{ session('status') }}</div>@endif
<section class="admin-panel">
    <div class="content-table admin-data-table">
        @forelse($rows as $row)
            <div>
                @foreach($columns as $column)
                    <span><strong>{{ $labels[$column] ?? $column }}:</strong> {{ is_scalar($row[$column] ?? null) ? \Illuminate\Support\Str::limit((string) $row[$column], 80) : json_encode($row[$column] ?? '', JSON_UNESCAPED_UNICODE) }}</span>
                @endforeach
                <a class="admin-primary admin-small" href="{{ route('admin.module.edit', [$module, $row['id']]) }}">ویرایش</a>
            </div>
        @empty
            <div>هنوز رکوردی در این بخش ثبت نشده است.</div>
        @endforelse
    </div>
</section>
@endsection
