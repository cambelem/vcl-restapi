<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $id
 * @property mixed $vmhostid
 * @property mixed $predictivemoduleid
 * @property int $ownerid
 * @property boolean $scheduleid
 * @property boolean $stateid
 * @property boolean $platformid
 * @property mixed $currentimageid
 * @property mixed $provisioningid
 * @property int $imagerevisionid
 * @property mixed $nextimageid
 * @property int $RAM
 * @property boolean $procnumber
 * @property mixed $procspeed
 * @property mixed $network
 * @property string $hostname
 * @property string $IPaddress
 * @property string $privateIPaddress
 * @property string $eth0macaddress
 * @property string $eth1macaddress
 * @property mixed $type
 * @property string $drivetype
 * @property boolean $deleted
 * @property \Carbon\Carbon $datedeleted
 * @property string $notes
 * @property \Carbon\Carbon $lastcheck
 * @property string $location
 * @property string $dsa
 * @property string $dsapub
 * @property string $rsa
 * @property string $rsapub
 * @property mixed $host
 * @property string $hostpub
 * @property boolean $vmtypeid
 * @property-read Vmhost $vmhost
 * @property-read Module $module
 * @property-read User $user
 * @property-read Schedule $schedule
 * @property-read State $state
 * @property-read Platform $platform
 * @property-read Image $image
 * @property-read Provisioning $provisioning
 * @property-read Imagerevision $imagerevision
 * @property-read Image $image
 * @property-read BlockComputers[] $blockComputers
 * @property-read Changelog[] $changelogs
 * @property-read Computerloadlog[] $computerloadlogs
 * @property-read Log[] $logs
 * @property-read Nathost[] $nathosts
 * @property-read Openstackcomputermap $openstackcomputermap
 * @property-read Reservation[] $reservations
 * @property-read Semaphore[] $semaphores
 * @property-read Sublog[] $sublogs
 * @property-read Sublog[] $sublogs
 * @property-read Vmhost[] $vmhosts
 */
class Computer extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'computer';

    /**
     * @var array
     */
    protected $fillable = ['id', 'vmhostid', 'predictivemoduleid', 'ownerid', 'scheduleid', 'stateid', 'platformid', 'currentimageid', 'provisioningid', 'imagerevisionid', 'nextimageid', 'RAM', 'procnumber', 'procspeed', 'network', 'hostname', 'IPaddress', 'privateIPaddress', 'eth0macaddress', 'eth1macaddress', 'type', 'drivetype', 'deleted', 'datedeleted', 'notes', 'lastcheck', 'location', 'dsa', 'dsapub', 'rsa', 'rsapub', 'host', 'hostpub', 'vmtypeid'];

    /**
     * @var array
     */
    protected $dates = ['datedeleted', 'lastcheck'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vmhost()
    {
        return $this->belongsTo('Vmhost', 'vmhostid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module()
    {
        return $this->belongsTo('Module', 'predictivemoduleid');
    }

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
    public function schedule()
    {
        return $this->belongsTo('Schedule', 'scheduleid');
    }

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
    public function platform()
    {
        return $this->belongsTo('Platform', 'platformid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo('Image', 'currentimageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provisioning()
    {
        return $this->belongsTo('Provisioning', 'provisioningid');
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
        return $this->belongsTo('Image', 'nextimageid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blockComputers()
    {
        return $this->hasMany('BlockComputers', 'computerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function changelogs()
    {
        return $this->hasMany('Changelog', 'computerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function computerloadlogs()
    {
        return $this->hasMany('Computerloadlog', 'computerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany('Log', 'computerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function nathosts()
    {
        return $this->belongsToMany('Nathost', 'nathostcomputermap', 'computerid', 'nathostid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function openstackcomputermap()
    {
        return $this->hasOne('Openstackcomputermap', 'computerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany('Reservation', 'computerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function semaphores()
    {
        return $this->hasMany('Semaphore', 'computerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sublogs()
    {
        return $this->hasMany('Sublog', 'computerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sublogs()
    {
        return $this->hasMany('Sublog', 'hostcomputerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vmhosts()
    {
        return $this->hasMany('Vmhost', 'computerid');
    }
}
