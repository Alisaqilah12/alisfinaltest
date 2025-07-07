<?php require_once 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mobile Application Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #eaf4fb;
        }
        .card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease;
            background-color: #ffffff;
        }
        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }
        .card-title {
            font-weight: 600;
            color: #004085;
        }
        .card-text, .text-muted {
            color: #495057;
        }
        .badge {
            font-size: 0.8rem;
        }
        .form-control::placeholder {
            font-style: italic;
        }
        h2.title-header {
            color: #003366;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-5 title-header">📱 <strong>Mobile Application Reviews</strong></h2>

    <!-- Search Bar -->
    <form method="GET" class="mb-4">
        <div class="input-group shadow-sm">
            <input type="text" name="search" class="form-control" placeholder="🔍 Search by title or category" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </div>
    </form>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="create.php" class="btn btn-success"><b>➕ Add New Review</b></a>
        <a href="export_pdf.php" class="btn btn-outline-danger"><b>📄 Export to PDF</b></a>
    </div>

    <!-- Reviews Grid -->
    <div class="row justify-content-center">
        <?php
        $query = "SELECT r.*, c.name AS category 
                  FROM reviews r 
                  JOIN categories c ON r.category_id = c.id";

        if (!empty($_GET['search'])) {
            $search = '%' . $_GET['search'] . '%';
            $stmt = $pdo->prepare($query . " WHERE r.title LIKE ? OR c.name LIKE ?");
            $stmt->execute([$search, $search]);
        } else {
            $stmt = $pdo->prepare($query);
            $stmt->execute();
        }

        while ($row = $stmt->fetch()) {
        ?>
        <div class="col-md-6 col-lg-4 mb-4 d-flex">
            <div class="card shadow-sm w-100">
                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="App Image" style="height: 200px; object-fit: cover; border-top-left-radius: 15px; border-top-right-radius: 15px;">

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
                    <p><strong>Status:</strong>
                        <?php if ($row['status'] === 'active'): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Created:</strong> <span class="text-muted"><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></span></p>

                    <div class="d-flex gap-2 mb-3">
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">✏️ Edit</a>
                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">🗑 Delete</a>
                    </div>

                    <hr>

                    <!-- Comments -->
                    <h6 class="text-muted mb-2">💬 Comments:</h6>
                    <div style="max-height: 100px; overflow-y: auto;">
                    <?php
                    $commentStmt = $pdo->prepare("SELECT * FROM comments WHERE review_id = ?");
                    $commentStmt->execute([$row['id']]);
                    while ($comment = $commentStmt->fetch()) {
                        echo "<p class='text-muted small fst-italic mb-1'>– " . htmlspecialchars($comment['comment']) . "</p>";
                    }
                    ?>
                    </div>

                    <!-- Add Comment -->
                    <form method="POST" action="add_comment.php" class="mt-2">
                        <div class="input-group input-group-sm">
                            <input type="hidden" name="review_id" value="<?= $row['id'] ?>">
                            <input type="text" name="comment" class="form-control" placeholder="Write a comment..." required>
                            <button type="submit" class="btn btn-outline-primary">Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

</body>
</html>
