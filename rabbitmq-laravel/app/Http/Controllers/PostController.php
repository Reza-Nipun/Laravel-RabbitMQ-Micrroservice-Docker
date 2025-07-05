<?php

namespace App\Http\Controllers;

use App\Jobs\PostCreate;
use App\Jobs\PostDelete;
use App\Models\Post;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $elasticClient;

    public function __construct()
    {
        $this->elasticClient = ClientBuilder::create()
            ->setHosts(config('services.elasticsearch.hosts'))
            ->build();
    }

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

    public function search(Request $request)
    {
        $query = $request->input('q');

        if (! $query) {
            return response()->json(['error' => 'Search query is required'], 400);
        }

        $params = [
            'index' => 'posts', // ES index name
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['title', 'image'], // fields to search in
                        // 'fuzziness' => 'AUTO', // optional: to allow typo tolerance
                        'type' => 'phrase', // exact phrase match
                    ],
                ],
            ],
        ];

        $results = $this->elasticClient->search($params);

        // Extract _source from hits
        $posts = array_map(function ($hit) {
            return $hit['_source'];
        }, $results['hits']['hits']);

        return response()->json($posts);
    }
}
