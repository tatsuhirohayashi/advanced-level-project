<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ReservationForm extends Component
{
    public $reservation_date;
    public $reservation_time;
    public $number_of_people;
    public $restaurant;

    public function render()
    {
        return view('livewire.reservation-form');
    }
}
