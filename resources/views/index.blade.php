<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRUD LARAVEL using AJAX</title>
  {{-- CDN Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
  integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
{{-- CDN Bootstrap Icon --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
{{-- CDN DataTables --}}
{{-- <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet"> --}}
<link
    rel="stylesheet"
    type="text/css"
    href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"/>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
  <div class="container">
    <div class="row my-5">
      <div class="col-lg-12">
        <h2>CRUD LARAVEL 10 using AJAX</h2>
        <div class="card shadow">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="text-light">Manage Employees</h3>
            <button class="btn btn-light" data-bs-toggle="modal" id="buttonModalForm" data-bs-target="#addEmployeeModal" tabindex="-1"><i
                class="bi-plus-circle me-2"></i>Tambah Karyawan</button>
          </div>
          {{-- <div class="card-body" id="show_all_employees">
            <h1 class="text-center text-secondary my-5">Loading...</h1>
          </div> --}}
          <div class="card-body" id="show_all_employees">
            {{-- <h1 class="text-center text-secondary my-5">Loading...</h1> --}}
            <table class="table table-striped table-sm" id="employees_table">
                <thead class="table-head">
                    <tr>
                        <th>No</th>
                        <th>Foto Profil</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
        </div>
      </div>
    </div>
  </div>
  {{-- Add New Employee Modal --}}
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="exampleModalLabel"
  data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Karyawan Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="add_employee_form" enctype="multipart/form-data">
        {{-- @csrf --}}
        <div class="modal-body p-4 bg-light">
          <div class="my-2">
            <label for="first_name">Nama Depan</label>
            <div class="invalid-feedback" id="first_name_error"></div>
            <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Nama Depan" required>
          </div>
          <div class="my-2">
            <label for="last_name">Nama Belakang</label>
            <div class="invalid-feedback" id="last_name_error"></div>
            <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Nama Belakang" required>
          </div>
          <div class="my-2">
            <label for="email">E-mail</label>
            <div class="invalid-feedback" id="email_error"></div>
            <input type="email" id="email" name="email" class="form-control" placeholder="E-mail" required>
          </div>
          <div class="my-2">
            <label for="avatar">Masukkan Foto</label>
            <div class="invalid-feedback" id="avatar_error">
            </div>
            <input id="avatar" type="file" name="avatar" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalBtn">Tutup</button>
          <button type="submit" id="add_employee_btn" class="btn btn-primary">Tambah Karyawan</button>
        </div>
      </form>
    </div>
  </div>
</div>
 
{{-- edit employee modal --}}
<div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="exampleModalLabel"
  data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Data Karyawan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="edit_employee_form" enctype="multipart/form-data">
        {{-- @csrf --}}
        <input type="hidden" name="employee_id" id="employee_id">
        <input type="hidden" name="employee_avatar" id="employee_avatar">
        <div class="modal-body p-4 bg-light">
            <div class="my-2">
              <label for="first_name_edit">Nama Depan</label>
              <div class="invalid-feedback" id="first_name_edit_error"></div>
              <input type="text" name="first_name" id="first_name_edit" class="form-control" placeholder="First Name" required>
            </div>
            <div class="my-2">
              <label for="last_name_edit">Nama Belakang</label>
              <div class="invalid-feedback" id="last_name_edit_error"></div>
              <input type="text" name="last_name" id="last_name_edit" class="form-control" placeholder="Last Name" required>
            </div>
          <div class="my-2">
            <label for="email_edit">E-mail</label>
            <div class="invalid-feedback" id="email_edit_error"></div>
            <input type="email" name="email" id="email_edit" class="form-control" placeholder="E-mail" required>
          </div>
          <div class="my-2">
            <label for="avatar_edit">Masukkan Foto</label>
            <div class="invalid-feedback" id="avatar_edit_error">
            </div>
            <input type="file" name="avatar" class="form-control">
          </div>
          <div class="mt-2" id="avatar_edit"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" id="edit_employee_btn" class="btn btn-success">Perbarui</button>
        </div>
      </form>
    </div>
  </div>
</div>
 {{-- CDN JQUERY --}}
 <script src="https://code.jquery.com/jquery-3.7.0.min.js"
 integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
{{-- CDN BOOTSTRAP --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
 integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
</script>
{{-- CDN DataTables --}}
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>
{{-- CDN SweetAlert --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

@include('partials.script')
</body>
</html>
 