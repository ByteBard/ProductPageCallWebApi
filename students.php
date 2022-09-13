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

    echo "You have CORS!";
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

     <!-- Add Product -->
     <div class="modal fade" id="productAddModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">Add Product</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <form id="saveProduct">
                     <div class="modal-body">

                         <div id="errorMessage" class="alert alert-warning d-none"></div>

                         <div class="mb-3">
                             <label for="">Name</label>
                             <input type="text" name="name" class="form-control" />
                         </div>
                         <div class="mb-3">
                             <label for="">Price</label>
                             <input type="text" name="email" class="form-control" />
                         </div>
                         <div class="mb-3">
                             <label for="">Type</label>
                             <input type="text" name="phone" class="form-control" />
                         </div>
                         <div class="mb-3">
                             <label for="">Course</label>
                             <input type="text" name="course" class="form-control" />
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-primary">Save Product</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>

     <!-- Edit Product Modal -->
     <div class="modal fade" id="productEditModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">Edit Product</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <form id="updateProduct">
                     <div class="modal-body">

                         <div id="errorMessageUpdate" class="alert alert-warning d-none"></div>

                         <input type="hidden" name="product_id" id="product_id">

                         <div class="mb-3">
                             <label for="">Name</label>
                             <input type="text" name="name" id="name" class="form-control" />
                         </div>
                         <div class="mb-3">
                             <label for="">Email</label>
                             <input type="text" name="email" id="email" class="form-control" />
                         </div>
                         <div class="mb-3">
                             <label for="">Phone</label>
                             <input type="text" name="phone" id="phone" class="form-control" />
                         </div>
                         <div class="mb-3">
                             <label for="">Course</label>
                             <input type="text" name="course" id="course" class="form-control" />
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-primary">Update Product</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>

     <!-- View Product Modal -->
     <div class="modal fade" id="productViewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">View Product</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">

                     <div class="mb-3">
                         <label for="">Name</label>
                         <p id="view_name" class="form-control"></p>
                     </div>
                     <div class="mb-3">
                         <label for="">Email</label>
                         <p id="view_email" class="form-control"></p>
                     </div>
                     <div class="mb-3">
                         <label for="">Phone</label>
                         <p id="view_phone" class="form-control"></p>
                     </div>
                     <div class="mb-3">
                         <label for="">Course</label>
                         <p id="view_course" class="form-control"></p>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 </div>
             </div>
         </div>
     </div>

     <div class="container mt-4">
         <div class="row">
             <div class="col-md-12">
                 <div class="card">
                     <div class="card-header">
                         <h4>Product List

                             <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#productAddModal">
                                 Add Product
                             </button>
                         </h4>
                     </div>
                     <div class="card-body">

                         <table id="myTable" class="table table-bordered table-striped">
                             <thead>
                                 <tr>
                                     <th>ID</th>
                                     <th>Name</th>
                                     <th>Email</th>
                                     <th>Phone</th>
                                     <th>Course</th>
                                     <th>Action</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php
                                    require 'dbcon.php';

                                    $query = "SELECT * FROM students";
                                    $query_run = mysqli_query($con, $query);

                                    if (mysqli_num_rows($query_run) > 0) {
                                        foreach ($query_run as $student) {
                                    ?>
                                         <tr>
                                             <td><?= $student['id'] ?></td>
                                             <td><?= $student['name'] ?></td>
                                             <td><?= $student['email'] ?></td>
                                             <td><?= $student['phone'] ?></td>
                                             <td><?= $student['course'] ?></td>
                                             <td>
                                                 <button type="button" value="<?= $student['id']; ?>" class="viewProductBtn btn btn-info btn-sm">View</button>
                                                 <button type="button" value="<?= $student['id']; ?>" class="editStudentBtn btn btn-success btn-sm">Edit</button>
                                                 <button type="button" value="<?= $student['id']; ?>" class="deleteProductBtn btn btn-danger btn-sm">Delete</button>
                                             </td>
                                         </tr>
                                 <?php
                                        }
                                    }
                                    ?>

                             </tbody>
                         </table>

                     </div>
                 </div>
             </div>
         </div>
     </div>


     <div class="container mt-4">
         <div class="row">
             <div class="col-md-12">
                 <div class="card">
                     <div class="card-header">
                         <h4>Product List
                             <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#productAddModal">
                                 Add Product
                             </button>
                         </h4>
                     </div>
                     <div class="card-body">

                         <table id="productTable" class="table table-bordered table-striped">
                             <thead>
                                 <tr>
                                     <th>Name</th>
                                     <th>Price</th>
                                     <th>Type</th>
                                 </tr>
                             </thead>
                             <tbody id="table-body">
                             </tbody>
                         </table>
                         <!-- <div class="pagination-container">
                             <nav>
                                 <ul class="pagination"></ul>
                             </nav>
                         </div> -->
                     </div>
                     <div class="container ">
                         <div id="pagination-wrapper"></div>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <!-- <div class="container" style="margin-top:40px">
         <h4>Select row Number for each page</h4>
         <div class="form-group">
             <select name="state" id="maxRows" class="form-control" style="width:180px;">
                 <option value="10000">Show All</option>
                 <option value="5">5</option>
                 <option value="10">10</option>
                 <option value="20">20</option>
                 <option value="10000">Show All</option>
             </select>
         </div>
     </div> -->


     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

     <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

     <script>
         $(document).ready(function() {
             $.ajax({
                 type: "GET",
                 url: "http://localhost:5275/api/Product/GetAllProduct",
                 processData: false,
                 contentType: false,
                 success: function(response) {
                     var state = {
                         'querySet': response,
                         'page': 1,
                         'rows': 5,
                         'window': 5,
                     }

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
                  <td>${myList[i].name}</td>
                  <td>${myList[i].price}</td>
                  <td>${myList[i].type}</td>
                  `
                             table.append(row)
                         }

                         pageButtons(data.pages)
                     }


                 }
             });
         });





         //  var table = "#productTable"
         //  $('#maxRows').on('change', function() {
         //      $('.pagination').html('')
         //      var trnum = 0
         //      var maxRows = parseInt($(this).val())
         //      var totalRows = $(table + ' tbody tr').length
         //      $(table + ' tr:gt(0)').each(function() {
         //          trnum++
         //          if (trnum > maxRows) {
         //              $(this).hide()
         //          }
         //          if (trnum <= maxRows) {
         //              $(this).show()
         //          }
         //      })
         //      if (totalRows > maxRows) {
         //          var pageNum = Math.cell(totalRows / maxRows)
         //          for (var i = 1; i <= pagenum;) {
         //              $('.pagination').append('<li data-page="' + i + '">\<span>' + i++ +
         //                  '<span class="sr-only">(current)</span></span>\</li>').show()
         //          }
         //      }
         //      $('.pagination li:first-child').addClass('active')
         //      $('.pagination li').on('click', function() {
         //          var pageNum = $(this).attr('data-page')
         //          var triIndex = 0;
         //          $('.pagination li').removeClass('active')
         //          $(this).addClass('active')
         //          $(table + ' tr:gt(0)').each(function() {
         //              triIndex++
         //              if (triIndex > (maxRows * pageNum) || triIndex <= ((maxRows * pageNum) - maxRows)) {
         //                  $(this).hide()
         //              } else {
         //                  $(this).show()
         //              }
         //          })
         //      })

         //      $(function() {
         //          $('table tr:eq(0)').prepend('<th>ID</th>')
         //          var id = 0;
         //          $('table tr:gt(0)').each(function() {
         //              id++
         //              $(this).prepend('<td>' + id + '</td>')
         //          })
         //      })
         //  })



         //  $(document).ready(function() {
         //      $.ajax({
         //          type: "GET",
         //          url: "http://localhost:5275/api/Product/GetAllProduct",
         //          processData: false,
         //          contentType: false,
         //          success: function(response) {
         //              $.each(response, function(index, rec) {
         //                  $("#productTable tbody").append("<tr>" +
         //                      "<td>" + rec.name + "</td>" +
         //                      "<td>" + rec.price + "</td>" +
         //                      "<td>" + rec.type + "</td>" +
         //                      "</tr>");
         //              });
         //          }
         //      });
         //  });

         $(document).on('submit', '#saveProduct', function(e) {
             e.preventDefault();

             var formData = new FormData(this);
             formData.append("save_student", true);

             $.ajax({
                 type: "GET",
                 url: "http://localhost:5275/api/Product/GetAllProduct",
                 data: formData,
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