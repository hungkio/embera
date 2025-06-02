<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\CommentDataTable;
use App\Domain\Comment\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class CommentController
{
    use AuthorizesRequests;

    public function index(CommentDataTable $dataTable)
    {
        $this->authorize('comments.view', Comment::class);

        return $dataTable->render('admin.comments.index');
    }

    public function delete($id){
        $comment = Comment::find($id);
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => __('Bình luận đã được xóa thành công!'),
        ]);
    }
}
