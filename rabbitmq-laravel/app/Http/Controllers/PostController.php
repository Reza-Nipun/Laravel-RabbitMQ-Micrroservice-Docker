<?php

namespace App\Http\Controllers;

use App\Jobs\PostCreate;
use App\Jobs\PostDelete;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }

    public function show($id)
    {
        return Post::findOrFail($id);
    }

    public function store(Request $request)
    {
        $post = Post::create($request->all());

        PostCreate::dispatch($post->toArray());

        return response()->json('post is added', 201);
    }

    public function edit($id)
    {
        return Post::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        if (! $post) {
            return response()->json('post not found', 404);
        }

        $post->update($request->all());

        return response()->json('post is updated', 200);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if (! $post) {
            return response()->json('post not found', 404);
        }

        PostDelete::dispatch($post->toArray());
        $post->delete();

        return response()->json('post is deleted', 200);
    }
}
