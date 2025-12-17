<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AudioFile extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'file_path',       // Stores the public URL from Bytescale
        'bytescale_id',    // Optional: unique file ID from Bytescale
        'bytescale_path',  // Optional: path in Bytescale bucket (e.g., /audio/myfile.mp3)
    ];
}