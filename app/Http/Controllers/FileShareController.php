<?php

namespace App\Http\Controllers;

use App\Models\Fichero;
use App\Models\Carpeta;
use App\Models\CarpetaCompartida;
use App\Models\FicheroCompartido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileShareController extends Controller
{
    public function share($type, $id)
    {
        $appUrl = config('app.url');
        $appUrl = rtrim($appUrl, '/');
        
        if ($type === 'file') {
            $file = Fichero::findOrFail($id);
            if (Auth::user()->id !== $file->user_id) {
                return response()->json(['error' => 'No tienes permiso para compartir este archivo'], 403);
            }
            $shareUrl = $appUrl . '/share-file/' . $file->id;
        } else if ($type === 'folder') {
            $folder = Carpeta::findOrFail($id);
            $shareUrl = $appUrl . '/share-folder/' . $folder->id;
        } else {
            return response()->json(['error' => 'Tipo inválido'], 400);
        }
        
        return response()->json(['share_url' => $shareUrl]);
    }

    public function accessFile(Fichero $file)
    {
        $user = Auth::user();
        
        if ($user && $file->user_id !== $user->id) {
            FicheroCompartido::firstOrCreate([
                'fichero_id' => $file->id,
                'user_id' => $user->id
            ]);
        }

        return redirect('/');
    }

    public function accessFolder(Carpeta $folder)
    {
        $user = Auth::user();
        
        if ($user && $folder->user_id !== $user->id) {
            // Create the main folder sharing record
            CarpetaCompartida::firstOrCreate([
                'carpeta_id' => $folder->id,
                'user_id' => $user->id
            ]);
            
            // Recursively share all subfolders
            $this->shareSubfolders($folder, $user->id);
            
            return redirect('/');
        }

        return redirect('/');
    }
    
    /**
     * Public method to synchronize shared folders and their contents
     */
    public function syncSharedFolders(Carpeta $parentFolder, $userId)
    {
        // Call the private method to handle the recursive sharing
        $this->shareSubfolders($parentFolder, $userId);
    }
    
    /**
     * Recursively share subfolders and their files with a user
     */
    private function shareSubfolders(Carpeta $parentFolder, $userId)
    {
        // Share all files in the current folder
        foreach ($parentFolder->ficheros as $file) {
            FicheroCompartido::firstOrCreate([
                'fichero_id' => $file->id,
                'user_id' => $userId
            ]);
        }
        
        foreach ($parentFolder->children as $subfolder) {
            // Share the subfolder
            CarpetaCompartida::firstOrCreate([
                'carpeta_id' => $subfolder->id,
                'user_id' => $userId
            ]);
            
            // Recursively process this subfolder's children
            $this->shareSubfolders($subfolder, $userId);
        }
    }
}