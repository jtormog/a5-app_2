<nav class="border-gray-200 bg-gray-900">
    <div class="flex flex-wrap justify-between p-4">
        <div class="flex place-self-start">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Google_Drive_icon_%282020%29.svg/800px-Google_Drive_icon_%282020%29.svg.png"
                class="h-8 block invert" alt="drivent Logo"/>
            <span class="self-center text-2xl font-semibold whitespace-nowrap block ml-2 text-orange-500"><a href="/">Driven't</a></span>
        </div>
        <div class="ml-auto">
            <ul class="flex space-x-4">
                @auth
                    <li>
                        <a href="/user/{{ Auth::user()->id }}"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-orange-700 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">{{ Auth::user()->name }}</a>
                    </li>
                    <li>
                        <a href="/logout"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-orange-700 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Logout</a>
                    </li>
                @else
                    <li>
                        <a href="/login"
                            class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-orange-700 md:p-0 dark:text-white md:dark:hover:text-orange-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Login</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
