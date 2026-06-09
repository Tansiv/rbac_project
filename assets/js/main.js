// ── Toast ────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    let t = document.getElementById('globalToast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'globalToast';
        t.className = 'toast';
        document.body.appendChild(t);
    }
    t.textContent = msg;
    t.className = 'toast ' + type;
    requestAnimationFrame(() => {
        requestAnimationFrame(() => t.classList.add('show'));
    });
    setTimeout(() => { t.classList.remove('show'); }, 3000);
}

// ── Confirm modal ────────────────────────────────────────────
function openConfirm(title, message, onConfirm) {
    const overlay = document.getElementById('confirmModal');
    overlay.querySelector('.modal-title').textContent = title;
    overlay.querySelector('.modal-body').textContent  = message;
    overlay.querySelector('.confirm-yes').onclick = () => {
        closeConfirm();
        onConfirm();
    };
    overlay.classList.add('active');
}
function closeConfirm() {
    const overlay = document.getElementById('confirmModal');
    if (overlay) overlay.classList.remove('active');
}

// ── Inject confirm modal into every page ─────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const modal = `
    <div class="modal-overlay" id="confirmModal">
      <div class="modal">
        <div class="modal-title">Confirm</div>
        <div class="modal-body" style="color:var(--text-muted);font-size:.93rem;"></div>
        <div class="modal-actions">
          <button class="btn btn-ghost" onclick="closeConfirm()">Cancel</button>
          <button class="btn btn-danger confirm-yes">Delete</button>
        </div>
      </div>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', modal);
});

// ── Avatar colour from username hash ─────────────────────────
function avatarClass(username) {
    let h = 0;
    for (let c of username) h = (h * 31 + c.charCodeAt(0)) & 0xffffffff;
    return 'av-' + (Math.abs(h) % 6);
}

// ── Toggle comment form ───────────────────────────────────────
function toggleCommentForm(postId) {
    const f = document.getElementById('cf-' + postId);
    if (f) {
        f.style.display = f.style.display === 'none' ? 'flex' : 'none';
    }
}

// ── AJAX delete helper ────────────────────────────────────────
async function ajaxPost(url, data) {
    const fd = new FormData();
    for (const [k, v] of Object.entries(data)) fd.append(k, v);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    if (csrf && !fd.has('_csrf')) {
        fd.append('_csrf', csrf);
    }
    const r = await fetch(url, { method: 'POST', body: fd });
    return r.json();
}

// ── Delete post ───────────────────────────────────────────────
function deletePost(postId) {
    openConfirm('Delete Post', 'Are you sure you want to delete this post? All comments will also be removed.', async () => {
        const res = await ajaxPost('/rbac_project/api/delete_post.php', { post_id: postId });
        if (res.success) {
            document.getElementById('post-' + postId)?.remove();
            showToast('Post deleted', 'success');
        } else {
            showToast(res.error || 'Failed to delete post', 'error');
        }
    });
}

// ── Delete comment ────────────────────────────────────────────
function deleteComment(commentId) {
    openConfirm('Delete Comment', 'Delete this comment?', async () => {
        const res = await ajaxPost('/rbac_project/api/delete_comment.php', { comment_id: commentId });
        if (res.success) {
            document.getElementById('cmt-' + commentId)?.remove();
            showToast('Comment deleted', 'success');
        } else {
            showToast(res.error || 'Failed to delete comment', 'error');
        }
    });
}

// ── Add comment ───────────────────────────────────────────────
async function addComment(e, postId) {
    e.preventDefault();
    const input = document.getElementById('cmtInput-' + postId);
    const text  = input.value.trim();
    if (!text) return;
    const res = await ajaxPost('/rbac_project/api/add_comment.php', { post_id: postId, content: text });
    if (res.success) {
        const c = res.comment;
        const list = document.getElementById('cmtList-' + postId);
        const div  = document.createElement('div');
        div.id = 'cmt-' + c.id;
        div.className = 'comment-item';
        div.innerHTML = `
            <div class="comment-avatar ${avatarClass(c.username)}">${c.username[0].toUpperCase()}</div>
            <div class="comment-content">
                <span class="comment-author">${escHtml(c.username)}</span>
                <span class="badge ${c.badge_class}" style="font-size:.65rem;margin-left:.35rem;">${escHtml(c.role_label)}</span>
                <p class="comment-text">${escHtml(c.content)}</p>
                <span class="comment-time">Just now</span>
            </div>
            ${c.can_delete ? `<button class="comment-del" onclick="deleteComment(${c.id})">✕ Delete</button>` : ''}
        `;
        list.appendChild(div);
        input.value = '';
        showToast('Comment added!', 'success');
    } else {
        showToast(res.error || 'Failed to add comment', 'error');
    }
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
