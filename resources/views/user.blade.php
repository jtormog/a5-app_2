@include('head')
@include('navbar')
<div class="flex items-center justify-center mt-8">
    <div class="grid grid-cols-1 gap-4 w-1/2 md:w-1/3 bg-gray-700 drop-shadow-md p-6 rounded-lg shadow-md border border-slate-600">
        <header class="text-orange-500 flex flex-row justify-center">
            <h2 class="text-center font-bold text-xl float-left">{{$user->name}}</h2 class=""><a href="{{$user->id}}/edit"><h2><span class="material-symbols-outlined text-orange-500 hover:text-orange-700 ml-2">
                edit
                </span></h2></a>
        </header>
    </div>
</div>