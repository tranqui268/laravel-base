@extends('layouts.empty')
@section('content')
    <link href="{{ asset('css/product-detail.css') }}" rel="stylesheet">
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('products') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết sản phẩm</li>
            </ol>
        </nav>
        <h2>{{ isset($product) ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới' }}</h2>
        <form id="productForm" enctype="multipart/form-data">
            @csrf
            @if(isset($product))
                <input type="hidden" id="productId" name="productId" value="{{ $product->product_id }}" />
            @endif
            <div class="row">
                <!-- Cột trái: Thông tin sản phẩm -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="productName">Tên sản phẩm</label>
                        <input type="text" id="productName" name="productName" class="form-control"
                            value="{{ $product->product_name ?? '' }}" required>
                        <div class="invalid-feedback">Tên sản phẩm không được để trống.</div>
                    </div>
                    <div class="form-group">
                        <label for="productPrice">Giá bán</label>
                        <input type="number" id="productPrice" name="productPrice" class="form-control" min="0" step="1"
                            value="{{ $product->product_price ?? '' }}" required>
                        <div class="invalid-feedback">Giá bán không được nhỏ hơn 0.</div>
                    </div>
                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea id="description" name="description" class="form-control" rows="3">
                                                {{ $product->description ?? '' }}
                                                </textarea>
                        <div class="invalid-feedback">Mô tả không hợp lệ.</div>
                    </div>
                    <div class="form-group">
                        <label for="isSales">Trạng thái</label>
                        <select id="isSales" name="isSales" class="form-control">
                            <option value="1" {{ isset($product) && $product->is_sales == 1 ? 'selected' : '' }}>
                                Đang bán
                            </option>
                            <option value="0" {{ isset($product) && $product->is_sales == 0 ? 'selected' : '' }}>
                                Ngừng bán
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Cột phải: Phần hình ảnh -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Hình ảnh</label>
                        <div id="imagePreviewContainer">
                            <img id="imagePreview" src="{{ $product->product_image ?? '#' }}" alt="Xem trước ảnh">
                            <button type="button" id="removeFileBtn" title="Xóa file">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <label for="imageUpload" class="custom-file-upload">
                            <i class="bi bi-upload"></i> Upload
                        </label>
                        <input type="file" id="imageUpload" name="imageUpload" accept="image/png,image/jpeg">
                        <div id="uploadResult"></div>
                    </div>
                    <input type="hidden" id="oldImageUrl" name="oldImageUrl" value="#">
                </div>
            </div>

            <!-- Nút Hủy và Lưu -->
            <div class="row">
                <div class="col-md-12">
                    <button type="button" id="cancelBtn" class="btn btn-secondary">Hủy</button>
                    <button type="submit" class="btn btn-danger">Lưu</button>
                </div>
            </div>
        </form>
    </div>
    @section('scripts')
        @include('scripts.product-detail-scripts')
    @endsection

@endsection