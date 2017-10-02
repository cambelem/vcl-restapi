<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property mixed $imageid
 * @property int $userid
 * @property mixed $revision
 * @property \Carbon\Carbon $datecreated
 * @property boolean $deleted
 * @property \Carbon\Carbon $datedeleted
 * @property boolean $production
 * @property string $comments
 * @property string $imagename
 * @property boolean $autocaptured
 * @property-read Image $image
 * @property-read User $user
 * @property-read ClickThroughs[] $clickThroughs
 * @property-read Computer[] $computers
 * @property-read Connectmethodmap[] $connectmethodmaps
 * @property-read Image[] $images
 * @property-read Imagerevisioninfo $imagerevisioninfo
 * @property-read Openstackimagerevision $openstackimagerevision
 * @property-read Reservation[] $reservations
 * @property-read Semaphore[] $semaphores
 * @property-read Sublog[] $sublogs
 */
class ImageRevision extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'imagerevision';

    /**
     * @var array
     */
    protected $fillable = ['id', 'imageid', 'userid', 'revision', 'datecreated', 'deleted', 'datedeleted', 'production', 'comments', 'imagename', 'autocaptured'];

    /**
     * @var array
     */
    protected $dates = ['datecreated', 'datedeleted'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo('Image', 'imageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clickThroughs()
    {
        return $this->hasMany('ClickThroughs', 'imagerevisionid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function computers()
    {
        return $this->hasMany('Computer', 'imagerevisionid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function connectmethodmaps()
    {
        return $this->hasMany('Connectmethodmap', 'imagerevisionid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('Image', 'basedoffrevisionid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function imagerevisioninfo()
    {
        return $this->hasOne('Imagerevisioninfo', 'imagerevisionid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function openstackimagerevision()
    {
        return $this->hasOne('Openstackimagerevision', 'imagerevisionid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany('Reservation', 'imagerevisionid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function semaphores()
    {
        return $this->hasMany('Semaphore', 'imagerevisionid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sublogs()
    {
        return $this->hasMany('Sublog', 'imagerevisionid');
    }
}
