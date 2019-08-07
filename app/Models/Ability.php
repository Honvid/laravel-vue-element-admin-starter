<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Silber\Bouncer\Database\Concerns\IsAbility;
use Silber\Bouncer\Database\Models;

/**
 * @property integer $id
 * @property string  $name
 * @property string  $title
 * @property string  $group
 *
 */
class Ability extends Model
{
    use IsAbility;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'title', 'group'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'int',
        'only_owned' => 'boolean',
    ];

    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = Models::table('abilities');

        parent::__construct($attributes);
    }
}
