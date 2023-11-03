<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = ['first_name', 'last_name','phone_number','application_text','google_sheet_url'];
}
