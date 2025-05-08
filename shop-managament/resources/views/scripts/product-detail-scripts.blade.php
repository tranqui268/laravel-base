<script>
    function getProductIdFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('productId');
        console.log("Extracted productId from URL: ", productId);
        // Đảm bảo productId là chuỗi, không phải mảng
        if (Array.isArray(productId)) {
            productId = productId[0]; // Lấy phần tử đầu tiên nếu là mảng
        }
        return productId ? String(productId) : null;
    }
    $(document).ready(function () {
        document.getElementById('productPrice').addEventListener('keydown', function (e) {
            if (["e", "E", "+", "-"].includes(e.key)) {
                e.preventDefault();
            }
        });
        console.log("Document ready");
        let id = getProductIdFromURL();
        console.log("ID" + id)

        if (id) {
            $.ajax({
                url: `{{ url('api/products') }}/${id}`,
                method: 'GET',
                traditional: true,
                dataType: 'json',
                beforeSend: function () {
                    console.log("Sending AJAX request with productId: ", id);
                    console.log("Type of productId: ", typeof id);
                },
                success: function (response) {
                    if (response.success && response.data) {
                        const product = response.data;

                        $('#productName').val(product.product_name || '');
                        $('#productPrice').val(product.product_price || '');
                        $('#description').val(product.description || '');
                        $('#isSales').val(product.is_sales == false ? '0' : '1');

                        if (product.product_image) {
                            const decodeImage = product.product_image.replace(/\\\//g, '/');
                            $('#imagePreview').attr('src', decodeImage);
                            $('#imagePreviewContainer').show();
                            $('#oldImageUrl').val(decodeImage);
                        }
                    } else {
                        alert('Không thể tải dữ liệu sản phẩm: ' + (response.message || 'Lỗi không xác định'));
                        window.location.href = 'product.action';
                    }


                },
                error: function (xhr, status, error) {
                    alert('Lỗi khi tải dữ liệu sản phẩm: ' + error);
                    window.location.href = 'product.action';
                }

            })
        }



        // Hiển thị ảnh xem trước
        $('#imageUpload').on('change', function () {
            const file = this.files[0];
            const $uploadResult = $('#uploadResult');
            const $imagePreview = $('#imagePreview');
            const $imagePreviewContainer = $('#imagePreviewContainer');

            if (!file) {
                $uploadResult.html('<p class="text-danger">Vui lòng chọn file ảnh.</p>');
                $imagePreviewContainer.hide();
                return;
            }

            // Kiểm tra định dạng
            const allowedTypes = ['image/png', 'image/jpeg'];
            if (!allowedTypes.includes(file.type)) {
                $uploadResult.html('<p class="text-danger">Chỉ chấp nhận file PNG, JPG, JPEG.</p>');
                $imagePreviewContainer.hide();
                return;
            }

            // Kiểm tra dung lượng (2MB)
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes
            if (file.size > maxSize) {
                $uploadResult.html('<p class="text-danger">Dung lượng file không được vượt quá 2MB.</p>');
                $imagePreviewContainer.hide();
                return;
            }

            // Kiểm tra kích thước ảnh
            const img = new Image();
            img.onload = function () {
                if (this.width > 1024 || this.height > 1024) {
                    $uploadResult.html('<p class="text-danger">Kích thước ảnh không được vượt quá 1024px.</p>');
                    $imagePreviewContainer.hide();
                } else {
                    $uploadResult.html('');
                    $imagePreview.attr('src', this.src);
                    $imagePreviewContainer.show();
                }
            };
            img.onerror = function () {
                $uploadResult.html('<p class="text-danger">File không phải là ảnh hợp lệ.</p>');
                $imagePreviewContainer.hide();
            };
            img.src = URL.createObjectURL(file);
        });

        // Xử lý nút "Xóa file"
        $('#removeFileBtn').on('click', function () {
            $('#imageUpload').val('');
            $('#imagePreviewContainer').hide();
            $('#uploadResult').html('');
        });

        // Xử lý form submit
        $('#productForm').on('submit', function (e) {
            e.preventDefault();

            // Validation
            let isValid = true;
            const productName = $('#productName').val().trim();
            const productPrice = parseFloat($('#productPrice').val());
            const description = $('#description').val().trim();
            const file = $('#imageUpload')[0].files[0];
            const $uploadResult = $('#uploadResult');
            const $imagePreviewContainer = $('#imagePreviewContainer');
            const specialCharRegex = /^[\p{L}0-9\s\-_]+$/u;
            const sqlInjectionRegex = /(SELECT|INSERT|UPDATE|DELETE|DROP|TRUNCATE|UNION|EXEC|CREATE|ALTER|GRANT|REVOKE|--|\/\*|\*\/|;)/i;
            const hasPotentialSqlInjection = (text) => {
                return sqlInjectionRegex.test(text);
            };


            $('#productName, #productPrice, #description').removeClass('is-invalid');
            $('.invalid-feedback').text('');


            // Validate productName
            if (!productName) {
                $('#productName').addClass('is-invalid');
                $('#productName').next('.invalid-feedback').text('Tên sản phẩm không được để trống.');
                isValid = false;
                console.log('productName validation failed: Empty');
            } else if (!specialCharRegex.test(productName)) {
                $('#productName').addClass('is-invalid');
                $('#productName').next('.invalid-feedback').text('Tên sản phẩm không được chứa ký tự đặc biệt.');
                isValid = false;
                console.log('productName validation failed: Contains special characters');
            }


            // Validate description
            if (description) {
                if (!specialCharRegex.test(description)) {
                    $('#description').addClass('is-invalid');
                    $('#description').next('.invalid-feedback').text('Mô tả không được chứa ký tự đặc biệt.');
                    isValid = false;
                    console.log('description validation failed: Contains special characters');
                } else if (hasPotentialSqlInjection(description)) {
                    $('#description').addClass('is-invalid');
                    $('#description').next('.invalid-feedback').text('Mô tả không được chứa câu lệnh SQL.');
                    isValid = false;
                    console.log('description validation failed: Contains SQL injection');
                }
            }
            console.log('After description validation, isValid:', isValid);

            // Validate productPrice
            if (isNaN(productPrice) || productPrice < 0) {
                $('#productPrice').addClass('is-invalid');
                $('#productPrice').next('.invalid-feedback').text('Giá sản phẩm không hợp lệ.');
                isValid = false;
                console.log('productPrice validation failed:', productPrice);
            }
            console.log('After productPrice validation, isValid:', isValid);

            const oldImage = $('#oldImageUrl').val();
            if (!file && !oldImage) {
                $uploadResult.html('<p class="text-danger">Vui lòng chọn file ảnh.</p>');
                $imagePreviewContainer.hide();
                isValid = false;
            }

            if (!isValid) return;

            // Chuẩn bị dữ liệu gửi đi
            const formData = new FormData();
            formData.append('name', productName);
            formData.append('price', productPrice);
            formData.append('description', $('#description').val());
            formData.append('status', $('#isSales').val());
            if (file) {
                formData.append('file', file);
                formData.append('fileFileName', file.name);
            } else if (oldImage) {
                formData.append('imageUrl', oldImage);
            }
            if (id) {
                formData.append('_method', 'PUT');
            }
            console.log(productName);

            Swal.fire({
                title: 'Đang xử lý...',
                text: 'Vui lòng chờ trong khi lưu sản phẩm.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: id ? `{{ url('api/products') }}/${id}` : `{{ url('api/products') }}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        Swal.close();
                        Swal.fire('Thành công!', 'Sản phẩm đã được lưu.' , 'success').then(() => {
                            window.location.href = '{{ url("products") }}';
                        });
                    } else {
                        alert(id ? 'Cập nhật sản phẩm thất bại' : 'Lưu sản phẩm thất bại: ' + (response.message || 'Lỗi không xác định'));
                        $('#productForm')[0].reset();
                        $('#productName, #productPrice, #description').removeClass('is-invalid');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close(); 
                    console.log('Error response:', xhr.responseJSON);
                    Swal.fire('Lỗi!', xhr.responseJSON?.error || 'Lỗi khi lưu sản phẩm', 'error');
                }
            });
        });

        // Xử lý nút Hủy
        $('#cancelBtn').on('click', function () {
            window.location.href = '{{ url("products") }}';
        });
    });
</script>