<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class DeliveryReport extends Model
{
    public function custody_car(){
      return $this->hasOne(CustodyCar::class, 'delivery_report_id', 'id');
    }
}
