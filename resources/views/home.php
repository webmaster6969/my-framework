<h1>Hello, {{ $name }}</h1>

@if ($name === 'John')
<p>Welcome back!</p>
@else
<p>Nice to meet you.</p>
@endif

@foreach (['apple', 'banana'] as $fruit)
<li>{{ $fruit }}</li>
@endforeach

@include('partials.footer')