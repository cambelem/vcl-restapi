<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $requestid
 * @property mixed $managementnodeid
 * @property mixed $computerid
 * @property int $imagerevisionid
 * @property mixed $imageid
 * @property string $remoteIP
 * @property \Carbon\Carbon $lastcheck
 * @property string $pw
 * @property string $connectIP
 * @property mixed $connectport
 * @property-read Request $request
 * @property-read Managementnode $managementnode
 * @property-read Computer $computer
 * @property-read Imagerevision $imagerevision
 * @property-read Image $image
 * @property-read Computerloadlog[] $computerloadlogs
 * @property-read Natport[] $natports
 * @property-read Reservationaccounts[] $reservationaccounts
 * @property-read Vcldsemaphore[] $vcldsemaphores
 */
class Reservation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reservation';

    /**
     * @var array
     */
    protected $fillable = ['id', 'requestid', 'managementnodeid', 'computerid', 'imagerevisionid', 'imageid', 'remoteIP', 'lastcheck', 'pw', 'connectIP', 'connectport'];

    /**
     * @var array
     */
    protected $dates = ['lastcheck'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function request()
    {
        return $this->belongsTo('Request', 'requestid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function managementnode()
    {
        return $this->belongsTo('Managementnode', 'managementnodeid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function computer()
    {
        return $this->belongsTo('Computer', 'computerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imagerevision()
    {
        return $this->belongsTo('Imagerevision', 'imagerevisionid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo('Image', 'imageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function computerloadlogs()
    {
        return $this->hasMany('Computerloadlog', 'reservationid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function natports()
    {
        return $this->hasMany('Natport', 'reservationid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservationaccounts()
    {
        return $this->hasMany('Reservationaccounts', 'reservationid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vcldsemaphores()
    {
        return $this->hasMany('Vcldsemaphore', 'reservationid');
    }
}
