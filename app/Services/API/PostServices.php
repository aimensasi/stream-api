<?php

namespace App\Services\API;

use App\Models\Post;
use App\Events\LiveStream;
use Modules\Identity\Identity;
use App\Services\Shared\TransformerService;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


use Modules\Core\Helpers\Response;

class PostServices extends TransformerService{

  /**
   * Filter and return a collection of data
   *
   * @return \Illuminate\Http\Response
   */
  public static function index(){
    $user = Identity::currentUser('api');
		$posts = Post::where('user_id', '!=', $user->id)->get();

		return Response::create(['rows' => (new self)->transformCollection($posts), 'total' => count($posts)]);
  }

  /**
   * Return the create page url.
   *
   * @return \Illuminate\Http\Response
   */
  public static function create(){
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public static function store(Request $request){
    $request->validate([
      'type' => 'required',
		]);

		$post = Post::create([
      'user_id' => Identity::currentUser('api')->id,
      'type' => $request->type,
      'status' => 'Live',
    ]);
    
    // if($post->type == 'Live'){
    //   // broadcast(new LiveStream($post))->toOthers();
    // }

		return Response::create([
      "post" => $post
		]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Post  $post
   * @return \Illuminate\Http\Response
   */
  public static function show(Post $post){
    return (new self)->transform($post);
  }


  /**
   * Return the edit page url
   *
   * @param  \App\Models\Post  $post
   * @return \Illuminate\Http\Response
   */
  public static function edit(Post $post){
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Post  $post
   * @return \Illuminate\Http\Response
   */
  public static function update(Request $request, Post $post){
		$request->validate([
      'status' => [
        'required',
        Rule::in(['Live', 'Ended'])
      ]
    ]);

    $post->status = $request->status;
    $post->save();


		return Response::success([
			"message" => "post was successfully updated."
		]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Post  $post
   * @return \Illuminate\Http\Response
   */
  public static function destroy(Post $post){
    $post->delete();

		return Response::success([
			"message" => "post was successfully deleted."
		]);
  }


	/**
   * transfom the resource data.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
	public function transform($post){
		return [
      'id' => $post->id,
      'user_id' => $post->user_id,
      'type' => $post->type,
      'status' => $post->status,
		];
	}
}
