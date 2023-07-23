<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoList extends Model
{
    use HasFactory;

    // $fillable = ['name'];
    // protected =$guarded = [];
    protected $fillable = ['name'];
}
