<?php

namespace App;

use App\Services\ConfigService;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'paypal_email', 'password', 'type', 
        'contact_info', 'photo', 'pay_rate',
        'country_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_admin', 'is_super', 'is_moderator', 'is_country_manager', 'is_team_leader',
        'is_mine', 'earnings', 'currency'
    ];

    public function getIsAdminAttribute()
    {
        return $this->type == 'admin';
    }

    public function getIsSuperAttribute()
    {
        return $this->type == 'super';
    }

    public function getIsModeratorAttribute()
    {
        return $this->type == 'moderator';
    }

    public function getIsCountryManagerAttribute()
    {
        return $this->type == 'country_manager';
    }

    public function getIsTeamLeaderAttribute()
    {
        return $this->type == 'team_leader';
    }

    public function hasRole($role){
        return $this->type == $role ? true : false;
    }

    public function hasAnyRole($roles){
        foreach($roles as $role){
            if($this->hasRole($role))
                return true;
        }
        return false;
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }


    public function getIsMineAttribute()
    {
        return $this->id == auth()->user()->id;
    }

    public function managed_websites()
    {
        return $this->belongsToMany(Website::class, 'user_managed_websites')->withTimestamps();
    }

    public function sent_messages()
    {
        return $this->hasMany(UserSentMessage::class);
    }

    public function active_conversation()
    {
        return $this->hasOne(ActiveConversation::class);
    }

    public function affiliates()
    {
        return $this->hasMany(Affiliate\User::class);
    }

    public function getEarningsAttribute()
    {
        $earnings = 0;
        $sent = $this->sent_messages()->has('replies')->get();
        foreach ($sent as $message) {
            $earnings = $earnings + $message->replies()->unpaid()->get()->count() * $this->pay_rate;
        }
        return $earnings;
    }

    public function getCurrencyAttribute()
    {
        $config = new ConfigService;
        return $config->getConfigValue('currency');
    }
}
