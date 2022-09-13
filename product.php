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
             <div class="col-md-12">
                 <div class="card">
                     <div class="card-header">
                         <h4>Product List
                             <button type="button" id="createProductBtn" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#productAddModal">
                                 Add Product
                             </button>
                         </h4>
                     </div>
                     <div class="card-body">

                         <table id="productTable" class="table table-bordered table-striped">
                             <thead>
                                 <tr>
                                     <th>Id</th>
                                     <th>Name</th>
                                     <th>Price</th>
                                     <th>Type</th>
                                     <th>Active</th>
                                 </tr>
                             </thead>
                             <tbody id="table-body">
                             </tbody>
                         </table>
                     </div>
                     <div class="container ">
                         <div id="pagination-wrapper"></div>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
     <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

     <script>
         var orderByAsc = true;
         $('#createProductBtn').click(function() {
             window.location.href = 'create.php';
         });


         $('#productTable thead').on('click', 'th', function() {
             orderByAsc = !orderByAsc
             var sortBy = this.firstChild.data
             var url = "http://localhost:5275/api/Product/GetAllProduct/" + orderByAsc.toString() + "/" + sortBy
             postDataWithCallback("GET", url)

         });

         $('table').on('click', 'input[type="button"]', function(e) {
             e.preventDefault();
             var answer = confirm('Do you want to delete?');
             if (answer) {
                 var id = $(this).closest("tr").find(".idRow").text()
                 var url = "http://localhost:5275/api/Product/Delete/" + id
                 postDataWithCallback("POST", url)
                 alert('Deleted');
                 reloadPage()
             } else {
                 alert('Not Deleted');
             }
         })

         function postDataWithCallback(method, url) {
             $.ajax({
                 type: method,
                 url: url,
                 processData: false,
                 contentType: false,
                 success: function(response) {
                     var state = {
                         'querySet': response,
                         'page': 1,
                         'rows': 5,
                         'window': 5,
                     }

                     $('#productTable tbody').empty();
                     buildTable()

                     function pagination(querySet, page, rows) {

                         var trimStart = (page - 1) * rows
                         var trimEnd = trimStart + rows

                         var trimmedData = querySet.slice(trimStart, trimEnd)

                         var pages = Math.round(querySet.length / rows);

                         return {
                             'querySet': trimmedData,
                             'pages': pages,
                         }
                     }

                     function pageButtons(pages) {
                         var wrapper = document.getElementById('pagination-wrapper')

                         wrapper.innerHTML = ``
                         console.log('Pages:', pages)

                         var maxLeft = (state.page - Math.floor(state.window / 2))
                         var maxRight = (state.page + Math.floor(state.window / 2))

                         if (maxLeft < 1) {
                             maxLeft = 1
                             maxRight = state.window
                         }

                         if (maxRight > pages) {
                             maxLeft = pages - (state.window - 1)

                             if (maxLeft < 1) {
                                 maxLeft = 1
                             }
                             maxRight = pages
                         }

                         for (var page = maxLeft; page <= maxRight; page++) {
                             wrapper.innerHTML += `<button value=${page} class="page btn btn-sm btn-info">${page}</button>`
                         }

                         if (state.page != 1) {
                             wrapper.innerHTML = `<button value=${1} class="page btn btn-sm btn-info">&#171; First</button>` + wrapper.innerHTML
                         }

                         if (state.page != pages) {
                             wrapper.innerHTML += `<button value=${pages} class="page btn btn-sm btn-info">Last &#187;</button>`
                         }

                         $('.page').on('click', function() {
                             $('#table-body').empty()

                             state.page = Number($(this).val())

                             buildTable()
                         })

                     }

                     function buildTable() {
                         var table = $('#table-body')
                         var data = pagination(state.querySet, state.page, state.rows)
                         var myList = data.querySet

                         for (var i = 1 in myList) {
                             //Keep in mind we are using "Template Litterals to create rows"
                             var row = `<tr>
                  <td class="idRow">${myList[i].id}</td>
                  <td >${myList[i].name}</td>
                  <td>${myList[i].price}</td>
                  <td>${myList[i].type}</td>
                  <td>${myList[i].active}</td>
                  <td><input type="button" value="Delete"></td>
                  `
                             table.append(row)
                         }

                         pageButtons(data.pages)
                     }


                 }
             });
         }

         function reloadPage() {
             var url = "http://localhost:5275/api/Product/GetAllProduct/true"
             postDataWithCallback("GET", url)
         };

         $(document).ready(function() {
             reloadPage()
         });

         $(document).on('submit', '#saveProduct', function(e) {
             e.preventDefault();
             $.ajax({
                 type: "GET",
                 url: "http://localhost:5275/api/Product/GetAllProduct",
                 processData: false,
                 contentType: false,
                 success: function(response) {
                     var res = jQuery.parseJSON(response);
                     if (res.status == 422) {
                         $('#errorMessage').removeClass('d-none');
                         $('#errorMessage').text(res.message);

                     } else if (res.status == 200) {

                         $('#errorMessage').addClass('d-none');
                         $('#productAddModal').modal('hide');
                         $('#saveProduct')[0].reset();

                         alertify.set('notifier', 'position', 'top-right');
                         alertify.success(res.message);

                         $('#myTable').load(location.href + " #myTable");

                     } else if (res.status == 500) {
                         alert(res.message);
                     }
                 }
             });

         });

         $(document).on('click', '.editStudentBtn', function() {

             var product_id = $(this).val();

             $.ajax({
                 type: "GET",
                 url: "code.php?product_id=" + product_id,
                 success: function(response) {

                     var res = jQuery.parseJSON(response);
                     if (res.status == 404) {

                         alert(res.message);
                     } else if (res.status == 200) {

                         $('#product_id').val(res.data.id);
                         $('#name').val(res.data.name);
                         $('#email').val(res.data.email);
                         $('#phone').val(res.data.phone);
                         $('#course').val(res.data.course);

                         $('#productEditModal').modal('show');
                     }
                 }
             });

         });

         $(document).on('submit', '#updateProduct', function(e) {
             e.preventDefault();

             var formData = new FormData(this);
             formData.append("update_student", true);

             $.ajax({
                 type: "POST",
                 url: "code.php",
                 data: formData,
                 processData: false,
                 contentType: false,
                 success: function(response) {

                     var res = jQuery.parseJSON(response);
                     if (res.status == 422) {
                         $('#errorMessageUpdate').removeClass('d-none');
                         $('#errorMessageUpdate').text(res.message);

                     } else if (res.status == 200) {

                         $('#errorMessageUpdate').addClass('d-none');

                         alertify.set('notifier', 'position', 'top-right');
                         alertify.success(res.message);

                         $('#productEditModal').modal('hide');
                         $('#updateProduct')[0].reset();

                         $('#myTable').load(location.href + " #myTable");

                     } else if (res.status == 500) {
                         alert(res.message);
                     }
                 }
             });

         });

         $(document).on('click', '.viewProductBtn', function() {

             var product_id = $(this).val();
             $.ajax({
                 type: "GET",
                 url: "code.php?product_id=" + product_id,
                 success: function(response) {

                     var res = jQuery.parseJSON(response);
                     if (res.status == 404) {

                         alert(res.message);
                     } else if (res.status == 200) {

                         $('#view_name').text(res.data.name);
                         $('#view_email').text(res.data.email);
                         $('#view_phone').text(res.data.phone);
                         $('#view_course').text(res.data.course);

                         $('#productViewModal').modal('show');
                     }
                 }
             });
         });

         $(document).on('click', '.deleteProductBtn', function(e) {
             e.preventDefault();

             if (confirm('Are you sure you want to delete this product?')) {
                 var product_id = $(this).val();
                 $.ajax({
                     type: "POST",
                     url: "code.php",
                     data: {
                         'delete_student': true,
                         'product_id': product_id
                     },
                     success: function(response) {

                         var res = jQuery.parseJSON(response);
                         if (res.status == 500) {

                             alert(res.message);
                         } else {
                             alertify.set('notifier', 'position', 'top-right');
                             alertify.success(res.message);

                             $('#myTable').load(location.href + " #myTable");
                         }
                     }
                 });
             }
         });
     </script>

 </body>

 </html>