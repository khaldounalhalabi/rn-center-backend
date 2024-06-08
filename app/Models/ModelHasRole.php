<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    public $timestamps = true;
    protected $table = 'model_has_roles';
    protected $guarded = ['id'];
    protected $fillable = ['roleable_id', 'roleable_type', 'role_id'];
}
