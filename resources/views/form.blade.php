<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
</head>

<body>
    @if ($errors->any())
    <ul>
        @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
    @endif

    <form action="{{ route('submit') }}" method="POST">
        @method('post')
        @csrf
        <label for="username">username</label>
        <input type="text" name="username" id="username"><br>
        @error('username')
        {{ $message }}
        @enderror
        <br>
        <label for="password">password</label>
        <input type="password" name="password" id="password"><br>
        @error('password')
        {{ $message }}
        @enderror
        <br>
        <button type="submit">submit</button>
    </form>
</body>

</html>