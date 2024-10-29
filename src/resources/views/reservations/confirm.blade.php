@extends('layouts.app')

@section('content')
<div class="container">
    <h1>予約情報</h1>
    <p>予約番号: {{ $reservation->id }}</p>
    <p>レストラン: {{ $reservation->restaurant->restaurant_name }}</p>
    <p>予約日: {{ $reservation->reservation_date }}</p>
    <p>予約時間: {{ $reservation->reservation_time }}</p>
    <p>人数: {{ $reservation->number_of_people }}</p>
</div>
@endsection