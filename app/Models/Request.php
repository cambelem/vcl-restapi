<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property boolean $stateid
 * @property boolean $laststateid
 * @property int $userid
 * @property int $logid
 * @property boolean $forimaging
 * @property boolean $test
 * @property boolean $preload
 * @property \Carbon\Carbon $start
 * @property \Carbon\Carbon $end
 * @property \Carbon\Carbon $daterequested
 * @property \Carbon\Carbon $datemodified
 * @property boolean $checkuser
 * @property-read State $state
 * @property-read State $state
 * @property-read User $user
 * @property-read Log $log
 * @property-read Reservation[] $reservations
 * @property-read Serverrequest $serverrequest
 */
class Request extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'request';

    /**
     * @var array
     */
    protected $fillable = ['id', 'stateid', 'laststateid', 'userid', 'logid', 'forimaging', 'test', 'preload', 'start', 'end', 'daterequested', 'datemodified', 'checkuser'];

    /**
     * @var array
     */
    protected $dates = ['start', 'end', 'daterequested', 'datemodified'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo('State', 'stateid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo('State', 'laststateid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function log()
    {
        return $this->belongsTo('Log', 'logid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany('Reservation', 'requestid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function serverrequest()
    {
        return $this->hasOne('Serverrequest', 'requestid');
    }
}
