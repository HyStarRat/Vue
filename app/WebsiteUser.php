<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsiteUser extends Model
{
    protected $fillable = ['userId', 'website_id', 'fake_message', 'country_id'];

    public function user()
    {
        return $this->hasOne(Tenant\User::class, 'id', 'userId');
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }


}
