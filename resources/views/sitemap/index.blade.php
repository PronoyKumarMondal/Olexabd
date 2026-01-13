<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($posts as $post)
    <url>
        <loc>{{ $post['loc'] }}</loc>
        <lastmod>{{ $post['lastmod'] }}</lastmod>
        <changefreq>{{ $post['changefreq'] }}</changefreq>
        <priority>{{ $post['priority'] }}</priority>
    </url>
    @endforeach
</urlset>
