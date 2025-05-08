@extends('layouts.app')
@section('content')
    <link href="{{ asset('css/product.css') }}" rel="stylesheet">
    <div>
        <form id="searchForm" method="get" class="form-inline mb-2">
            <input id="searchName" type="text" name="name" placeholder="Nhập tên sản phẩm" class="form-control mr-1" />

            <select id="searchStatus" name="status" class="form-control mr-1">
                <option value="">Chọn trạng thái</option>
                <option value="1">Có hàng bán</option>
                <option value="0">Dừng bán</option>
            </select>
            <input id="priceFrom" type="number" name="priceFrom" placeholder="Giá bán từ" min="0" step="10"
                class="form-control mr-1" />
            <input id="priceTo" type="number" name="priceTo" placeholder="Giá bán đến" min="0" step="10"
                class="form-control mr-1" />


            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tìm kiếm</button>
            <a href="#" id="clearSearchBtn" class="btn btn-secondary ml-1"><i class="bi bi-x-circle"></i> Xóa tìm</a>
        </form>
        <a id="btnAdd" class="btn btn-secondary ml-1"><i class="bi bi-person-fill-add" style="color: blue"></i>Thêm mới</a>
        <div class="d-flex align-items-center justify-content-end" id="total">

        </div>

    </div>


    <table id="productTable" class="table table-bordered table-striped text-center">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Tên sản phẩm</th>
                <th>Mô tả</th>
                <th>Giá</th>
                <th>Tình trạng</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="productTableBody">

        </tbody>
    </table>


    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center" id="pagination">
        </ul>
    </nav>

@endsection
@section('scripts')
    @include('scripts.product-scripts')
@endsection