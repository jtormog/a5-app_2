@include('head')
@include('navbar')
<form method="post" class="flex items-center justify-center min-h-screen">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 gap-4 w-1/2 md:w-1/3 bg-gray-700 drop-shadow-md p-6 rounded-lg shadow-md border border-slate-600">
        <img class="w-20 place-self-center invert" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Google_Drive_icon_%282020%29.svg/800px-Google_Drive_icon_%282020%29.svg.png">
        <label for="name" class="text-orange-500 font-semibold">Nombre</label>
        <input name="name" placeholder="Nombre" class="border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-orange-500" value="{{$user->name}}">
        
        <label for="email" class="text-orange-500 font-semibold">Email</label>
        <input type="email" name="email" placeholder="email" class="border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-orange-500" value="{{$user->email}}">
        
        <input type="submit" value="Guardar Cambios" class="bg-orange-500 text-white font-semibold py-2 rounded hover:bg-orange-700 cursor-pointer">
    </div>
</form> 