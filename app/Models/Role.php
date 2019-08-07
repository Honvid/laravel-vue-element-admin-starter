<?php

namespace App\Models;

use Silber\Bouncer\Database\Concerns\IsRole;
use Silber\Bouncer\Database\Models;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use IsRole;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'title', 'level'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'entity_id' => 'int',
        'level' => 'int',
    ];

    /**
     * Constructor.
     *
     * @param array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = Models::table('roles');

        parent::__construct($attributes);
    }
}
