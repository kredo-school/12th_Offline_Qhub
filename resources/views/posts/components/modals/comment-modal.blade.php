<!-- コメントモーダル -->
<div class="modal fade" id="commentsModal-{{ $post->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Comments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- コメント投稿フォーム -->
                <form id="comment-form-{{ $post->id }}" action="{{ route('comment.store', $post->id) }}"
                    method="POST" class="mb-3">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="body" class="form-control form-control-sm"
                            placeholder="Add a comment..." required>
                        <button type="submit" class="btn btn-sm btn-primary">Post</button>
                    </div>
                    <p class="text-danger small d-none" id="comment-error-{{ $post->id }}"></p>
                </form>

                <!-- コメント一覧 -->
                <div id="comment-list-{{ $post->id }}" class="text-start">
                    @foreach ($post->comments as $comment)
                        @include('posts.components.partials.comment_card', ['comment' => $comment])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- コメント報告モーダル -->
@foreach ($post->comments as $comment)
    @include('posts.components.partials.comment_report_modal', [
        'comment' => $comment,
        'report_reasons' => $all_report_reasons,
    ])
@endforeach

@push('scripts')
    @once
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const forms = document.querySelectorAll('[id^="comment-form-"]');

                forms.forEach(form => {
                    const postId = form.id.split('-').pop();
                    const error = document.getElementById('comment-error-' + postId);
                    const list = document.getElementById('comment-list-' + postId);
                    const submitButton = form.querySelector('button[type="submit"]');
                    let isSubmitting = false;

                    form.addEventListener('submit', async e => {
                        e.preventDefault();

                        // 二重送信防止
                        if (isSubmitting) return;
                        isSubmitting = true;
                        submitButton.disabled = true;
                        error.classList.add('d-none');

                        const formData = new FormData(form);

                        try {
                            const res = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                            if (!res.ok) {
                                const text = await res.text();
                                console.error('Response not OK:', text);
                                throw new Error('Network error or validation failed');
                            }

                            const data = await res.json();

                            const newCommentHtml = `

                            <div class="d-flex border-bottom py-2">
                                <img src="${data.user.avatar}" class="rounded-circle me-2" width="40" height="40"
                                onerror="this.src='{{ asset('images/user_icon.png') }}';">
                                <div>
                                <strong>${data.user.name}</strong>
                                <p class="mb-0">${data.body}</p>
                                </div>
                            </div>`;

                            list.insertAdjacentHTML('afterbegin', newCommentHtml);
                            form.reset();
                        } catch (err) {
                            console.error('Error:', err);
                            error.textContent = 'Failed to post comment.';
                            error.classList.remove('d-none');
                        } finally {
                            isSubmitting = false;
                            submitButton.disabled = false;
                        }
                    });
                });
            });

            function editComment(id) {
                document.getElementById('comment-body-' + id).classList.add('d-none');
                document.getElementById('edit-form-' + id).classList.remove('d-none');
            }

            function cancelEdit(id) {
                document.getElementById('comment-body-' + id).classList.remove('d-none');
                document.getElementById('edit-form-' + id).classList.add('d-none');
            }

            async function submitEditComment(e, id, postId) {
                e.preventDefault();

                const form = document.getElementById('edit-form-' + id);
                const input = form.querySelector('input[name="body"]');
                const body = input.value;

                try {
                    const res = await fetch(`/comment/${postId}/${id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            body
                        })
                    });

                    if (!res.ok) throw new Error('Update failed');

                    const data = await res.json();
                    document.getElementById('comment-body-' + id).textContent = data.body;
                    cancelEdit(id);
                } catch (error) {
                    alert('Edit failed');
                    console.error(error);
                }
            }

            async function deleteComment(id) {
                if (!confirm('Are you sure you want to delete this comment?')) return;

                try {
                    const res = await fetch(`/comment/${id}/destroy`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) throw new Error('Delete failed');

                    const commentEl = document.getElementById('comment-body-' + id).closest('.d-flex');
                    commentEl.remove();
                } catch (error) {
                    alert('Delete failed');
                    console.error(error);
                }
            }
        </script>
    @endonce
@endpush
