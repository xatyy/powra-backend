<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class PowraReport extends Model
{
    public function user(){
      return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
