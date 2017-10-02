<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $id
 * @property int $ownerid
 * @property boolean $platformid
 * @property mixed $imagetypeid
 * @property mixed $imagemetaid
 * @property int $basedoffrevisionid
 * @property string $name
 * @property string $prettyname
 * @property boolean $OSid
 * @property int $minram
 * @property boolean $minprocnumber
 * @property mixed $minprocspeed
 * @property mixed $minnetwork
 * @property boolean $maxconcurrent
 * @property boolean $reloadtime
 * @property boolean $deleted
 * @property boolean $test
 * @property \Carbon\Carbon $lastupdate
 * @property boolean $forcheckout
 * @property mixed $maxinitialtime
 * @property mixed $project
 * @property mixed $size
 * @property mixed $architecture
 * @property string $description
 * @property string $usage
 * @property-read User $user
 * @property-read Platform $platform
 * @property-read OS $oS
 * @property-read Imagetype $imagetype
 * @property-read Imagemeta $imagemeta
 * @property-read Imagerevision $imagerevision
 * @property-read BlockComputers[] $blockComputers
 * @property-read BlockRequest[] $blockRequests
 * @property-read ClickThroughs[] $clickThroughs
 * @property-read Computer[] $computers
 * @property-read Computer[] $computers
 * @property-read Imagerevision[] $imagerevisions
 * @property-read Log[] $logs
 * @property-read Oneclick[] $oneclicks
 * @property-read Reservation[] $reservations
 * @property-read Semaphore[] $semaphores
 * @property-read Serverprofile[] $serverprofiles
 * @property-read Imagemeta[] $imagemetas
 * @property-read Sublog[] $sublogs
 * @property-read Vmprofile[] $vmprofiles
 */
class Image extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'image';

    /**
     * @var array
     */
    protected $fillable = ['id', 'ownerid', 'platformid', 'imagetypeid', 'imagemetaid', 'basedoffrevisionid', 'name', 'prettyname', 'OSid', 'minram', 'minprocnumber', 'minprocspeed', 'minnetwork', 'maxconcurrent', 'reloadtime', 'deleted', 'test', 'lastupdate', 'forcheckout', 'maxinitialtime', 'project', 'size', 'architecture', 'description', 'usage'];

    /**
     * @var array
     */
    protected $dates = ['lastupdate'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User', 'ownerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function platform()
    {
        return $this->belongsTo('Platform', 'platformid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oS()
    {
        return $this->belongsTo('OS', 'OSid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imagetype()
    {
        return $this->belongsTo('Imagetype', 'imagetypeid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imagemeta()
    {
        return $this->belongsTo('Imagemeta', 'imagemetaid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imagerevision()
    {
        return $this->belongsTo('Imagerevision', 'basedoffrevisionid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blockComputers()
    {
        return $this->hasMany('BlockComputers', 'imageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blockRequests()
    {
        return $this->hasMany('BlockRequest', 'imageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clickThroughs()
    {
        return $this->hasMany('ClickThroughs', 'imageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function computers()
    {
        return $this->hasMany('Computer', 'currentimageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function computers()
    {
        return $this->hasMany('Computer', 'nextimageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function imagerevisions()
    {
        return $this->hasMany('Imagerevision', 'imageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany('Log', 'imageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oneclicks()
    {
        return $this->hasMany('Oneclick', 'imageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany('Reservation', 'imageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function semaphores()
    {
        return $this->hasMany('Semaphore', 'imageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function serverprofiles()
    {
        return $this->hasMany('Serverprofile', 'imageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function imagemetas()
    {
        return $this->belongsToMany('Imagemeta', 'subimages', 'imageid', 'imagemetaid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sublogs()
    {
        return $this->hasMany('Sublog', 'imageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vmprofiles()
    {
        return $this->hasMany('Vmprofile', 'imageid');
    }
}
