<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $shibname
 * @property string $dataUpdateText
 * @property string $sitewwwaddress
 * @property string $helpaddress
 * @property boolean $shibonly
 * @property string $theme
 * @property-read Loginlog[] $loginlogs
 * @property-read Statgraphcache[] $statgraphcaches
 * @property-read User[] $users
 * @property-read Usergroup[] $usergroups
 * @property-read WinKMS[] $winKMSs
 * @property-read WinProductKey[] $winProductKeys
 */
class Affiliation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'affiliation';

    /**
     * @var array
     */
    protected $fillable = ['id', 'name', 'shibname', 'dataUpdateText', 'sitewwwaddress', 'helpaddress', 'shibonly', 'theme'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loginlogs()
    {
        return $this->hasMany('Loginlog', 'affiliationid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statgraphcaches()
    {
        return $this->hasMany('Statgraphcache', 'affiliationid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\Models\Vcluser', 'affiliationid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usergroups()
    {
        return $this->hasMany('Usergroup', 'affiliationid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function winKMSs()
    {
        return $this->hasMany('WinKMS', 'affiliationid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function winProductKeys()
    {
        return $this->hasMany('WinProductKey', 'affiliationid');
    }
}
