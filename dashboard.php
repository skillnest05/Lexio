<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$initials = strtoupper(substr($_SESSION['user_name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Lexio</title>
    <meta name="description" content="Draft professional AI-powered emails with Lexio.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ═══════════ NAVBAR ═══════════ -->
<nav class="app-navbar">
    <div class="nav-brand">✦ Lexio</div>
    <div class="nav-user">
        <div class="user-avatar"><?= htmlspecialchars($initials) ?></div>
        <span class="d-none d-sm-inline" style="font-size:.88rem;font-weight:600;color:#475569;">
            <?= htmlspecialchars($_SESSION['user_name']) ?>
        </span>
        <button class="btn-logout" id="logoutBtn"><i class="bi bi-box-arrow-right me-1"></i>Sign Out</button>
    </div>
</nav>

<!-- ═══════════ DASHBOARD LAYOUT ═══════════ -->
<div class="dashboard-layout">

    <!-- ── LEFT: Input Form ── -->
    <div class="panel">
        <div class="panel-header">
            <i class="bi bi-pencil-square"></i> Compose Brief
        </div>
        <div class="panel-body d-flex flex-column gap-3">

            <!-- Alert -->
            <div id="genError" class="alert-banner"></div>

            <!-- Sender / Recipient -->
            <div>
                <div class="input-group-label">People</div>
                <div class="names-row">
                    <div>
                        <label class="form-label" for="senderName">From (Sender)</label>
                        <input type="text" id="senderName" class="form-control" placeholder="Your name">
                    </div>
                    <div>
                        <label class="form-label" for="recipientName">To (Recipient)</label>
                        <input type="text" id="recipientName" class="form-control" placeholder="Recipient's name">
                    </div>
                </div>
            </div>

            <!-- Tone -->
            <div>
                <div class="input-group-label">Tone</div>
                <input type="hidden" id="selectedTone" value="professional">
                <div class="tone-chips">
                    <span class="tone-chip active" data-value="professional"><i class="bi bi-briefcase me-1"></i>Professional</span>
                    <span class="tone-chip"         data-value="casual"><i class="bi bi-emoji-smile me-1"></i>Casual</span>
                    <span class="tone-chip"         data-value="persuasive"><i class="bi bi-megaphone me-1"></i>Persuasive</span>
                </div>
            </div>

            <!-- Length -->
            <div>
                <div class="input-group-label">Length</div>
                <input type="hidden" id="selectedLength" value="medium">
                <div class="length-chips">
                    <span class="length-chip"       data-value="short">Short</span>
                    <span class="length-chip active" data-value="medium">Medium</span>
                    <span class="length-chip"       data-value="long">Long</span>
                </div>
            </div>

            <!-- Description -->
            <div class="d-flex flex-column" style="flex:1">
                <div class="input-group-label">Email Description / Key Points</div>
                <textarea
                    id="description"
                    class="description-area"
                    style="flex:1; min-height:150px;"
                    placeholder="Describe what the email should say&#10;e.g. Follow up on Monday's meeting about the Q3 budget. Ask if they've reviewed the proposal and if they need any changes."></textarea>
            </div>

            <button class="btn-generate" id="generateBtn">
                <i class="bi bi-stars"></i> Generate Email
            </button>

        </div>
    </div>

    <!-- ── RIGHT: Output ── -->
    <div class="panel" id="outputPanel">
        <div class="panel-header">
            <i class="bi bi-envelope-check"></i> Generated Draft
        </div>
        <div class="panel-body d-flex flex-column">

            <!-- Subject Line -->
            <div class="subject-display">
                <span class="subject-label">Subject:</span>
                <span id="subjectText" style="color:#475569;font-weight:500;">Your subject line will appear here.</span>
            </div>

            <!-- Email Body -->
            <textarea id="emailBodyOutput" class="form-control" style="flex:1;min-height:320px;font-size:.9rem;line-height:1.7;" placeholder="Your generated email will appear here. You can edit it freely before copying." readonly></textarea>

            <!-- Actions -->
            <div class="output-actions flex-wrap">
                <button class="btn-action" id="copySubjectBtn">
                    <i class="bi bi-clipboard"></i> Copy Subject
                </button>
                <button class="btn-action" id="copyBodyBtn">
                    <i class="bi bi-clipboard-check"></i> Copy Body
                </button>
                <button class="btn-action" id="copyAllBtn">
                    <i class="bi bi-copy"></i> Copy Full Email
                </button>
                <button class="btn-action ms-auto" onclick="document.getElementById('emailBodyOutput').removeAttribute('readonly'); document.getElementById('emailBodyOutput').focus();">
                    <i class="bi bi-pencil"></i> Edit
                </button>
            </div>

        </div>
    </div>

    <!-- ── HISTORY SIDEBAR ── -->
    <div class="panel history-panel">
        <div class="panel-header">
            <i class="bi bi-clock-history"></i> Recent Drafts
        </div>
        <div class="panel-body" id="historyContainer">
            <div class="empty-state">
                <i class="bi bi-envelope-open"></i>
                <p class="small mb-0">No drafts yet.<br>Generate your first email!</p>
            </div>
        </div>
    </div>

</div>

<script src="js/app.js"></script>
</body>
</html>
