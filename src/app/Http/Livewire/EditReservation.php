<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Reservation;

class EditReservation extends Component
{
    public $reservation;
    public $reservation_date;
    public $reservation_time;
    public $number_of_people;

    public function mount($reservationId)
    {
        $this->reservation = Reservation::findOrFail($reservationId);

        // 予約情報をフォームにセット
        $this->reservation_date = $this->reservation->reservation_date;
        $this->reservation_time = \Carbon\Carbon::parse($this->reservation->reservation_time)->format('H:i');
        $this->number_of_people = $this->reservation->number_of_people;
    }

    public function render()
    {
        return view('livewire.edit-reservation');
    }
}
