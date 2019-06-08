<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'certificates';

    protected $fillable = ['title','background','content'];

    public function students(){
    	return $this->belongsToMany('App\Student','certificate_students');
    }
}
