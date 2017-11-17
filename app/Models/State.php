<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property boolean $id
 * @property string $name
 * @property-read Computer[] $computers
 * @property-read Managementnode[] $managementnodes
 * @property-read Request[] $requests
 * @property-read Request[] $requests
 */
class State extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'state';

    /**
     * @var array
     */
    protected $fillable = ['id', 'name'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function computers()
    {
        return $this->hasMany('Computer', 'stateid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function managementnodes()
    {
        return $this->hasMany('Managementnode', 'stateid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests()
    {
        return $this->hasMany('Request', 'stateid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests()
    {
        return $this->hasMany('Request', 'laststateid');
    }
}
