<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $affiliationid
 * @property int $uid
 * @property string $unityid
 * @property string $firstname
 * @property string $lastname
 * @property string $preferredname
 * @property string $email
 * @property boolean $emailnotices
 * @property boolean $IMtypeid
 * @property string $IMid
 * @property boolean $adminlevelid
 * @property mixed $width
 * @property mixed $height
 * @property boolean $bpp
 * @property mixed $audiomode
 * @property boolean $mapdrives
 * @property boolean $mapprinters
 * @property boolean $mapserial
 * @property mixed $rdpport
 * @property boolean $showallgroups
 * @property \Carbon\Carbon $lastupdated
 * @property boolean $validated
 * @property boolean $usepublickeys
 * @property string $sshpublickeys
 * @property-read Affiliation $affiliation
 * @property-read IMtype $iMtype
 * @property-read BlockRequest[] $blockRequests
 * @property-read Changelog[] $changelogs
 * @property-read ClickThroughs[] $clickThroughs
 * @property-read Computer[] $computers
 * @property-read Connectlog[] $connectlogs
 * @property-read Continuations[] $continuations
 * @property-read Image[] $images
 * @property-read Imagerevision[] $imagerevisions
 * @property-read Localauth $localauth
 * @property-read Log[] $logs
 * @property-read Managementnode[] $managementnodes
 * @property-read Oneclick[] $oneclicks
 * @property-read Querylog[] $querylogs
 * @property-read Request[] $requests
 * @property-read Reservationaccounts[] $reservationaccounts
 * @property-read Schedule[] $schedules
 * @property-read Serverprofile[] $serverprofiles
 * @property-read Shibauth[] $shibauths
 * @property-read Usergroup[] $usergroups
 * @property-read Usergroup[] $usergroups
 * @property-read Userpriv[] $userprivs
 */
class Vcluser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * @var array
     */
    protected $fillable = ['id', 'affiliationid', 'uid', 'unityid', 'firstname', 'lastname', 'preferredname', 'email', 'emailnotices', 'IMtypeid', 'IMid', 'adminlevelid', 'width', 'height', 'bpp', 'audiomode', 'mapdrives', 'mapprinters', 'mapserial', 'rdpport', 'showallgroups', 'lastupdated', 'validated', 'usepublickeys', 'sshpublickeys'];

    /**
     * @var array
     */
    protected $dates = ['lastupdated'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function affiliation()
    {
        return $this->belongsTo('App\Models\Affiliation', 'affiliationid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function iMtype()
    {
        return $this->belongsTo('IMtype', 'IMtypeid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blockRequests()
    {
        return $this->hasMany('BlockRequest', 'ownerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function changelogs()
    {
        return $this->hasMany('Changelog', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clickThroughs()
    {
        return $this->hasMany('ClickThroughs', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function computers()
    {
        return $this->hasMany('Computer', 'ownerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function connectlogs()
    {
        return $this->hasMany('Connectlog', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function continuations()
    {
        return $this->hasMany('Continuations', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('Image', 'ownerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function imagerevisions()
    {
        return $this->hasMany('App\Models\ImageRevision', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function localauth()
    {
        return $this->hasOne('Localauth', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany('Log', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function managementnodes()
    {
        return $this->hasMany('Managementnode', 'ownerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oneclicks()
    {
        return $this->hasMany('Oneclick', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function querylogs()
    {
        return $this->hasMany('Querylog', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests()
    {
        return $this->hasMany('Request', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservationaccounts()
    {
        return $this->hasMany('Reservationaccounts', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedules()
    {
        return $this->hasMany('Schedule', 'ownerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function serverprofiles()
    {
        return $this->hasMany('Serverprofile', 'ownerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shibauths()
    {
        return $this->hasMany('Shibauth', 'userid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usergroups()
    {
        return $this->hasMany('Usergroup', 'ownerid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function usergroups2()
    {
        return $this->belongsToMany('Usergroup', 'usergroupmembers', 'userid', 'usergroupid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userprivs()
    {
        return $this->hasMany('Userpriv', 'userid');
    }
}
