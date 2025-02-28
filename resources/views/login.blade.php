@include('head')
<form method="post" class="flex items-center justify-center min-h-screen">
    @csrf
    <div class="grid grid-cols-1 gap-4 w-1/2 md:w-1/3 bg-gray-700 drop-shadow-md p-6 rounded-lg shadow-md border border-slate-600">
        <img class="w-20 place-self-center invert" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Google_Drive_icon_%282020%29.svg/800px-Google_Drive_icon_%282020%29.svg.png">
        <label for="email" class="text-orange-500 font-semibold">Email</label>
        <input type="email" name="email" placeholder="Email" class="border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-orange-500">
        
        <label for="password" class="text-orange-500 font-semibold">Password</label>
        <input type="password" name="password" placeholder="Password" class="border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-orange-500">
        
        <input type="submit" value="Login" class="bg-orange-500 text-white font-semibold py-2 rounded hover:bg-orange-700 cursor-pointer">
        
        <div class="text-center text-gray-300">
            <a href="/" class="text-orange-500 hover:text-orange-700">Volver al inicio</a>
        </div>
    </div>
</form>
