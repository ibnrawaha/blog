<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Post;
use App\Comment;
use App\User;
// use App\User;

class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show', 'search', 'userPosts']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // Incase searching
        if ($request->search){
            $posts = Post::search($request->search)->paginate(10);
            $posts->search = $request->search;
        }
        else {
            // incase browsing all posts
            $posts = Post::orderBy('id', 'desc')->paginate(10);
        }


        return view('posts.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            'image' => 'image|max:2048|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if($request->hasFile('image')){
            // TO GET THE REAL FILE NAME
            $file = $request->file('image')->getClientOriginalName();
            $fileExplode = explode('.', $file);
            $fileToStore = $fileExplode[0]. time() . "." .  $fileExplode[1];
            // MUST BE ADDED TO DECLARE THE PATH
            $path = $request->file('image')->storeAs('public/image/uploads', $fileToStore);
        }
        else {
            // IF THERE ARE NO IMAGES
            $fileToStore = 'noImage.jpg';
        }



        $post = new Post;
        $post->title = $request->title;
        $post->content = $request->content;
        $post->user_id = auth()->user()->id;
        $post->image = $fileToStore;
        $post->save();

        // return $post->image;
        return redirect('/posts')->with('success', 'Post Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        // $title = str_replace('-', ' ', $post->title);
        // $post->title = str_replace('-', ' ', $title);
        // $post = Post::where('title', str_replace('-', ' ', $title))->first();
        $comments = Comment::orderBy('id', 'asc')->where('post_id', '=', $id)->get();

        // return view('posts.show')->with('post',$post);
        return view('posts.show')->with('post',$post)->with('comments', $comments);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $post = Post::find($id);

        if(auth()->user()->id == $post->user_id){
            return view('posts.edit')->with('post',$post);
        }
        else{
            // return redirect('/posts')->with('error', 'An Unauthorized Page');
            return view('error.404');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            // 'image' => 'image|max:2048|mimes:jpeg,png,jpg,gif,svg',
        ]);

        // dd($request);

        if($request->hasFile('image')){
            // TO GET THE REAL FILE NAME
            $file = $request->file('image')->getClientOriginalName();
            $fileExplode = explode('.', $file);
            $fileToStore = $fileExplode[0]. time() . "." .  $fileExplode[1];
            // MUST BE ADDED TO DECLARE THE PATH
            $path = $request->file('image')->storeAs('public/image/uploads', $fileToStore);

            // DELETE OLD IMAGE IF EXIST
            if($post->image != 'noimage.jpg'){

                // return $post->image;
                Storage::delete('/public/image/uploads/'. $post->image);

            }
        }
        else {
            // IF THERE IS NO IMAGE
            $fileToStore = 'noImage.jpg';
        }

        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->image = $fileToStore;
        $post->save();
        // return $fileToStore;
        return redirect('/posts/'.$post->id.'/'.$post->title)->with('post', $post);

    }

    public function delete($id) {
        $post = Post::find($id);

        return view('posts.delete')->with('post', $post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        
        if(auth()->user()->id == $post->user_id){
            
            // DELETE IMAGE
            if($post->image != 'noimage.jpg'){
                
                $filePath = '/public/image/uploads/';
                Storage::delete($filePath.$post->image);
            }

            //DELETE POST
            $post->delete();
            return redirect('/dashboard')->with('error', 'Post Deleted !');
        }
        else{
            // return redirect('/posts')->with('error', 'An Unauthorized Page');
            return view('error.404');
        }



    }
    

    public function userPosts($username){

        $user = User::where('name', $username)->get()->first();

        if($user){
            
            $posts = Post::where('user_id', $user->id)->paginate(10);

            return view('posts.user_posts')->with('posts', $posts)->with('user', $user);
        }

        else {
            return view('error.404');
        }


    }

    public function search(Request $request){
        
        // return $request->search;
        // $posts = Post::search($request->search)->paginate();
        $posts = Post::search($request->search)->get();
        // $posts = Post::search('qui')->paginate(10);
        $posts->search = $request->search;

        // return 'hel';
        return view('posts.search')->with('posts', $posts);
    }

}
