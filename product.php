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
                                     <th>Edit</th>
                                     <th>Delete</th>
                                 </tr>
                             </thead>
                             <tbody id="table-body">
                             </tbody>
                         </table>
                     </div>
                     <div class="container ">
                         <div id="pagination-context"></div>
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

         $('table').on('click', 'input[type="submit"]', function(e) {
             e.preventDefault();
             var id = $(this).closest("tr").find(".idRow").text()
             var name = $(this).closest("tr").find(".nameRow").text()
             var price = $(this).closest("tr").find(".priceRow").text()
             var type = $(this).closest("tr").find(".typeRow").text()
             var active = $(this).closest("tr").find(".activeRow").text()
             window.location.href = "edit.php?id=" + id + "&name=" + name + "&price=" + price + "&type=" + type + "&active=" + active;
         })

         function postDataWithCallback(method, url) {
             $.ajax({
                 type: method,
                 url: url,
                 processData: false,
                 contentType: false,
                 success: function(response) {
                     var paginationContext = {
                         'dataSet': response,
                         'page': 1,
                         'limit': 5,
                         'size': 10,
                     }

                     $('#productTable tbody').empty();
                     setupTable()

                     function setPagination(dataSet, page, limit) {
                         var start = (page - 1) * limit
                         var end = start + limit
                         var slice = dataSet.slice(start, end)
                         var pageCount = Math.round(dataSet.length / limit) + 1;

                         return {
                             'dataSet': slice,
                             'pages': pageCount,
                         }
                     }

                     function setNavigateButtons(pages) {
                         var contextComponent = document.getElementById('pagination-context')
                         contextComponent.innerHTML = ``
                         var left = (paginationContext.page - Math.floor(paginationContext.size / 2))
                         var right = (paginationContext.page + Math.floor(paginationContext.size / 2))

                         //adjust left, set as first page by default
                         if (left < 1) {
                             left = 1
                             right = paginationContext.size
                         }

                         //adjust right, set as the last page by default
                         if (right > pages) {
                             left = pages - (paginationContext.size - 1)

                             if (left < 1) {
                                 left = 1
                             }
                             right = pages
                         }

                         setPaginationBtnTxt(paginationContext, contextComponent, left, right, pages)

                         $('.page').on('click', function() {
                             $('#table-body').empty()
                             paginationContext.page = Number($(this).val())
                             setupTable()
                         })

                     }

                     function setPaginationBtnTxt(paginationContext, contextComponent, left, right, pages) {
                         // text == first page by default (over-boundary)
                         if (paginationContext.page != 1) {
                             contextComponent.innerHTML = `<button value=${1} class="page btn btn-sm btn-info">&#171; First Page</button>` + contextComponent.innerHTML
                         }

                         for (var page = left; page <= right; page++) {
                             contextComponent.innerHTML += `<button value=${page} class="page btn btn-sm btn-info">${page}</button>`
                         }

                         // text == last page by default (over-boundary)
                         if (paginationContext.page != pages) {
                             contextComponent.innerHTML += `<button value=${pages} class="page btn btn-sm btn-info">Last Page&#187;</button>`
                         }
                     }

                     function setupTable() {
                         var table = $('#table-body')
                         var data = setPagination(paginationContext.dataSet, paginationContext.page, paginationContext.limit)
                         var myList = data.dataSet

                         for (var i = 1 in myList) {
                             var row = `<tr>
                  <td class="idRow">${myList[i].id}</td>
                  <td class="nameRow">${myList[i].name}</td>
                  <td class="priceRow">${myList[i].price}</td>
                  <td class="typeRow">${myList[i].type}</td>
                  <td class="activeRow">${myList[i].active}</td>
                  <td><input type="submit" value="Edit"></td>
                  <td><input type="button" value="Delete"></td>
                  `
                             table.append(row)
                         }

                         setNavigateButtons(data.pages)
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
     </script>

 </body>

 </html>