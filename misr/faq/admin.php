<?php
session_start();

// Basic authentication (you should implement proper authentication)
$admin_password = "admin123"; // Change this to a secure password

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["password"]) && $_POST["password"] === $admin_password) {
        $_SESSION["authenticated"] = true;
    }
    
    if (isset($_POST["action"]) && $_SESSION["authenticated"]) {
        $faqs = json_decode(file_get_contents("faqs.json"), true);
        $message = "";
        
        switch ($_POST["action"]) {
            case "add_category":
                if (isset($_POST["category_name"])) {
                    $new_category_id = 1;
                    if (!empty($faqs["categories"])) {
                        $max_category_id = max(array_column($faqs["categories"], 'id'));
                        $new_category_id = $max_category_id + 1;
                    }
                    $faqs["categories"][] = [
                        "id" => $new_category_id,
                        "name" => strip_tags($_POST["category_name"]),
                        "faqs" => []
                    ];
                    $message = "Category added successfully!";
                }
                break;

            case "edit_category":
                if (isset($_POST["category_id"]) && isset($_POST["category_name"])) {
                    foreach ($faqs["categories"] as &$category) {
                        if ($category["id"] == $_POST["category_id"]) {
                            $category["name"] = strip_tags($_POST["category_name"]);
                            $message = "Category updated successfully!";
                            break;
                        }
                    }
                }
                break;

            case "delete_category":
                if (isset($_POST["category_id"])) {
                    $general_category = null;
                    foreach ($faqs["categories"] as $key => $category) {
                        if ($category["id"] == $_POST["category_id"]) {
                            // Move FAQs to "General Information" category
                            foreach ($faqs["categories"] as &$cat) {
                                if (strtolower($cat["name"]) === "general information") {
                                    $general_category = &$cat;
                                    break;
                                }
                            }
                            if (!$general_category) {
                                $general_category_id = max(array_column($faqs["categories"], 'id')) + 1;
                                $general_category = [
                                    "id" => $general_category_id,
                                    "name" => "General Information",
                                    "faqs" => []
                                ];
                                $faqs["categories"][] = $general_category;
                            }
                            $general_category["faqs"] = array_merge($general_category["faqs"], $category["faqs"]);
                            unset($faqs["categories"][$key]);
                            $faqs["categories"] = array_values($faqs["categories"]);
                            $message = "Category deleted successfully!";
                            break;
                        }
                    }
                }
                break;

            case "reorder_categories":
                if (isset($_POST["order"])) {
                    $new_order = json_decode($_POST["order"], true);
                    $reordered_categories = [];
                    foreach ($new_order as $id) {
                        foreach ($faqs["categories"] as $category) {
                            if ($category["id"] == $id) {
                                $reordered_categories[] = $category;
                                break;
                            }
                        }
                    }
                    if (count($reordered_categories) === count($faqs["categories"])) {
                        $faqs["categories"] = $reordered_categories;
                        $message = "Category order updated successfully!";
                    }
                }
                break;

            case "add":
                if (isset($_POST["question"], $_POST["answer"], $_POST["category_id"])) {
                    foreach ($faqs["categories"] as &$category) {
                        if ($category["id"] == $_POST["category_id"]) {
                            $new_id = 1;
                            if (!empty($category["faqs"])) {
                                $max_id = max(array_column($category["faqs"], 'id'));
                                $new_id = $max_id + 1;
                            }
                            $category["faqs"][] = [
                                "id" => $new_id,
                                "question" => strip_tags($_POST["question"]),
                                "answer" => $_POST["answer"]
                            ];
                            $message = "FAQ added successfully!";
                            break;
                        }
                    }
                }
                break;

            case "edit":
                if (isset($_POST["id"]) && isset($_POST["question"]) && isset($_POST["answer"]) && isset($_POST["category_id"])) {
                    $faq_found = false;
                    foreach ($faqs["categories"] as &$category) {
                        foreach ($category["faqs"] as $key => &$faq) {
                            if ($faq["id"] == $_POST["id"]) {
                                // If category is changed, move the FAQ to the new category
                                if ($category["id"] != $_POST["category_id"]) {
                                    $faq_to_move = $faq;
                                    unset($category["faqs"][$key]);
                                    $category["faqs"] = array_values($category["faqs"]);
                                    foreach ($faqs["categories"] as &$new_category) {
                                        if ($new_category["id"] == $_POST["category_id"]) {
                                            $new_id = 1;
                                            if (!empty($new_category["faqs"])) {
                                                $max_id = max(array_column($new_category["faqs"], 'id'));
                                                $new_id = $max_id + 1;
                                            }
                                            $faq_to_move["id"] = $new_id;
                                            $faq_to_move["question"] = strip_tags($_POST["question"]);
                                            $faq_to_move["answer"] = $_POST["answer"];
                                            $new_category["faqs"][] = $faq_to_move;
                                            $faq_found = true;
                                            break;
                                        }
                                    }
                                } else {
                                    // Update FAQ in the same category
                                    $faq["question"] = strip_tags($_POST["question"]);
                                    $faq["answer"] = $_POST["answer"];
                                    $faq_found = true;
                                }
                                break 2;
                            }
                        }
                    }
                    if ($faq_found) {
                        $message = "FAQ updated successfully!";
                    }
                }
                break;

            case "delete":
                if (isset($_POST["id"])) {
                    foreach ($faqs["categories"] as &$category) {
                        foreach ($category["faqs"] as $key => $faq) {
                            if ($faq["id"] == $_POST["id"]) {
                                unset($category["faqs"][$key]);
                                $category["faqs"] = array_values($category["faqs"]);
                                $message = "FAQ deleted successfully!";
                                break 2;
                            }
                        }
                    }
                }
                break;

            case "reorder":
                if (isset($_POST["order"])) {
                    $new_order = json_decode($_POST["order"], true);
                    foreach ($faqs["categories"] as &$category) {
                        $reordered_faqs = [];
                        foreach ($new_order as $id) {
                            foreach ($category["faqs"] as $faq) {
                                if ($faq["id"] == $id) {
                                    $reordered_faqs[] = $faq;
                                    break;
                                }
                            }
                        }
                        if (count($reordered_faqs) === count($category["faqs"])) {
                            $category["faqs"] = $reordered_faqs;
                            $message = "FAQ order updated successfully!";
                        }
                    }
                }
                break;
        }
        
        if (!empty($message)) {
            file_put_contents("faqs.json", json_encode($faqs, JSON_PRETTY_PRINT));
        }
    }
}

$faqs = json_decode(file_get_contents("faqs.json"), true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/qg3gqb4jaz7209tg81l07vfx37gpymza4wcub94f3jfm67uy/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <style>
        .admin-header {
            background-color: #8B0000;
            color: #ffffff;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .faq-item {
            border: 1px solid #ddd;
            margin-bottom: 1rem;
            border-radius: 5px;
            background: white;
            cursor: move;
        }
        .faq-item.dragging {
            opacity: 0.5;
            background: #f8f9fa;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .modal-content {
            border-radius: 10px;
        }
        .sortable-ghost {
            opacity: 0.5;
            background: #f8f9fa;
        }
        .handle {
            cursor: move;
            padding: 0 10px;
            color: #666;
        }
        .category-item {
            cursor: move;
        }
    </style>
</head>
<body>
    <div class="admin-header text-center">
        <h1>FAQ Admin Panel</h1>
    </div>

    <div class="container mt-4">
        <?php if (!isset($_SESSION["authenticated"])): ?>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="password" class="form-label">Admin Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <?php if (isset($message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Manage FAQs</h2>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                        <i class="bi bi-plus-circle"></i> Add New QA
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bi bi-plus-circle"></i> Add New Category
                    </button>
                </div>
            </div>

            <div id="categoryList" class="mb-4">
                <?php foreach ($faqs["categories"] as $category): ?>
                    <div class="category-item mb-3" data-id="<?php echo $category["id"]; ?>">
                        <h3>
                            <?php echo htmlspecialchars($category["name"]); ?>
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editCategoryModal"
                                    data-id="<?php echo $category["id"]; ?>"
                                    data-name="<?php echo htmlspecialchars($category["name"]); ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteCategoryModal"
                                    data-id="<?php echo $category["id"]; ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </h3>
                        <div class="list-group" id="faqList_<?php echo $category["id"]; ?>">
                            <?php foreach ($category["faqs"] as $faq): ?>
                            <div class="list-group-item faq-item" data-id="<?php echo $faq["id"]; ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="handle">
                                            <i class="bi bi-grip-vertical"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1"><?php echo htmlspecialchars($faq["question"]); ?></h5>
                                            <div class="mb-1"><?php echo $faq["answer"]; ?></div>
                                        </div>
                                    </div>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editFaqModal"
                                                data-id="<?php echo $faq["id"]; ?>"
                                                data-question="<?php echo htmlspecialchars($faq["question"]); ?>"
                                                data-answer="<?php echo htmlspecialchars($faq["answer"]); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteFaqModal"
                                                data-id="<?php echo $faq["id"]; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Add Category Modal -->
            <div class="modal fade" id="addCategoryModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Category</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="action" value="add_category">
                                <div class="mb-3">
                                    <label for="category_name" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="category_name" name="category_name" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Category</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Category Modal -->
            <div class="modal fade" id="editCategoryModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Category</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="action" value="edit_category">
                                <input type="hidden" name="category_id" id="edit_category_id">
                                <div class="mb-3">
                                    <label for="edit_category_name" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Category Modal -->
            <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete Category</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="action" value="delete_category">
                                <input type="hidden" name="category_id" id="delete_category_id">
                                <p>Are you sure you want to delete this category? All FAQs will be moved to "General Information".</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add FAQ Modal -->
            <div class="modal fade" id="addFaqModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New FAQ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="action" value="add">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <?php foreach ($faqs["categories"] as $category): ?>
                                            <option value="<?php echo $category["id"]; ?>"><?php echo htmlspecialchars($category["name"]); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="question" class="form-label">Question</label>
                                    <input type="text" class="form-control" id="question" name="question" required>
                                </div>
                                <div class="mb-3">
                                    <label for="answer" class="form-label">Answer</label>
                                    <textarea class="form-control" id="answer" name="answer"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add FAQ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit FAQ Modal -->
            <div class="modal fade" id="editFaqModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit FAQ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" id="editFaqForm">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" id="edit_id">
                                <div class="mb-3">
                                    <label for="edit_category_id" class="form-label">Category</label>
                                    <select class="form-control" id="edit_category_id" name="category_id" required>
                                        <?php foreach ($faqs["categories"] as $category): ?>
                                            <option value="<?php echo $category["id"]; ?>"><?php echo htmlspecialchars($category["name"]); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_question" class="form-label">Question</label>
                                    <input type="text" class="form-control" id="edit_question" name="question" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_answer" class="form-label">Answer</label>
                                    <textarea class="form-control" id="edit_answer" name="answer"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete FAQ Modal -->
            <div class="modal fade" id="deleteFaqModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Delete FAQ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" id="delete_id">
                                <p>Are you sure you want to delete this FAQ?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '#answer, #edit_answer',
            plugins: 'lists link image table code help wordcount',
            toolbar: 'undo redo | blocks | ' +
                    'bold italic underline strikethrough | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist outdent indent | ' +
                    'link image | ' +
                    'removeformat help',
            height: 300,
            menubar: false,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save(); // Ensure content is saved to textarea
                });
            }
        });

        // Initialize Sortable for categories
        const categoryList = document.getElementById('categoryList');
        new Sortable(categoryList, {
            handle: 'h3',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                const order = Array.from(categoryList.children).map(item => item.dataset.id);
                const formData = new FormData();
                formData.append('action', 'reorder_categories');
                formData.append('order', JSON.stringify(order));
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                }).then(response => {
                    location.reload(); // Reload to show updated order
                });
            }
        });

        // Initialize Sortable for FAQs
        <?php foreach ($faqs["categories"] as $category): ?>
        const faqList_<?php echo $category["id"]; ?> = document.getElementById('faqList_<?php echo $category["id"]; ?>');
        new Sortable(faqList_<?php echo $category["id"]; ?>, {
            handle: '.handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                const order = Array.from(faqList_<?php echo $category["id"]; ?>.children).map(item => item.dataset.id);
                const formData = new FormData();
                formData.append('action', 'reorder');
                formData.append('order', JSON.stringify(order));
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                }).then(response => {
                    location.reload(); // Reload to show updated order
                });
            }
        });
        <?php endforeach; ?>

        // Add FAQ form submission
        document.querySelector('#addFaqModal form').addEventListener('submit', function(e) {
            e.preventDefault();
            // Make sure TinyMCE content is updated
            tinymce.triggerSave();
            
            const formData = new FormData(this);
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(response => {
                location.reload(); // Reload to show new FAQ
            });
        });

        // Edit FAQ form submission with validation
        document.getElementById('editFaqForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Ensure TinyMCE content is saved to the textarea
            tinymce.triggerSave();

            const answer = document.getElementById('edit_answer').value.trim();
            if (!answer) {
                alert('Answer is required.');
                return;
            }

            const formData = new FormData(this);
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(response => {
                location.reload(); // Reload to show updated FAQ
            });
        });

        // Delete FAQ form submission
        document.querySelector('#deleteFaqModal form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(response => {
                location.reload(); // Reload to show updated list
            });
        });

        // Edit FAQ Modal
        document.getElementById('editFaqModal').addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const question = button.getAttribute('data-question');
            const answer = button.getAttribute('data-answer');
            const categoryId = button.closest('.category-item').getAttribute('data-id');
            
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_question').value = question;
            document.getElementById('edit_category_id').value = categoryId;
            
            // Wait for TinyMCE to be ready
            setTimeout(() => {
                tinymce.get('edit_answer').setContent(answer);
            }, 100);
        });

        // Delete FAQ Modal
        document.getElementById('deleteFaqModal').addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            document.getElementById('delete_id').value = id;
        });

        // Edit Category Modal
        document.getElementById('editCategoryModal').addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            
            document.getElementById('edit_category_id').value = id;
            document.getElementById('edit_category_name').value = name;
        });

        // Delete Category Modal
        document.getElementById('deleteCategoryModal').addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            document.getElementById('delete_category_id').value = id;
        });
    </script>
</body>
</html>