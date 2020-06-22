<?php

namespace App\Services\API;

use Modules\Identity\Identity;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\Shared\TransformerService;
use App\Events\LiveStream;
use App\Events\JoinPostEvent;


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
      'signal' => 'required',
      'socket_id' => 'required',
		]);

		$post = Post::create([
      'user_id' => Identity::currentUser('api')->id,
      'type' => $request->type,
      'signal' => $request->signal,
      'socket_id' => $request->socket_id,
    ]);
    
    if($post->type == 'Live'){
      broadcast(new LiveStream($post))->toOthers();
    }

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
   * Display the specified resource.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Post  $post
   * @return \Illuminate\Http\Response
   */
  public static function join(Request $request, Post $post){
    broadcast(new JoinPostEvent($post, $request->signal, $request->socket_id));

    return Response::success();
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
			// add attributes to validate
		]);


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
      'signal' => $post->signal,
      'socket_id' => $post->socket_id,
		];
	}
}
