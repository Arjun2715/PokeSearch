<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    use HasFactory;

    protected $fillable = ['search_term', 'user_session_id'];// fillable columnas buscar y userSession

    protected $primaryKey = 'id';  // primary key
}
