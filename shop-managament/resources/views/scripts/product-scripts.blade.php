<script>
    $(document).ready(function () {
        fetchProducts();

        function fetchProducts(page = 1) {
            const name = $('#searchName').val();
            const status = $('#searchStatus').val();
            const priceFrom = $('#priceFrom').val();
            const priceTo = $('#priceTo').val();

            $.ajax({
                url: 'api/products',
                type: 'GET',
                data: { name, status, priceFrom, priceTo, page },
                success: function (res) {
                    let tableBody = $('#productTableBody');
                    tableBody.empty();
                    if (res.data && res.data.length > 0) {
                        const startIndex = (page - 1) * res.pagination.page_size;
                        $.each(res.data, function (index, product) {
                            let decodedImageUrl = 'https://res.cloudinary.com/dhis8yzem/image/upload/v1741008403/Avatar_default_zfdjrk.png'
                            if (product.product_image) {
                                decodedImageUrl = product.product_image.replace(/\\\//g, '/');
                            }
                            let row = `
                                        <tr>
                                            <td>${startIndex + index + 1}</td>
                                            <td>
                                                <span class="product-name" data-image-url="${decodedImageUrl}">
                                                    ${product.product_name}
                                                </span>
                                            </td>
                                            <td>${product.description}</td>
                                            <td>$ ${product.product_price}</td>
                                            <td>${product.is_sales == 1 ? '<span class="text-success">Đang bán</span>' : '<span class="text-danger">Ngừng bán</span>'}</td>
                                            <td>
                                                <a class="editUser text-info mr-2" href="{{ url('products') }}?productId=${product.product_id}">
                                                    <i class="bi bi-pencil-fill" style="color: blue"></i>
                                                </a>
                                                <a href="#" class="deleteProduct text-danger mr-2" data-id="${product.product_id}">
                                                    <i class="bi bi-trash-fill" style="color: red;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    `;
                            tableBody.append(row);
                        });

                        $('#total').html('<p>Hiển thị từ ' + (startIndex + 1) + ' ~ ' + (startIndex + res.data.length) + ' trong tổng số ' + res.original.pagination.total + ' sản phẩm</p>');

                    } else {
                        tableBody.append('<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>');
                        $('#total').html('<p>Hiển thị 0 sản phẩm</p>');
                    }
                    renderPagination(res);
                },
                error: function (xhr, status, error) {
                    $('#userTableBody').html('<tr><td colspan="5" class="text-center text-danger">Lỗi khi tải dữ liệu</td></tr>');
                }

            });

        }

        function renderPagination(res) {
            let pagination = $('#pagination')
            pagination.empty();
            let paginationInfo = res.pagination;
            let currentPage = paginationInfo.current_page;
            let totalPages = paginationInfo.last_page;
            if (totalPages > 1) {
                if (currentPage > 1) {
                    pagination.append('<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '">Previous</a></li>');
                } else {
                    pagination.append('<li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>');
                }

                // Các số trang
                for (let i = 1; i <= totalPages; i++) {
                    if (i === currentPage) {
                        pagination.append('<li class="page-item active"><a class="page-link" href="#">' + i + '</a></li>');
                    } else {
                        pagination.append('<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
                    }
                }

                // Nút Next
                if (currentPage < totalPages) {
                    pagination.append('<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '">Next</a></li>');
                } else {
                    pagination.append('<li class="page-item disabled"><a class="page-link" href="#">Next</a></li>');
                }

                // Gắn sự kiện cho các nút phân trang
                $('.page-link').click(function (e) {
                    e.preventDefault();
                    let page = $(this).data('page');
                    console.log(page);

                    if (page) {
                        fetchProducts(page);
                    }
                });
            }
        }


        $('#searchForm').on('submit', function (e) {
            e.preventDefault();
            fetchProducts(1);
        });

        $('#clearSearchBtn').on('click', function (e) {
            e.preventDefault();
            $('#searchForm')[0].reset();
            fetchProducts(1);
        });

        $(document).on('mouseenter', '.product-name', function () {
            const $this = $(this);
            let imageUrl = $this.data('image-url');

            if (!imageUrl || imageUrl.trim() === '') {
                imageUrl = 'https://res.cloudinary.com/dhis8yzem/image/upload/v1741008403/Avatar_default_zfdjrk.png';
            }

            const tooltip = $('<div class="tooltip-image"><img src="' + imageUrl + '" alt="Product Image" style="max-width: 150px; max-height: 150px;"/></div>');
            $this.append(tooltip);
        });

        $(document).on('mouseleave', '.product-name', function () {
            $(this).find('.tooltip-image').remove();
        });


        // Delete user
        $(document).on('click', '.deleteProduct', function () {
            const $icon = $(this);
            const productId = $icon.data('id');
            const name = $icon.closest('tr').find('td').eq(1).text();
            const is_sales = $icon.closest('tr').find('td').eq(4).text();
            console.log("is_sales:", is_sales);
            console.log("typeof is_sales:", typeof is_sales);
            if (is_sales.trim() === 'Đang bán') {
                if (confirm('Bạn có chắc muốn xóa ' + name)) {
                    $.ajax({
                        url: `api/products/${productId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire('Thành công!', 'Sản phẩm đã được xóa.', 'success');
                                fetchProducts();
                            } else {
                                Swal.fire('Lỗi!', 'Xóa thất bại.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Lỗi!', 'Lỗi khi xóa.', 'error');
                        }
                    });
                }

            } else {
                Swal.fire('Thông báo', `Sản phẩm ${name} đã ngừng bán`, 'info');
            }

        });

        $('#btnAdd').on('click', function (e) {
            e.preventDefault();
            window.location.href = '{{ url("products/detail") }}';
        });

    });
</script>