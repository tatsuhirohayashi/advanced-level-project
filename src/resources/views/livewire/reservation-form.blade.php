<div>
    <form class="form-reservation" action="{{ route('restaurants.reserve', ['id' => $restaurant->id])}}" method="post">
        @csrf
        <div class="reserve__content-header">予約</div>
        <label for="reservation_date"></label>
        <input class="reserve__content-date" type="date" name="reservation_date" wire:model="reservation_date" id="reservation_date" min="{{ date('Y-m-d') }}">

        <label for="time-select"></label>
        <select id="time-select" name="reservation_time" wire:model="reservation_time">
            <option value="">-- Select Time --</option>
            @for ($hour = 0; $hour < 24; $hour++)
                @for ($minute=0; $minute < 60; $minute +=30)
                @php
                $time=sprintf('%02d:%02d', $hour, $minute);
                $current_time=now()->format('H:i');
                $today = now()->toDateString();
                @endphp
                @if ($reservation_date == $today)
                @if ($time >= $current_time)
                <option value="{{ $time }}">{{ $time }}</option>
                @endif
                @else
                <option value="{{ $time }}">{{ $time }}</option>
                @endif
                @endfor
                @endfor
        </select>

        <label for="person-select"></label>
        <select id="person-select" name="number_of_people" wire:model="number_of_people">
            <option value="">-- Select Number of People --</option>
            @for ($i = 1; $i <= 10; $i++) <option value="{{ $i }}">{{ $i }}人</option>
                @endfor
        </select>
        <div class="reserve__content-confirm">
            <div class="reserve__content-confirm-restaurant">
                <div class="reserve__content-confirm-restaurant-item">Shop</div>
                <div class="reserve__content-confirm-restaurant-name">{{ $restaurant['restaurant_name'] }}</div>
            </div>
            <div class="reserve__content-confirm-restaurant">
                <div class="reserve__content-confirm-reservation-item">Date</div>
                @if (isset($reservation_date))
                <div class="reserve__content-confirm-reservation-date">{{ $reservation_date }}</div>
                @endif
            </div>
            <div class="reserve__content-confirm-restaurant">
                <div class="reserve__content-confirm-reservation-item">Time</div>
                @if (isset($reservation_time))
                <div class="reserve__content-confirm-reservation-time">{{ $reservation_time }}</div>
                @endif
            </div>
            <div class="reserve__content-confirm-restaurant">
                <div class="reserve__content-confirm-reservation-item">Number</div>
                @if (isset($number_of_people))
                <div class="reserve__content-confirm-reservation-time">{{ $number_of_people }}人</div>
                @endif
            </div>
        </div>
        <div class="form__error">
            @error('reservation_date')
            {{ $message }}
            @enderror
        </div>
        <div class="form__error">
            @error('reservation_time')
            {{ $message }}
            @enderror
        </div>
        <div class="form__error">
            @error('number_of_people')
            {{ $message }}
            @enderror
        </div>
        @auth
        <div class="form__button">
            <button class="form__button-submit" type="submit">予約する</button>
        </div>
        @endauth
    </form>
</div>