<!DOCTYPE html>
<html>
<head>
    <title>Blade Test</title>
</head>
<body>
    <h1>Blade Template Test</h1>
    <p>Current time: {{ now() }}</p>
    <p>App name: {{ config('app.name') }}</p>
    <p>Authenticated: {{ auth()->check() ? 'Yes' : 'No' }}</p>
    @if(auth()->check())
        <p>User ID: {{ auth()->user()->id }}</p>
        <p>Username: {{ auth()->user()->username }}</p>
        <p>Full Name: {{ auth()->user()->full_name }}</p>
        <p>Role: {{ auth()->user()->role }}</p>
        <p>Balance: {{ auth()->user()->balance }}</p>
        <p>Level: {{ auth()->user()->level }}</p>
    @endif
</body>
</html>
