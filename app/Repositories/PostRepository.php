<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\Media;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Tag;

use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\ChecksModelExistence;
use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\PostBlogException;
use Illuminate\Support\Facades\Validator;
class PostRepository
{
    use ChecksModelExistence;

    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }


    public function getUserPosts()
    {
        $userID=Auth::id();
        $post = Post::with('user')->where('status', 'published')->where('user_id', $userID)->orderBy('created_at', 'desc')->get();
        return $post;
    }


    public function createPost($data)
    {

        // Get the currently authenticated user's ID
        $user_id = Auth::id();
        $data['owner_id'] = $user_id;

        // Create the post
        $post = Post::create($data);

        // Ensure 'user_ids' exists and is an array
        if (isset($data['user_ids']) && is_array($data['user_ids']) && count($data['user_ids']) > 0) {
            foreach ($data['user_ids'] as $user) {
                Tag::create([
                    'user_id' => $user,
                    'post_id' => $post->id,
                ]);
            }
            Tag::create([
                'user_id' => $user_id,
                'post_id' => $post->id,
            ]);
        }
        else{
            Tag::create([
                'user_id' => $user_id,
                'post_id' => $post->id,
            ]);
        }

        return $post;
    }



    public function update($id,$data)
    {
        $user_id=Auth::id();

        $post = Post::findOrFail($id);
        $post->owner_id = $user_id; // Use the authenticated user ID
        $post->category_id = $data['category_id'];
        $post->description = $data['description'];
        $post->save(); // Save the post first

        return $post;
    }

    public function archive($id)
    {

        $postCheck = $this->checkModelExists(Post::class, $id);

        $post = Post::findOrFail($id);
        $post->status = 'archived';
        $post->save();
        return $post;
    }

    public function filterPosts($status)
    {
        Log::info("filterPosts");
        Log::info($status);
        $owner_id=Auth::id();
        $Posts=Post::where('status', $status)->where('owner_id', $owner_id)->with('media')->orderBy('created_at', 'desc')->paginate(4, ['*'], 'page', 1);
        return $Posts;
    }


    public function getPersonalPhoto(){
        $userID=Auth::id();
        $medias=Media::where('mediable_id', $userID)->where('type','user_profile_image')->first();
        return $medias->URL;
    }

    public function getUserName(){
        $user=User::find(Auth::id());
        return $user->name;
    }

    public function deleteMedia($id)
    {
        $mediacheck = $this->checkModelExists(Media::class, $id);

        $media=Media::findOrFail($id);
        $postId = $media->mediable_id; // Store post ID before deletion

         // Delete file from storage
         if ($media->type == 'post_image') {
            Storage::disk('public')->delete('photos/' . $media->URL);
        } else if($media->type == 'post_video'){
            Storage::disk('public')->delete('videos/' . $media->URL);
        }
        else{
            $validator = Validator::make([], []);
            $errorMessage = 'Invalid media type';
            throw new PostBlogException($validator, $errorMessage);
        }

        $media->delete();

        $post=Post::findOrFail($postId);
        $data['id']=$post->id;
        $data['description']=$post->description;
        $data['category_id']=$post->category_id;
        $data['media']=Media::where('mediable_id', $postId)->get();

         return $data ;
    }

    public function publishPost($id)
    {
        $postCheck = $this->checkModelExists(Post::class, $id);

        $post=Post::findOrFail($id);
        if($post->status=="published"){
            $errorMessage = 'Post already published';
            $validator = Validator::make([], []);
            throw new PostBlogException($validator, $errorMessage);
        }
        $post->status='published';
        $post->save();
        return $post;
    }

    public function postList($status, $page=1)
    {
        Log::info("postList");
        Log::info($status);
        $userID = Auth::id();
        if($status=="draft"||$status=="archived"){
            $posts=Post::query()
            ->where('status', $status)
            ->where('owner_id', $userID)
            ->with(['media', 'user.media', 'comment'])
            ->orderBy('created_at', 'desc')
            ->paginate(4, ['*'], 'page', $page);
        }
        else{
            $posts = Post::query()
            ->where('status', $status)
        // Start with Eloquent query
            ->whereExists(function ($query)  {
                $query->select(DB::raw(1))
                      ->from('tags')
                      ->whereColumn('tags.post_id', 'posts.id');
            })
            ->with(['media', 'user.media', 'comment'])
            ->orderBy('created_at', 'desc')
            ->paginate(4, ['*'], 'page', $page);

        }
        return $posts;
    }

    public function destroy($id)
    {
        $postCheck = $this->checkModelExists(Post::class, $id);

        $post=Post::findOrFail($id);
        $post->delete();
        return $post;
    }

    public function index()
    {
        $Posts=Post::with('media')->where('status', 'published')->orderBy('created_at', 'desc')->paginate(3);
        return $Posts;
    }

    public function getPost($id)
    {
        $post=Post::with('media','tag','comment','user','user.media')->findOrFail($id);
        return $post;
    }

    public function update2($id,$data)
    {
        $post=Post::findOrFail($id);
        $post->update($data);
        return $post;
    }

}
