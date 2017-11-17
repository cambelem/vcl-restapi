<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $id
 * @property boolean $checkuser
 * @property boolean $subimages
 * @property boolean $sysprep
 * @property string $postoption
 * @property string $architecture
 * @property boolean $rootaccess
 * @property boolean $sethostname
 * @property-read Image[] $images
 * @property-read Image[] $images
 */
class Imagemeta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'imagemeta';

    /**
     * @var array
     */
    protected $fillable = ['id', 'checkuser', 'subimages', 'sysprep', 'postoption', 'architecture', 'rootaccess', 'sethostname'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('Image', 'imagemetaid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subimages()
    {
        return $this->belongsToMany('Image', 'subimages', 'imagemetaid', 'imageid');
    }
}
