<?php
// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Product</title>

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <div class="mb-3">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control" maxlength="100" size="100" />
                        </div>
                        <div class="mb-3">
                            <label for="">Price</label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" maxlength="20" size="20" />
                        </div>
                        <div class="mb-3">
                            <label for="type">Choose a type:</label>
                            <select name="type" id="type">
                                <option value="Books">Books</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Toys">Toys</option>
                            </select>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="activeCheck">
                            <label class="form-check-label" for="activeCheck">Active</label><br><br><br>
                        </div>
                        <div class="card" style="width: 18rem;">
                            <button type="button" id="create" class="btn btn-primary btn-lg">Create Product</button>
                        </div><br>
                        <div class="card" style="width: 18rem;">
                            <button type="button" id="back" class="btn btn-secondary btn-lg">Back to Product</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

    <script>
        $('#create').click(function() {
            isValid = true
            var name = $('#name').val();
            var price = $('#price').val();
            var type = $('#type').val();
            var isActive = $('#activeCheck').is(':checked');

            if (name == "" || price == "") {
                alert("Name and Price must be filled out");
                isValid = false;
            }

            if (isValid) {
                var url = "http://localhost:5275/api/Product/CreateProduct/" + name + "/" + price + "/" + type + "/" + isActive;
                CreateProduct("POST", url)
                reloadPage()
            } else {
                alert("product created failed, please check input")
            }

        });
        $('#back').click(function() {
            window.location.href = 'product.php';
        });

        function CreateProduct(method, url) {
            $.ajax({
                type: method,
                url: url,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert("product created!")
                }
            });
        }

        function reloadPage() {
            window.location.href = 'product.php';
        };
    </script>
</body>

</html>