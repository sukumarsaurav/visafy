<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if professional is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'professional') {
    header('Location: ../login.php');
    exit;
}

$professionalId = $_SESSION['user_id'];
$caseId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($caseId === 0) {
    header('Location: dashboard.php');
    exit;
}

// Fetch case details
$stmt = $pdo->prepare("
    SELECT ca.*, u.first_name, u.last_name, u.email, u.profile_image
    FROM case_applications ca
    JOIN users u ON ca.client_id = u.id
    JOIN professional_clients pc ON pc.client_id = ca.client_id
    WHERE ca.id = ? AND pc.professional_id = ?
");
$stmt->execute([$caseId, $professionalId]);
$case = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$case) {
    header('Location: dashboard.php');
    exit;
}

// Update case status if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $newStatus = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    
    $updateStmt = $pdo->prepare("UPDATE case_applications SET status = ? WHERE id = ?");
    $updateSuccess = $updateStmt->execute([$newStatus, $caseId]);
    
    if ($updateSuccess) {
        $successMessage = "Case status updated successfully.";
        $case['status'] = $newStatus;
    } else {
        $errorMessage = "Failed to update case status.";
    }
}

// Fetch case documents
$docStmt = $pdo->prepare("
    SELECT * FROM documents 
    WHERE case_id = ? 
    ORDER BY upload_date DESC
");
$docStmt->execute([$caseId]);
$documents = $docStmt->fetchAll(PDO::FETCH_ASSOC);

// Get notes related to this case
$notesStmt = $pdo->prepare("
    SELECT n.id, n.content, n.created_at, n.is_private, n.user_type, 
           CASE 
               WHEN n.user_type = 'professional' THEN p.name
               WHEN n.user_type = 'client' THEN u.name 
               ELSE 'System'
           END as author_name
    FROM case_notes n
    LEFT JOIN professionals p ON n.user_id = p.id AND n.user_type = 'professional'
    LEFT JOIN users u ON n.user_id = u.id AND n.user_type = 'client'
    WHERE n.case_id = ?
    ORDER BY n.created_at DESC
");
$notesStmt->execute([$caseId]);
$notes = $notesStmt->fetchAll(PDO::FETCH_ASSOC);

// Add a new note if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_note'])) {
    $noteContent = filter_input(INPUT_POST, 'note_content', FILTER_SANITIZE_STRING);
    $isPrivate = isset($_POST['is_private']) ? 1 : 0;
    
    if (!empty($noteContent)) {
        $addNoteStmt = $pdo->prepare("
            INSERT INTO case_notes (case_id, user_id, content, created_at, is_private, user_type)
            VALUES (?, ?, ?, NOW(), ?, 'professional')
        ");
        $noteAdded = $addNoteStmt->execute([$caseId, $professionalId, $noteContent, $isPrivate]);
        
        if ($noteAdded) {
            // Refresh page to show the new note
            header("Location: case-details.php?id=" . $caseId);
            exit;
        } else {
            $errorMessage = "Failed to add note. Please try again.";
        }
    } else {
        $errorMessage = "Note content cannot be empty.";
    }
}

// Function to get appropriate badge class for case status
function getCaseStatusBadgeClass($status) {
    switch ($status) {
        case 'new':
            return 'bg-primary';
        case 'in_progress':
            return 'bg-info';
        case 'pending_documents':
            return 'bg-warning';
        case 'review':
            return 'bg-secondary';
        case 'approved':
            return 'bg-success';
        case 'rejected':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

// Format case status for display
function formatCaseStatus($status) {
    return ucwords(str_replace('_', ' ', $status));
}

include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Case Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="client-details.php?id=<?php echo $case['client_id']; ?>" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back to Client
                    </a>
                </div>
            </div>
            
            <?php if (isset($successMessage)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($errorMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Case Overview</h5>
                                <span class="badge <?php echo getCaseStatusBadgeClass($case['status']); ?>">
                                    <?php echo formatCaseStatus($case['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Client Information</h6>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-wrapper me-3">
                                            <?php if ($case['profile_image']): ?>
                                                <img src="../../uploads/profile/<?php echo $case['profile_image']; ?>" class="rounded-circle" width="50" height="50" alt="Profile">
                                            <?php else: ?>
                                                <div class="avatar-placeholder rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <span class="text-white"><?php echo substr($case['first_name'], 0, 1) . substr($case['last_name'], 0, 1); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($case['first_name'] . ' ' . $case['last_name']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($case['email']); ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Case Information</h6>
                                    <p><strong>Visa Type:</strong> <?php echo htmlspecialchars($case['visa_type']); ?></p>
                                    <p><strong>Application Date:</strong> <?php echo date('F j, Y', strtotime($case['application_date'])); ?></p>
                                    <p><strong>Reference Number:</strong> <?php echo htmlspecialchars($case['reference_number']); ?></p>
                                </div>
                            </div>
                            
                            <form method="POST" class="mt-3">
                                <div class="row align-items-end">
                                    <div class="col-md-6">
                                        <label for="status" class="form-label">Update Status</label>
                                        <select class="form-select" name="status" id="status">
                                            <option value="new" <?php echo $case['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                            <option value="in_progress" <?php echo $case['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="pending_documents" <?php echo $case['status'] === 'pending_documents' ? 'selected' : ''; ?>>Pending Documents</option>
                                            <option value="review" <?php echo $case['status'] === 'review' ? 'selected' : ''; ?>>Under Review</option>
                                            <option value="approved" <?php echo $case['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="rejected" <?php echo $case['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Documents</h5>
                            <a href="upload-document.php?case_id=<?php echo $caseId; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-upload"></i> Upload Document
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (count($documents) > 0): ?>
                                <div class="list-group">
                                    <?php foreach ($documents as $document): ?>
                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($document['title']); ?></h6>
                                                <small class="text-muted">
                                                    Uploaded on <?php echo date('M j, Y', strtotime($document['upload_date'])); ?>
                                                </small>
                                            </div>
                                            <div>
                                                <a href="../../uploads/documents/<?php echo $document['file_path']; ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="../../uploads/documents/<?php echo $document['file_path']; ?>" download class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No documents have been uploaded for this case.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Case Notes</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="mb-3">
                                <div class="mb-3">
                                    <label for="note_content" class="form-label">Add Note</label>
                                    <textarea class="form-control" id="note_content" name="note_content" rows="3" required></textarea>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_private" name="is_private" value="1">
                                    <label class="form-check-label" for="is_private">
                                        Private Note (only visible to professionals)
                                    </label>
                                </div>
                                <button type="submit" name="add_note" class="btn btn-primary">Add Note</button>
                            </form>
                            
                            <hr>
                            
                            <!-- Display Notes -->
                            <h5 class="mt-4">Case Notes</h5>
                            <?php if (!empty($notes)): ?>
                                <div class="list-group mb-3">
                                    <?php foreach ($notes as $note): ?>
                                        <?php 
                                            $noteTypeClass = $note['user_type'] === 'professional' ? 'list-group-item-info' : ($note['user_type'] === 'client' ? 'list-group-item-warning' : 'list-group-item-light');
                                        ?>
                                        <div class="list-group-item <?php echo $noteTypeClass; ?>">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">
                                                    <?php echo htmlspecialchars($note['author_name']); ?> 
                                                    (<?php echo ucfirst($note['user_type']); ?>)
                                                    <?php if (isset($note['is_private']) && $note['is_private'] == 1): ?>
                                                        <span class="badge bg-danger">Private</span>
                                                    <?php endif; ?>
                                                </h6>
                                                <small><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($note['created_at']))); ?></small>
                                            </div>
                                            <p class="mb-1"><?php echo nl2br(htmlspecialchars($note['content'])); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p>No notes available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 