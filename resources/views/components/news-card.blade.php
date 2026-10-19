@props([
    'berita' => null,
    'title' => null,
    'date' => null,
    'category' => null,
    'excerpt' => null,
    'url' => null,
    'image' => null,
    'featured' => false,
])

<x-kartu-berita 
    :berita="$berita"
    :title="$title"
    :date="$date"
    :category="$category"
    :excerpt="$excerpt"
    :url="$url"
    :image="$image"
    :featured="$featured"
/>
