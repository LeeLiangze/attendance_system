<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

        static::creating(function ($arupian) {
            do {
                //generate a random string using Laravel's str_random helper
                $token = Str::Random(5) . date('jn');
            } //check if the token already exists and if it does, try again

            while (Arupian::where('reference', $token)->first());

            do {
                $token_private = Str::Random(15);
            }
            while (Arupian::where('private_reference', $token_private)->first());
            $arupian->reference = $token;
            $arupian->private_reference = $token_private;

        });
    }
}
