<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Carpeta extends Model
{
    use HasUuids;
    
    protected $fillable = ['name', 'parent_id', 'user_id', 'size'];
    
    /**
     * Get the user that owns the folder
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the parent folder
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Carpeta::class, 'parent_id');
    }
    
    /**
     * Get the child folders
     */
    public function children(): HasMany
    {
        return $this->hasMany(Carpeta::class, 'parent_id');
    }
    
    /**
     * Get the files in this folder
     */
    public function ficheros(): HasMany
    {
        return $this->hasMany(Fichero::class, 'carpeta_id');
    }
    
    /**
     * Get all files in this folder and subfolders
     */
    public function allFicheros()
    {
        $files = $this->ficheros;
        
        foreach ($this->children as $child) {
            $files = $files->merge($child->allFicheros());
        }
        
        return $files;
    }
    
    /**
     * Calculate the size of this folder (files + subfolders)
     */
    public function calculateSize(): int
    {
        $size = 0;
        
        // Add size of all files in this folder
        foreach ($this->ficheros as $file) {
            try {
                $size += Storage::disk('public')->size($file->path);
            } catch (\Exception $e) {
                // Log the error but continue processing
                \Log::warning("Unable to get size for file {$file->path}: {$e->getMessage()}");
            }
        }
        
        // Add size of all subfolders
        foreach ($this->children as $subfolder) {
            $size += $subfolder->size;
        }
        
        return $size;
    }
    
    /**
     * Update the size of this folder and all parent folders
     */
    public function updateSize(): void
    {
        $this->size = $this->calculateSize();
        $this->save();
        
        // Update parent folder size if exists
        if ($this->parent_id) {
            $this->parent->updateSize();
        }
    }
    
    /**
     * Format the size for display
     */
    public function formattedSize(): string
    {
        $size = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
}