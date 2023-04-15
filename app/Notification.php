<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{
    public function powra(){
      return $this->hasOne(PowraReport::class, 'id', 'field_id');
    }
}
