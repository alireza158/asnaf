@extends('layouts.admin')
@section('title', $title.' | پنل مدیریت')
@section('content')
<header class="admin-header">
    <div><span>{{ $jalaliDate }}</span><h1>{{ $title }}</h1><p>این بخش مستقیماً از جدول دیتابیس خوانده می‌شود و برای توسعه CRUD کامل آماده است.</p></div>
    <a class="admin-primary" href="{{ route('admin.dashboard') }}">بازگشت به داشبورد</a>
</header>
<section class="admin-panel">
    <div class="content-table admin-data-table">
        @forelse($rows as $row)
            <div>
                @foreach($columns as $column)
                    <span><strong>{{ $labels[$column] ?? $column }}:</strong> {{ is_scalar($row[$column] ?? null) ? $row[$column] : json_encode($row[$column] ?? '', JSON_UNESCAPED_UNICODE) }}</span>
                @endforeach
            </div>
        @empty
            <div>هنوز رکوردی در این بخش ثبت نشده است.</div>
        @endforelse
    </div>
</section>
@endsection
