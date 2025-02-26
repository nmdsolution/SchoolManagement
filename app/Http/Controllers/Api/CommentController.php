<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = request()->validate([
            'course_id' => 'required_unless:comment_id,null',
            'comment_id' => 'required_unless:comment_id,null',
        ]);

        if(isset($data['course_id'])){
            $list = Comment::where('course_id', $data['course_id'])->with('comments')->paginate(15);

            return response()->json($list);
        }

        $list = Comment::where('comment_id', $data['comment_id'])->paginate(15);

        return response()->json($list);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string',
            'content' => 'required|string|min:15',
            'course_id' => 'nullable|required_without:comment_id|exists:courses,id',
            'comment_id' => 'nullable|required_without:course_id|exists:comments,id'
        ]);

        if(isset($data['comment_id'])){
            $comment = Comment::find($data['comment_id']);

            if($comment->course_id == null)
                return response()->json(['message' => 'max comment level is 2.'], 422);
        }

        $data['user_id'] = Auth::user()->id;

        $comment = Comment::create($data);
        $comment->user;

        return response()->json($comment, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        $comment->user;
        $comment->comments;

        return response()->json($comment);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        if(Auth::user()->id != $comment->user_id){
            return response()->json(['message' => "Cannot edit other users post"], 401);
        }

        $data = $request->validate([
            'title' => 'nullable|string',
            'content' => 'required|string|min:15'
        ]);

        $comment->update($data);
        $comment->user;
        $comment->comments;

        return response()->json($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        if(Auth::user()->id != $comment->user_id){
            return response()->json(['message' => "Cannot delete other users post"], 401);
        }

        $comment->delete();

        return response()->json(['message' => 'Done'], 200);
    }
}
