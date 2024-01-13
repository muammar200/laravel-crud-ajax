<script>
    // $(function() {

    // GLOBAL SETUP 
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Show All Employee
    $(document).ready(function(){
      $('#employees_table').DataTable({
          processing: true,
          ordering: false,
          serverSide: true,
          // ajax: "{{ url('fetchall') }}",
          ajax: "/fetchall",
          columns: [
            {
              data: null,
              searchable: false,
              render: function(data, type, row, meta){
                // return console.log(meta.row + meta.settings._iDisplayStart + 1);
                return meta.row + meta.settings._iDisplayStart + 1;
              },
            },
            {
              data: 'avatar',
              render: function(data, type, row){
                // return console.log(row.avatar);
                return `<img src="storage/images/${row.avatar}" style="width:80px; height:100px;" class="img-thumbnail">`;
              },
              // orderable: false,
              searchable: false,
            },
            {
              data: 'full_name',
              searchable: true,
              // orderable: false,
            },
            {
              data: 'email',
              searchable: true,
              // orderable: false,
            },
            {
              data: null,
              // orderable: false,
              render: function(data, type, row)   {
                let actionsHTML = `
                    <a id="${row.id}" style="text-decoration: none" class="link-success mx-1 fs-3 editIcon" data-bs-toggle="modal" data-bs-target="#editEmployeeModal">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <a id="${row.id}" class="link-danger mx-1 fs-3 deleteIcon">
                      <i class="bi bi-trash"></i>
                    </a>
                `;
                return actionsHTML;
              }
            },
          ], 
          responsive: true,
      });
    })

    // Event handler untuk tombol "Close" modal
    // Tambahkan event listener untuk form modal
    // $("#buttonModalForm").on("keydown", function(e) {
    // if (e.keyCode === 13) {
    //        e.preventDefault();
    //      if (Swal.isVisible()) {
    //             $(".swal2-confirm").click(); // Simulasi klik pada tombol OK
    //         }
    //     }
    // });

    // $("#buttonModalForm").on("keydown", function(e) {
    //     if (e.keyCode === 13) {
    //         e.preventDefault();
    //         // Tutup Sweet Alert jika muncul
    //         if (Swal.isVisible()) {
    //             $(".swal2-confirm").click(); // Simulasi klik pada tombol OK
    //         }
    //     }
    // });

      // add new employee ajax request
      $("#add_employee_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#add_employee_btn").text('Menambahkan...');
        $.ajax({
          url: '{{ route('store') }}',
          method: 'post',
          data: fd,
          cache: false,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(response) {
              // console.log(response);
              if (response.status === 'success') {
                  Swal.fire(
                      'Ditambahkan!',
                      'Karyawan Berhasil Ditambahkan!',
                      'success',
                      // allowOutsideClick: false, // Hindari menutup alert saat mengklik di luar
                      // allowEscapeKey: false, // Hindari menutup alert saat menekan tombol Escape
                      ) 
                      // .then((result) => {
                      // Tutup Sweet Alert saat tombol "Enter" ditekan
                      // if (result.isConfirmed) {
                      //    $(".swal2-confirm").click(); // Simulasi klik pada tombol OK
                      //}
                      // });
                  $("#add_employee_btn").text('Tambah Karyawan');
                  $("#add_employee_form")[0].reset();
                  $("#addEmployeeModal").modal('hide');
                  $("#add_employee_form input").removeClass("is-invalid");
                  $("#add_employee_form .invalid-feedback").text("");
                  $('#employees_table').DataTable().ajax.reload();
              }  
              else if (response.status === 'error') {
                  $("#add_employee_form input").removeClass("is-invalid");
                  $("#add_employee_form .invalid-feedback").text("");
                    let errors = response.errors;
                    // console.log(errors);
                      for (let fieldError in errors) {
                          $("#" + fieldError + "_error").text(errors[fieldError][0]);
                          $("#" + fieldError).addClass("is-invalid");
                          // console.log("#" + field);
                        }
                  $("#add_employee_btn").text('Tambah Karyawan');
              }
          }
      });
      });
 
//       Swal.mixin({
//     onOpen: (modalElement) => {
//         modalElement.addEventListener("keydown", function(e) {
//             if (e.keyCode === 13) {
//                 e.preventDefault();
//             }
//         });
//     }
// });


      // edit employee ajax request
      $(document).on('click', '.editIcon', function(e) {
        e.preventDefault();
        let id = $(this).attr('id');
        // console.log(id);
        $.ajax({
          url: '{{ route('edit') }}',
          method: 'get',
          data: {
            id: id,
          },
          success: function(response) {
            // console.log(response);
            $("#first_name_edit").val(response.first_name);
            $("#last_name_edit").val(response.last_name);
            $("#email_edit").val(response.email);
            $("#avatar_edit").html(
              `<img src="storage/images/${response.avatar}" width="100" class="img-fluid img-thumbnail">`);
            $("#employee_id").val(response.id);
            $("#employee_avatar").val(response.avatar);
          }
        });
      });
 
      // update employee ajax request
      $("#edit_employee_form").submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        $("#edit_employee_btn").text('Mengubah...');
        $.ajax({
          url: '{{ route('update') }}',
          method: 'post',
          data: fd,
          cache: false,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(response) {
            console.log(response);
            if (response.status == 200) {
              Swal.fire(
                'Diperbarui!',
                'Data Karyawan Berhasil Diubah!',
                'success'
              )
              $("#edit_employee_btn").text('Perbarui');
              $("#edit_employee_form")[0].reset();
              $("#editEmployeeModal").modal('hide');
              $("#edit_employee_form input").removeClass("is-invalid");
              $("#edit_employee_form .invalid-feedback").text("");
              $('#employees_table').DataTable().ajax.reload();
            }
            else if (response.status === 'error') {
                    let errors = response.errors;
                    console.log(errors);
                      for (let fieldError in errors) {
                          $("#" + fieldError + "_edit_error").text(errors[fieldError][0]);
                          $("#" + fieldError + "_edit").addClass("is-invalid");
                          // console.log("#" + field);
                        }
                  $("#edit_employee_btn").text('Perbarui');
              }
          }
        });
      });
 
      // delete employee ajax request
      $(document).on('click', '.deleteIcon', function(e) {
        e.preventDefault();
        let id = $(this).attr('id');
        Swal.fire({
          title: 'Apakah Anda yakin?',
          text: "Data yang sudah dihapus tidak dapat kembali!",
          icon: 'warning',
          showCancelButton: true,
          cancelButtonText: 'Batal',
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '{{ route('delete') }}',
              method: 'delete',
              data: {
                id: id,
              },
              success: function(response) {
                console.log(response);
                if (response.status == 200) {
                Swal.fire(
                'Dihapus!',
                'Data Karyawan Berhasil Dihapus!',
                'success'
              )
              $('#employees_table').DataTable().ajax.reload();
              } 
              else if (response.status == 400){
                Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Data gagal dihapus!',
})
            }
              }
            });
          }
        })
      });
 
      
    // });
  </script>