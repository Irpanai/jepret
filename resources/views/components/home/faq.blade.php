@props([
    'categories' => config('faq.categories', []),
])

<x-faq :categories="$categories" {{ $attributes }} />