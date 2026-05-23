<?php

// app/Http/Controllers/RedditController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class RedditController extends Controller
{
    public function index()
    {
        $usernames = [
            'spez',
            'GallowBoob',
            'shittymorph',
            'Unidan',
            'awkwardtheturtle',
            'karmanaut',
            'TheBlueRedditor',
            'nathanfhtagn',
            'pm_me_your_dogs',
            'sarahjeong'
        ];

        return view('reddit.index', compact('usernames'));
    }

    public function show($username)
    {
        $response = Http::get("https://www.reddit.com/user/{$username}/about.json");

        if ($response->ok() && isset($response['data'])) {
            $info = $response['data'];

            $user = [
                'username' => $username,
                'link_karma' => $info['link_karma'] ?? null,
                'comment_karma' => $info['comment_karma'] ?? null,
                'total_karma' => $info['total_karma'] ?? (
                    ($info['link_karma'] ?? 0) + ($info['comment_karma'] ?? 0)
                ),
                'created_utc' => isset($info['created_utc']) ? date('Y-m-d', $info['created_utc']) : null,
                'avatar' => $info['icon_img'] ?? null,
            ];

            return view('reddit.show', compact('user'));
        }

        return redirect()->route('reddit.index')->with('error', 'No se pudo obtener la información del usuario.');
    }
}
