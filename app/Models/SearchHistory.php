<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    use HasFactory;

    protected $fillable = ['search_term', 'user_session_id']; // Define fillable columns

    protected $primaryKey = 'id'; // Define the primary key column name
}
