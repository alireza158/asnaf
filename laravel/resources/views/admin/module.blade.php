@extends('layouts.admin')
@section('title', $title.' | پنل مدیریت')
@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div><span class="badge text-bg-secondary mb-2">{{ $jalaliDate }}</span><h1 class="h3 fw-black mb-1">{{ $title }}</h1><p class="text-muted mb-0">نمایش مرتب رکوردهای دیتابیس با جدول Bootstrap و امکان ویرایش سریع.</p></div>
        <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('admin.dashboard') }}">بازگشت</a>
    </div>
    @if(session('status'))<div class="alert alert-success rounded-4 shadow-sm">{{ session('status') }}</div>@endif
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 admin-bootstrap-table">
                <thead class="table-light"><tr>@foreach($columns as $column)<th>{{ $labels[$column] ?? $column }}</th>@endforeach<th class="text-end">عملیات</th></tr></thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            @foreach($columns as $column)
                                <td>{{ is_scalar($row[$column] ?? null) ? \Illuminate\Support\Str::limit((string) $row[$column], 90) : json_encode($row[$column] ?? '', JSON_UNESCAPED_UNICODE) }}</td>
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
