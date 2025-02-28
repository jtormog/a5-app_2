@include('head')
@include('navbar')
@auth

        @if (Auth::user()->ficheros->isEmpty())
            <h2 class="text-orange-500 text-2xl font-bold text-center m-5">No has subido ningún archivo</h2>
        @else
        <h2 class="text-orange-500 text-2xl font-bold text-center m-5">Tus archivos</h2>
        <table class="min-w-full bg-gray-800 text-white">
            <tr class="bg-gray-700">
                <th class="py-2 px-4"></th>
                <th class="py-2 px-4">Name</th>
                <th class="py-2 px-4">Size</th>
                <th class="py-2 px-4">Owner</th>
                <th class="py-2 px-4">Created at</th>
                <th class="py-2 px-4">Updated at</th>
            </tr>
        @foreach (Auth::user()->ficheros as $fichero)
            <tr class="bg-gray-600">
                <td class="py-2 px-4">
                    @can('delete', $fichero)
                        <a class="text-white" href="/delete/{{ $fichero->id }}"><span
                                class="material-symbols-outlined
                        ">
                                delete
                            </span></a>
                    @endcan
                </td>
                <td class="py-2 px-4 text-center"><a class="text-orange-400 hover:underline"
                        href="/download/{{ $fichero->id }}">{{ $fichero->name }}</a></td>
                <td class="py-2 px-4 text-center">{{ $fichero->size() }}</td>
                <td class="py-2 px-4 text-center">{{ Auth::user()->name }}</td>
                <td class="py-2 px-4 text-center">{{ $fichero->created_at }}</td>
                <td class="py-2 px-4 text-center">{{ $fichero->updated_at ?? 'No ha sido actualizado' }}</td>
            </tr>
        @endforeach
        @endif
        @if (!Auth::user()->ficherosCompartidos->isEmpty())
        <h2 class="text-orange-500 text-2xl font-bold text-center m-5">Archivos compartidos</h2>
        <table class="min-w-full bg-gray-800 text-white">
            <tr class="bg-gray-700">
                <th class="py-2 px-4"></th>
                <th class="py-2 px-4">Name</th>
                <th class="py-2 px-4">Size</th>
                <th class="py-2 px-4">Owner</th>
                <th class="py-2 px-4">Created at</th>
                <th class="py-2 px-4">Updated at</th>
            </tr>
        @foreach (Auth::user()->ficherosCompartidos as $fichero)
            <tr class="bg-gray-600">
                <td class="py-2 px-4">
                    @can('delete', $fichero)
                        <a class="text-white" href="/delete/{{ $fichero->id }}"><span
                                class="material-symbols-outlined
                        ">
                                delete
                            </span></a>
                    @endcan
                </td>
                <td class="py-2 px-4 text-center"><a class="text-orange-400 hover:underline"
                        href="/download/{{ $fichero->id }}">{{ $fichero->name }}</a></td>
                <td class="py-2 px-4 text-center">{{ $fichero->size() }}</td>
                <td class="py-2 px-4 text-center">{{ User::find($fichero->user_id)->name}}</td>
                <td class="py-2 px-4 text-center">{{ $fichero->created_at }}</td>
                <td class="py-2 px-4 text-center">{{ $fichero->updated_at ?? 'No ha sido actualizado' }}</td>
            </tr>
        @endforeach
        @endif
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
@can('upload', App\Models\Fichero::class)
    <form id="uploadform" method="POST" action="/upload" enctype="multipart/form-data" class="text-white m-8">
        @csrf
        <div class="flex justify-center">
            <label class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded" for="inputfile">Upload</label>
            <input id="inputfile" name="uploaded_file" style="display:none" type="file" onchange="uploadform.submit()" />
        </div>
    </form>
@endcan
