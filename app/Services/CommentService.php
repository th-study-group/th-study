<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Mail\InquiryAnsweredMail;
use App\Models\Comment;
use App\Repositories\CommentRepository;
use Illuminate\Pagination\LengthAwarePaginator;
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
        return $this->commentRepository->getByPostIdx($postIdx, $perPage);
    }

    /**
     * 댓글 등록
     *
     * @param array $payload
     * @return Comment
     */
    public function create(array $payload): Comment
    {
        $comment = $this->commentRepository->create([
            'user_idx' => $payload['user_idx'],
            'post_idx' => $payload['post_idx'],
            'content' => $payload['content'],
            'create_user_idx' => $payload['user_idx'],
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
        return $this->commentRepository->findByIdx($idx);
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
        return $this->commentRepository->update($comment, [
            'content' => $payload['content'],
            'update_user_idx' => $payload['update_user_idx'],
        ]);
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
        $this->commentRepository->update($comment, [
            'delete_user_idx' => $payload['delete_user_idx'],
        ]);

        $comment->delete();
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
        if (!$post || $post->post_type !== 'inquiries') {
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
