<?php
$faqs_json = file_get_contents("faqs.json");
$categories = json_decode($faqs_json, true)["categories"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cairo Faiz FAQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background-color: #8B0000;
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .navbar-nav .nav-link {
            color: #ffffff;
        }
        .accordion-button {
            background-color: #f2f2f2;
            color: #333;
        }
        .accordion-button:not(.collapsed) {
            color: #ffffff;
            background-color: #a52a2a;
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
        }
        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(139, 0, 0, 0.25);
        }
        .form-control:focus {
            border-color: #a52a2a;
            box-shadow: 0 0 0 0.25rem rgba(139, 0, 0, 0.25);
        }
        .faq-header {
            background-color: #8B0000;
            color: #ffffff;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .faq-header h1 {
            margin-bottom: 0;
        }
        .accordion-body {
            padding: 1.25rem;
            line-height: 1.6;
        }
        .accordion-body p {
            margin-bottom: 1rem;
        }
        .accordion-body ul, .accordion-body ol {
            margin-bottom: 1rem;
            padding-left: 2rem;
        }
        .accordion-body strong {
            font-weight: 600;
        }
        .accordion-body em {
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="faq-header text-center">
        <h1>Cairo Faiz - Frequently Asked Questions</h1>
    </div>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-8 offset-md-2">
                <input type="text" id="faqSearch" class="form-control" placeholder="Search FAQs...">
            </div>
        </div>

        <?php foreach ($categories as $category): ?>
        <div class="mb-5">
            <h2 class="text-center mb-4"><?php echo htmlspecialchars($category['name']); ?></h2>
            <div class="accordion" id="accordionCategory<?php echo $category['id']; ?>">
                <?php foreach ($category['faqs'] as $faq): ?>
                <div class="accordion-item faq-item">
                    <h2 class="accordion-header" id="heading<?php echo $faq['id']; ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapse<?php echo $faq['id']; ?>" 
                                aria-expanded="false" 
                                aria-controls="collapse<?php echo $faq['id']; ?>">
                            <?php echo htmlspecialchars($faq['question']); ?>
                        </button>
                    </h2>
                    <div id="collapse<?php echo $faq['id']; ?>" 
                         class="accordion-collapse collapse" 
                         aria-labelledby="heading<?php echo $faq['id']; ?>" 
                         data-bs-parent="#accordionCategory<?php echo $category['id']; ?>">
                        <div class="accordion-body">
                            <?php echo $faq['answer']; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <div id="noResults" class="alert alert-warning mt-4" role="alert" style="display: none;">
            No FAQs found matching your search.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const faqSearchInput = document.getElementById('faqSearch');
        const faqItems = document.querySelectorAll('.faq-item');
        const categories = document.querySelectorAll('.mb-5'); // Each category container
        const noResultsDiv = document.getElementById('noResults');

        faqSearchInput.addEventListener('keyup', function(event) {
            const searchTerm = event.target.value.toLowerCase();
            let itemsFound = false;

            categories.forEach(category => {
                const faqItemsInCategory = category.querySelectorAll('.faq-item');
                let categoryHasResults = false;

                faqItemsInCategory.forEach(item => {
                    const question = item.querySelector('.accordion-button').textContent.toLowerCase();
                    const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
                    const isMatch = question.includes(searchTerm) || answer.includes(searchTerm);

                    if (isMatch) {
                        item.style.display = '';
                        categoryHasResults = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (categoryHasResults) {
                    category.style.display = '';
                    itemsFound = true;
                } else {
                    category.style.display = 'none';
                }
            });

            if (!itemsFound && searchTerm !== '') {
                noResultsDiv.style.display = 'block';
            } else {
                noResultsDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>