<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arupian extends MyBaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array $fillable
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'group_id',
        'gender',
        'reference'
    ];

    /**
     * The group associated with the arupian.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            do {
                //generate a random string using Laravel's str_random helper
                $token = Str::Random(5) . date('jn');
            } //check if the token already exists and if it does, try again

            while (Order::where('order_reference', $token)->first());
            $order->order_reference = $token;

        });
    }
}
