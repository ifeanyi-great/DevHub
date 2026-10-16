 <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-md duration-200 transition-all">


             <h2 class="text-lg font-semibold leading-snug text-gray-900 ">
                 <a href=" {{ route('posts.show', ['post' => $post->id]) }}" class="hover:underline hover:text-blue-600 transition-colors">
                     {{$post->title}}
                 </a>
             </h2>

               <span @class(['mt-2 inline-block rounded-full  px-2.5 py-1 text-xs font-medium',
               'bg-green-50 text-green-700' => $post->difficulty === 'Beginner',
                'bg-amber-50 text-amber-700' => $post->difficulty === 'Intermediate',
                'bg-purple-50 text-purple-700' => $post->difficulty === 'Advanced',
               ])>
                 {{ $post->difficulty }}
                </span>

                <a href="{{ route('users.show', ['user' => $post->user->id]) }}"
                   class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors"
                 >
                 {{ $post->user->name }}
                 </a>

     <p class="mt-3 text-sm leading-6 text-gray-600">
        {{ $post->description }}
    </p>

</article>