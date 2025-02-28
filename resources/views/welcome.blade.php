@include('head')
@include('navbar')
@auth
        <div class="flex justify-center gap-4 m-8">
            <form method="POST" action="/folder/create" class="text-white">
                @csrf
                <div class="flex flex-col gap-2">
                    <div class="flex gap-2">
                        <input type="text" name="folder_name" placeholder="Nombre de la carpeta" required minlength="3" maxlength="255" class="border border-gray-300 p-2 rounded text-black focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <input type="hidden" name="parent_id" value="{{ request('folder') }}">
                        <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Crear Carpeta</button>
                    </div>
                    @error('folder_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </form>
        </div>

        @if (request('folder'))
            <div class="flex items-center justify-center mb-4">
                <a href="{{ request('folder') ? '/folder/' . (\App\Models\Carpeta::find(request('folder'))->parent_id ?? '') : '/' }}" class="text-orange-500 hover:text-orange-700">
                    <span class="material-symbols-outlined">arrow_back</span> Volver
                </a>
            </div>
        @endif

        <h2 class="text-orange-500 text-2xl font-bold text-center m-5">{{ request('folder') ? \App\Models\Carpeta::find(request('folder'))->name : 'Mis Archivos' }}</h2>
        
        <!-- Sección de Carpetas -->
        @php
    $currentParentId = request('folder');
    $userFolders = Auth::user()->carpetas->where('parent_id', $currentParentId);
    $sharedFolders = Auth::user()->carpetasCompartidas
        ->map(function($shared) { return $shared->carpeta; })
        ->filter(function($folder) use ($currentParentId) { 
            return $folder && $folder->parent_id == $currentParentId;
        });
    $allFolders = $userFolders->concat($sharedFolders);
    
    // Define $allFiles variable
    $userFiles = Auth::user()->ficheros->where('carpeta_id', $currentParentId);
    $sharedFiles = Auth::user()->ficherosCompartidos
        ->map(function($shared) { return $shared->fichero; })
        ->filter(function($file) use ($currentParentId) { 
            return $file && $file->carpeta_id == $currentParentId;
        });
    $allFiles = $userFiles->concat($sharedFiles);
@endphp
@if (!$allFolders->isEmpty())
        <h3 class="text-orange-400 text-xl font-semibold text-center m-3">Carpetas</h3>
        <table class="min-w-full bg-gray-800 text-white mb-8">
            <tr class="bg-gray-700">
                <th class="py-2 px-4"></th>
                <th class="py-2 px-4">Name</th>
                <th class="py-2 px-4">Size</th>
                <th class="py-2 px-4">Owner</th>
                <th class="py-2 px-4">Created at</th>
                <th class="py-2 px-4">Updated at</th>
            </tr>
        @foreach ($allFolders as $carpeta)
            <tr class="bg-gray-600">
                <td class="py-2 px-4">
                    <div class="flex gap-2">
                        <a class="text-white" href="/delete-folder/{{ $carpeta->id }}"><span
                                class="material-symbols-outlined">delete</span></a>
                        @if(Auth::user()->id === $carpeta->user_id)
                        <a class="text-white cursor-pointer" onclick="shareFolder('{{ $carpeta->id }}')"><span
                                class="material-symbols-outlined">share</span></a>
                        @endif
                    </div>
                </td>
                <td class="py-2 px-4 text-center">
                    <a class="text-orange-400 hover:underline" href="/folder/{{ $carpeta->id }}">
                        <span class="material-symbols-outlined align-middle">folder</span> {{ $carpeta->name }}
                    </a>
                </td>
                <td class="py-2 px-4 text-center">-</td>
                <td class="py-2 px-4 text-center">{{ \App\Models\User::find($carpeta->user_id)->name}}</td>
                <td class="py-2 px-4 text-center">{{ $carpeta->created_at }}</td>
                <td class="py-2 px-4 text-center">{{ $carpeta->updated_at ?? 'No ha sido actualizado' }}</td>
            </tr>
        @endforeach
        </table>
        @endif
        
        <!-- Sección de Archivos -->
        @if (!$allFiles->isEmpty())
        <h3 class="text-orange-400 text-xl font-semibold text-center m-3">Archivos</h3>
        <table class="min-w-full bg-gray-800 text-white">
            <tr class="bg-gray-700">
                <th class="py-2 px-4"></th>
                <th class="py-2 px-4">Name</th>
                <th class="py-2 px-4">Size</th>
                <th class="py-2 px-4">Owner</th>
                <th class="py-2 px-4">Created at</th>
                <th class="py-2 px-4">Updated at</th>
            </tr>
        
@foreach ($allFiles as $fichero)
            <tr class="bg-gray-600">
                <td class="py-2 px-4">
                    <div class="flex gap-2">
                        @can('delete', $fichero)
                            <a class="text-white" href="/delete/{{ $fichero->id }}"><span
                                    class="material-symbols-outlined">delete</span></a>
                        @endcan
                        @if(Auth::user()->id === $fichero->user_id)
                        <a class="text-white cursor-pointer" onclick="shareFile('{{ $fichero->id }}')"><span
                                class="material-symbols-outlined">share</span></a>
                        @endif
                    </div>
                </td>
                <td class="py-2 px-4 text-center">
    <a class="text-orange-400 hover:underline cursor-pointer" onclick="previewFile('{{ $fichero->id }}', '{{ $fichero->name }}')">
        {{ $fichero->name }}
    </a>
    <a href="/download/{{ $fichero->id }}" class="ml-2 text-sm text-gray-400 hover:text-white">
        <span class="material-symbols-outlined" style="font-size: 16px;">download</span>
    </a>
</td>
                <td class="py-2 px-4 text-center">{{ $fichero->size() }}</td>
                <td class="py-2 px-4 text-center">{{ \App\Models\User::find($fichero->user_id)->name}}</td>
                <td class="py-2 px-4 text-center">{{ $fichero->created_at }}</td>
                <td class="py-2 px-4 text-center">{{ $fichero->updated_at ?? 'No ha sido actualizado' }}</td>
            </tr>
        @endforeach
        </table>
        @endif
        
        @if ($allFolders->isEmpty() && $allFiles->isEmpty())
            <h2 class="text-orange-500 text-2xl font-bold text-center m-5">Esta carpeta está vacía</h2>
        @endif
        @can('upload', App\Models\Fichero::class)
        <form id="uploadform" method="POST" action="/upload" enctype="multipart/form-data" class="text-white m-8">
            @csrf
            <input type="hidden" name="carpeta_id" value="{{ request('folder') }}">
            <div class="flex justify-center">
                <label class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded" for="inputfile">Upload</label>
                <input id="inputfile" name="uploaded_file" style="display:none" type="file" onchange="uploadform.submit()" />
            </div>
        </form>
@endcan
        
    @else
        <div class="flex items-center justify-center mt-8">
            <div class="grid grid-cols-1 gap-4 w-1/2 md:w-1/3 bg-gray-700 drop-shadow-md p-6 rounded-lg shadow-md border border-slate-600">
                <header class="text-orange-500 flex flex-row justify-center">
                    <h2 class="text-center font-bold text-xl float-left">Bienvenido, <br>Logeate para empezar a usar Driven't</h2>
                </header>
            </div>
        </div>
    @endauth
</table>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-gray-800 p-4 rounded-lg max-w-4xl w-full mx-4 relative">
        <button onclick="closePreview()" class="absolute top-2 right-2 text-white hover:text-gray-300">
            <span class="material-symbols-outlined">close</span>
        </button>
        <h3 id="previewTitle" class="text-white text-xl mb-4"></h3>
        <div id="previewContent" class="bg-white rounded-lg p-4 min-h-[400px] flex items-center justify-center">
            <!-- Preview content will be inserted here -->
        </div>
    </div>
</div>

<script>
function previewFile(fileId, fileName) {
    const modal = document.getElementById('previewModal');
    const title = document.getElementById('previewTitle');
    const content = document.getElementById('previewContent');
    
    title.textContent = fileName;
    content.innerHTML = '<div class="text-gray-600">Loading preview...</div>';
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    fetch(`/preview/${fileId}`)
        .then(response => response.json())
        .then(data => {
            if (data.type === 'image') {
                console.log(data);
                content.innerHTML = `<img src="${data.url}" class="max-h-[600px] max-w-full object-contain" alt="${fileName}">`;
            } else if (data.type === 'pdf') {
                content.innerHTML = `<iframe src="${data.url}" class="w-full h-[600px]" frameborder="0"></iframe>`;
            } else if (data.type === 'text') {
                content.innerHTML = `<pre class="text-gray-800 whitespace-pre-wrap overflow-auto max-h-[600px] w-full">${data.content}</pre>`;
            } else {
                content.innerHTML = '<div class="text-gray-600">La previsualización no está disponible para este tipo de archivo</div>';
            }
        })
        .catch(error => {
            content.innerHTML = '<div class="text-red-500">Error loading preview</div>';
            console.error('Error:', error);
        });
}

function closePreview() {
    const modal = document.getElementById('previewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function shareFile(fileId) {
    fetch(`/share/file/${fileId}`)
        .then(response => response.json())
        .then(data => {
            navigator.clipboard.writeText(data.share_url);
            alert('Link copiado al portapapeles!');
        })
        .catch(error => console.error('Error:', error));
}

function shareFolder(folderId) {
    fetch(`/share/folder/${folderId}`)
        .then(response => response.json())
        .then(data => {
            navigator.clipboard.writeText(data.share_url);
            alert('Link copiado al portapapeles!');
        })
        .catch(error => console.error('Error:', error));
}
</script>


