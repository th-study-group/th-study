<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Mail\InquiryAnsweredMail;
use App\Models\Comment;
use App\Repositories\CommentRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 댓글 서비스
 */
class CommentService
{
    public function __construct(
        private CommentRepository $commentRepository
    ) {}

    /**
     * 게시글 댓글 목록
     *
     * @param integer $postIdx
     * @param integer $perPage
     * @return LengthAwarePaginator
     */
    public function getByPostIdx(int $postIdx, int $perPage = 20): LengthAwarePaginator
    {
        $comments = $this->commentRepository->getByPostIdx($postIdx, $perPage);

        Log::info('[Comment][List] 조회 완료', [
            'user_idx' => auth()->id(),
            'post_idx' => $postIdx,
            'per_page' => $perPage,
            'ip' => request()->ip(),
        ]);

        return $comments;
    }

    /**
     * 댓글 등록
     *
     * @param array $payload
     * @return Comment
     */
    public function create(array $payload): Comment
    {
        $ip = $payload['ip'] ?? request()->ip();

        $comment = $this->commentRepository->create([
            'user_idx' => $payload['user_idx'],
            'post_idx' => $payload['post_idx'],
            'content' => $payload['content'],
            'create_user_idx' => $payload['user_idx'],
        ]);

        Log::info('[Comment][Create] 등록 완료', [
            'user_idx' => $payload['user_idx'],
            'comment_idx' => $comment->idx,
            'post_idx' => $comment->post_idx,
            'ip' => $ip,
        ]);

        $this->notifyAdmins($comment);

        return $comment;
    }

    /**
     * 댓글 조회
     *
     * @param integer $idx
     * @return Comment
     */
    public function getByIdx(int $idx): Comment
    {
        $comment = $this->commentRepository->findByIdx($idx);

        Log::info('[Comment][View] 조회 완료', [
            'user_idx' => auth()->id(),
            'comment_idx' => $comment->idx,
            'post_idx' => $comment->post_idx,
            'ip' => request()->ip(),
        ]);

        return $comment;
    }

    /**
     * 댓글 수정
     *
     * @param Comment $comment
     * @param array $payload
     * @return Comment
     */
    public function update(Comment $comment, array $payload): Comment
    {
        $ip = $payload['ip'] ?? request()->ip();

        $updatedComment = $this->commentRepository->update($comment, [
            'content' => $payload['content'],
            'update_user_idx' => $payload['update_user_idx'],
        ]);

        Log::info('[Comment][Update] 수정 완료', [
            'user_idx' => $payload['update_user_idx'],
            'comment_idx' => $updatedComment->idx,
            'post_idx' => $updatedComment->post_idx,
            'ip' => $ip,
        ]);

        return $updatedComment;
    }

    /**
     * 댓글 삭제
     *
     * @param Comment $comment
     * @param array $payload
     * @return void
     */
    public function delete(Comment $comment, array $payload): void
    {
        $ip = $payload['ip'] ?? request()->ip();

        $this->commentRepository->update($comment, [
            'delete_user_idx' => $payload['delete_user_idx'],
        ]);

        $comment->delete();

        Log::info('[Comment][Delete] 삭제 완료', [
            'user_idx' => $payload['delete_user_idx'],
            'comment_idx' => $comment->idx,
            'post_idx' => $comment->post_idx,
            'ip' => $ip,
        ]);
    }

    /**
     * 게시글 작성자에게 댓글 등록 알림 메일 발송
     *
     * @param Comment $comment
     * @return void
     */
    private function notifyAdmins(Comment $comment): void
    {
        $commentWithRelations = Comment::with(['post.user'])->find($comment->idx);
        if (!$commentWithRelations) {
            return;
        }

        $post = $commentWithRelations->post;
        if (!$post) {
            return;
        }

        $postTypeExcluded = config('board.postTypeExcluded', []);
        if (!empty($postTypeExcluded) && in_array($post->post_type, $postTypeExcluded, true)) {
            return;
        }

        $writer = $post->user;
        if (!$writer || empty($writer->email)) {
            return;
        }

        $titlePreview = Str::limit($post->title, 30, '...');
        $commentPreview = Str::limit($commentWithRelations->content, 80, '...');
        $subjectTitle = sprintf('%s에 답변이 등록되었습니다.', $titlePreview);
        $bodyText = sprintf('%s에 답변 "%s"', $titlePreview, $commentPreview);
        $link = route('inquiries.show', ['idx' => $post->idx]);

        SendMailJob::dispatch(
            $writer->email,
            new InquiryAnsweredMail(
                subjectTitle: $subjectTitle,
                bodyText: $bodyText,
                link: $link
            ),
            '문의답변알림',
            null,
            $writer->idx
        );
    }
}
