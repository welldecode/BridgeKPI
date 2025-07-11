<?php

namespace App\Livewire\Coupons;

use Livewire\Component;
use App\Models\Coupon;

class Index extends Component
{

    public $coupons;
    public $code, $discount, $type, $valid_from, $valid_to, $usage_limit;
    public $editId = null; 
 

    protected $rules = [
        'code' => 'required|unique:coupons,code',
        'discount' => 'required|numeric|min:0',
        'type' => 'required|in:fixed,percentage',
        'valid_from' => 'nullable|date',
        'valid_to' => 'nullable|date|after_or_equal:valid_from',
        'usage_limit' => 'nullable|integer|min:1',
    ];


    public function save()
    { 
        $this->validate();
    }

    public function edit($id)
    {

    } 
    public function update()
    {

    }

    public function delete($id)
    {

    } 

    public function mount() { 
        $this->coupons = Coupon::all();
    }

    public function render()
    {
        return view('livewire.coupons.index');
    }
}
