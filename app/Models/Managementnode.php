<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $id
 * @property mixed $imagelibgroupid
 * @property int $ownerid
 * @property boolean $stateid
 * @property string $IPaddress
 * @property string $hostname
 * @property \Carbon\Carbon $lastcheckin
 * @property boolean $checkininterval
 * @property string $installpath
 * @property boolean $imagelibenable
 * @property string $imagelibuser
 * @property string $imagelibkey
 * @property string $keys
 * @property mixed $sshport
 * @property mixed $publicIPconfiguration
 * @property string $publicSubnetMask
 * @property string $publicDefaultGateway
 * @property string $publicDNSserver
 * @property string $sysadminEmailAddress
 * @property string $sharedMailBox
 * @property string $NOT_STANDALONE
 * @property string $availablenetworks
 * @property-read Resourcegroup $resourcegroup
 * @property-read User $user
 * @property-read State $state
 * @property-read BlockRequest[] $blockRequests
 * @property-read Reservation[] $reservations
 * @property-read Semaphore[] $semaphores
 * @property-read Sublog[] $sublogs
 */
class Managementnode extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'managementnode';

    /**
     * @var array
     */
    protected $fillable = ['id', 'imagelibgroupid', 'ownerid', 'stateid', 'IPaddress', 'hostname', 'lastcheckin', 'checkininterval', 'installpath', 'imagelibenable', 'imagelibuser', 'imagelibkey', 'keys', 'sshport', 'publicIPconfiguration', 'publicSubnetMask', 'publicDefaultGateway', 'publicDNSserver', 'sysadminEmailAddress', 'sharedMailBox', 'NOT_STANDALONE', 'availablenetworks'];

    /**
     * @var array
     */
    protected $dates = ['lastcheckin'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resourcegroup()
    {
        return $this->belongsTo('Resourcegroup', 'imagelibgroupid');
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
    public function state()
    {
        return $this->belongsTo('State', 'stateid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blockRequests()
    {
        return $this->hasMany('BlockRequest', 'managementnodeid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany('Reservation', 'managementnodeid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function semaphores()
    {
        return $this->hasMany('Semaphore', 'managementnodeid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sublogs()
    {
        return $this->hasMany('Sublog', 'managementnodeid');
    }
}
