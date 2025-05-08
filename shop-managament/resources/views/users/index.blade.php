@extends('layouts.app')
@section('content')
    <div>
        <form id="searchForm" method="get" class="form-inline mb-2">
            <input id="searchName" type="text" name="name" placeholder="Nhập họ tên" class="form-control mr-1" />
            <input id="searchEmail" type="text" name="email" placeholder="Nhập email" class="form-control mr-1" />

            <select id="searchGroup" name="group" class="form-control mr-1">
                <option value="">Chọn nhóm</option>
                <option value="admin">Admin</option>
                <option value="editor">Editor</option>
            </select>

            <select id="searchStatus" name="status" class="form-control mr-1">
                <option value="">Chọn trạng thái</option>
                <option value="1">Đang hoạt động</option>
                <option value="0">Tạm khóa</option>
            </select>

            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tìm kiếm</button>
            <a href="#" id="clearSearchBtn" class="btn btn-secondary ml-1"><i class="bi bi-x-circle"></i>
                Xóa tìm</a>
        </form>
        <a id="btnAdd" class="btn btn-secondary ml-1"><i class="bi bi-person-fill-add" style="color: blue"></i>Thêm mới</a>

        <div class="mb-2">
            <button id="deleteSelectedBtn" class="btn btn-danger">Xóa hàng loạt</button>
        </div>

        <div class="d-flex align-items-center justify-content-end" id="total">

        </div>

    </div>


    <table id="userTable" class="table table-bordered table-striped text-center">
        <thead class="thead-light">
            <tr>
                <th></th>
                <th>#</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Nhóm</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="userTableBody">

        </tbody>
    </table>


    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center" id="pagination">
            <li>1</li>
        </ul>
    </nav>
    @include('common.modalUser')
    @include('common.modalConfirm')
@endsection

@section('scripts')
    @include('scripts.user-scripts')
@endsection