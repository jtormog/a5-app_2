<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Fichero extends Model
{
    use HasUuids;
    
    protected $fillable = ['name', 'path', 'user_id', 'carpeta_id'];
    
    public function size(){
        try {
            $size = Storage::disk('public')->size($this->path);
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $power = $size > 0 ? floor(log($size, 1024)) : 0;
            return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
        } catch (\Exception $e) {
            return 'Size unavailable';
        }
    }
    
    /**
     * Get the user that owns the file
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the folder that contains this file
     */
    public function carpeta(): BelongsTo
    {
        return $this->belongsTo(Carpeta::class, 'carpeta_id');
    }
}
