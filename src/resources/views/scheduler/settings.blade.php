@extends('layouts.app')

@section('content')
<div class="container">
    <h1>タスクスケジューラーの設定</h1>
    <form action="{{ route('scheduler.saving') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="schedule_time">スケジュール時間</label>
            <input type="time" id="schedule_time" name="schedule_time" value="{{ $setting->schedule_time ?? '07:00' }}" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">保存</button>
    </form>

    @if (session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
    @endif
    <a href="/admin/shop-register">戻る</a>
</div>
@endsection