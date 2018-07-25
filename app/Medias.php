<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Medias extends Model
{
    protected $table = 'medias';
    protected $fillable = ['name', 'media', 'mime_type', 'size'];
}
