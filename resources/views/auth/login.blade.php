
<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
    </head>

    <body>
     

           <form method="POST" action="login">
              @csrf

            <label for="email">Email</label>
            <input type="email" name="email" id="email">

            <label for="password">Password</label>
            <input type="password" name="password" id="password">

            <button type="submit">Login</button>

            </form>

             
       @if ($errors->any())
    <div>
    @foreach ($errors->all() as $error)
            <p >
                {{ $error }}
            </p>
        @endforeach
    </div>
   @endif   
    </body>
</html>