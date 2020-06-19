<?php

namespace App\Services\API;

use Modules\Identity\Identity;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\Shared\TransformerService;

use Modules\Core\Helpers\Response;

class PostServices extends TransformerService{

  /**
   * Filter and return a collection of data
   *
   * @return \Illuminate\Http\Response
   */
  public static function index(){
		$posts = Post::all();

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
		]);

		return Response::success([
			"message" => "post was successfully updated."
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
		];
	}
}
