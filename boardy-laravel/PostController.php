<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Post::class);

        $posts = Post::with('comments')
            ->latest()
            ->paginate(5);

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Post::class);

        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'body' => 'nullable|string|max:5000',
        ]);


        $post = $request->user()->posts()->create($validated);

        try {
            Http::timeout(2)->post('http://localhost:8000/internal/broadcast', [
                'id' => $post->id,
                'title' => $post->title,
                'body' => $post->body,
                'author' => auth()->user()->name,
                'created_at' => $post->created_at->toISOString(),
            ]);
        } catch (\Exception $e) {
            \Log::warning('WS Broadcast failed: ' . $e->getMessage());
        }

        return redirect('/posts')->with('success', 'Пост создан');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);

        $post->load('comments', 'author');
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'body' => 'nullable|string|max:5000',
        ]);

        $post->update($validated);

        return redirect()->route('posts.show', $post)
            ->with('success', 'Пост обновлен.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Пост удален.');
    }
}
