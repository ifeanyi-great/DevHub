@vite(['resources/css/app.css', 'resources/js/app.js'])

<main class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        <h1 class="mb-6 text-2xl font-bold text-gray-900">
          Community Posts Feed
        </h1>

        <div class="space-y-4">
          @foreach ($posts as $post)
             <x-posts.post-card :post="$post" />

           @endforeach
        </div>

     <div class="mt-8 flex flex-col items-center gap-4">

            <p class="text-sm text-gray-500">
                Showing
                <span class="font-medium text-gray-900">{{ $posts->firstItem() }}</span>
                -
                <span class="font-medium text-gray-900">{{ $posts->lastItem() }}</span>
                of
                <span class="font-medium text-gray-900">{{ $posts->total() }}</span>
                posts
              </p>

         {{ $posts->onEachSide(2)->links() }}
 </div>

</main>