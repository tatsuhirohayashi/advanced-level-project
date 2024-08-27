<html>

<head>
    <title>予約情報のおしらせ</title>
</head>

<body>
    <p>{{ $reservation->user->name }}さん、</p>
    <p>予約情報のおしらせです。</p>
    <p>予約日時: {{ $reservation->reservation_date }} {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i') }}</p>
    <p>予約人数：{{ $reservation->number_of_people }}</p>
    <p>飲食店: {{ $reservation->restaurant->restaurant_name }}</p>
</body>

</html>