<script>
    $(document).ready(function () {
        fetchCustomers();

        function fetchCustomers(page = 1) {
            const name = $('#searchName').val();
            const email = $('#searchEmail').val();
            const status = $('#searchStatus').val();
            const address = $('#searchAddress').val();

            $.ajax({
                url: 'api/customers',
                type: 'GET',
                data: { name, email, status, address, page },
                success: function (res) {
                    let tableBody = $('#customerTableBody');
                    tableBody.empty();
                    if (res.data && res.data.length > 0) {
                        const startIndex = (page - 1) * res.pagination.page_size;
                        $.each(res.data, function (index, customer) {
                            let row = '<tr>' +
                                '<td>' + (startIndex + index + 1) + '</td>' +
                                '<td>' + customer.customer_name + '</td>' +
                                '<td>' + customer.email + '</td>' +
                                '<td>' + (customer.address) + '</td>' +
                                '<td>' + (customer.tel_num) + '</td>' +
                                '<td>' +
                                '<a class="editUser text-info mr-2" data-id="' + customer.customer_id + '"><i class="bi bi-pencil-fill" style="color: blue"></i></a>' +
                                '</td>' +
                                '</tr>';
                            tableBody.append(row);
                        });

                        $('#total').html('<p>Hiển thị từ ' + (startIndex + 1) + ' ~ ' + (startIndex + res.data.length) + ' trong tổng số ' + res.pagination.total + ' khách hàng</p>');

                    } else {
                        tableBody.append('<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>');
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
                        fetchCustomers(page);
                    }
                });
            }
        }


        $('#searchForm').on('submit', function (e) {
            e.preventDefault();
            fetchCustomers(1);
        });

        $('#clearSearchBtn').on('click', function (e) {
            e.preventDefault();
            $('#searchForm')[0].reset();
            fetchCustomers(1);
        });


        //Validate add user
        $('#btnAdd').on('click', function () {
            $('#addCustomerForm')[0].reset();
            $('#addCustomerModalLabel').text('Thêm khách hàng');

            $('#addCustomerModal').modal('show');
        });

        $('#addCustomerModal').on('show.bs.modal', function () {
            const errorIds = ['#nameError', '#emailError', '#phoneError', '#addressError'];
            errorIds.forEach(id => $(id).text(''));
        });

        $('#saveCustomerBtn').on('click', function () {
            let name = $('#newName').val().trim();
            let email = $('#newEmail').val().trim();
            let phone = $('#newPhone').val().trim();
            let address = $('#newAddress').val().trim();

            const errorIds = ['#nameError', '#emailError', '#phoneError', '#addressError'];
            errorIds.forEach(id => $(id).text(''));

            let specialCharRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
            let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            const phoneRegex = /^0\d{10}$/;
            let hasError = false;

            if (!name) {
                $('#nameError').text('Tên không được để trống');
                hasError = true;
            } else if (specialCharRegex.test(name)) {
                $('#nameError').text('Tên không được chứa ký tự đặc biệt');
                hasError = true;
            }

            if (!email) {
                $('#emailError').text('Email không được để trống.');
                hasError = true;
            } else if (!emailRegex.test(email)) {
                $('#emailError').text('Email không đúng định dạng.');
                hasError = true;
            } else if (specialCharRegex.test(email.split('@')[0])) {
                $('#emailError').text('Phần trước @ của email không được chứa ký tự đặc biệt.');
                hasError = true;
            }

            if (!phone) {
                $('#phoneError').text('Điện thoại không được để trống');
                hasError = true;
            } else if (phoneRegex.test(phone)) {
                $('#phoneError').text('Điện thoại không đúng định dạng');
                hasError = true;
            }

            if (!address) {
                $('#addressError').text('Địa chỉ không được để trống');
                hasError = true;
            }


            if (hasError) return;


            $.ajax({
                url: 'api/customers',
                method: 'POST',
                data: {
                    name: name,
                    email: email,
                    tel_num: phone,
                    address: address
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        alert("Thêm thành công");
                        $('#addCustomerForm')[0].reset();
                        fetchCustomers();
                    } else {
                        alert("Thêm thất bại");
                    }
                    $('#addCustomerModal').modal('hide');
                },
                error: function (response) {
                    alert(response.message);
                    $('#addCustomerModal').modal('hide');
                }

            });
        });





        // edit customer
        $(document).on('click', '.editUser', function (e) {
            e.preventDefault();

            // Kiểm tra nếu có dòng đang chỉnh sửa
            if ($('tr.editing').length) {
                Swal.fire('Thông báo', 'Vui lòng lưu hoặc hủy dòng đang chỉnh sửa trước khi chỉnh sửa dòng khác.', 'info');
                return;
            }

            const row = $(this).closest('tr');
            const id = $(this).data('id');
            console.log(id);


            const name = row.find('td:eq(1)').text().trim();
            const email = row.find('td:eq(2)').text().trim();
            const address = row.find('td:eq(3)').text().trim();
            const tel = row.find('td:eq(4)').text().trim();

            // Chuyển các ô sang input
            row.find('td:eq(1)').html('<input type="text" class="form-control form-control-sm edit-name" value="' + name + '">');
            row.find('td:eq(2)').html('<input type="email" class="form-control form-control-sm edit-email" value="' + email + '">');
            row.find('td:eq(3)').html('<input type="text" class="form-control form-control-sm edit-address" value="' + address + '">');
            row.find('td:eq(4)').html('<input type="text" class="form-control form-control-sm edit-tel" value="' + tel + '">');

            // Thay nút "Edit" bằng "Save" và "Cancel"
            row.find('td:eq(5)').html('<a href="#" class="saveEdit text-success mr-2" data-id="' + id + '"><i class="bi bi-check-lg"></i></a>' +
                '<a href="#" class="cancelEdit text-secondary" data-id="' + id + '"><i class="bi bi-x-lg"></i></a>');

            row.addClass('editing');
        });


        // save edit
        $(document).on('click', '.saveEdit', function (e) {
            e.preventDefault();

            const row = $(this).closest('tr');
            const id = $(this).data('id');
            let specialCharRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
            console.log(id);


            var name = row.find('.edit-name').val().trim();
            var email = row.find('.edit-email').val().trim();
            var address = row.find('.edit-address').val().trim();
            var tel = row.find('.edit-tel').val().trim();

            // Validation
            if (name === '' || email === '' || tel === '') {
                alert('Vui lòng điền đầy đủ tên, email và số điện thoại!');
                return;
            }

            if (specialCharRegex.test(name)) {
                alert('Tên không hợp lệ!');
                return;
            }

            const phoneRegex = /^0\d{9}$/;
            if (!phoneRegex.test(tel)) {
                alert('Số điện thoại không hợp lệ!');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Email không đúng định dạng!');
                return;
            }

            // Gửi request cập nhật
            $.ajax({
                url: 'api/customers/check-email-id',
                method: 'POST',
                data: {
                    customer_id: id,
                    email: email
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.exists) {
                        alert("email đã tồn tại");
                    } else {
                        $.ajax({
                            url: `api/customers/${id}`,
                            type: 'PUT',
                            data: {                               
                                name: name,
                                email: email,
                                tel_num: tel,
                                address: address,

                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {

                                    row.find('td:eq(1)').text(name);
                                    row.find('td:eq(2)').text(email);
                                    row.find('td:eq(3)').text(address);
                                    row.find('td:eq(4)').text(tel);

                                    row.find('td:eq(5)').html
                                        (
                                            `<a class="editUser text-info mr-2" data-id="${id}">
                                                                                            <i class="bi bi-pencil-fill" style="color: blue"></i>
                                                                                        </a>`
                                        );
                                    row.removeClass('editing');
                                } else {
                                    alert('Cập nhật thất bại');
                                }
                            },
                            error: function () {
                                alert('Lỗi kết nối server!');
                            }
                        });


                    }
                }

            });
        });


        // cancel edit
        $(document).on('click', '.cancelEdit', function (e) {
            e.preventDefault();

            const row = $(this).closest('tr');
            const id = $(this).data('id');


            fetchCustomers();
        });


        // Import file
        $('#importFile').on('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            $.ajax({
                url: 'api/customers/import',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    let message = `Import hoàn tất:<br>` +
                        `Thêm mới: ${response.success_count}<br>` +
                        `Cập nhật: ${response.updated_count}<br>` +
                        `Bỏ qua: ${response.skipped_count}<br>`;

                    if (response.errors.length > 0) {
                        message += `Lỗi:<br>`;
                        response.errors.forEach(error => {
                            message += `Dòng ${error.row}: ${error.errors.join(', ')}<br>`;
                        });
                    }

                    Swal.fire({
                        title: 'Kết quả import',
                        html: message,
                        icon: response.errors.length > 0 ? 'warning' : 'success',
                    });
                    fetchCustomers();
                    $('#importFile').val(''); // Reset input file
                },
                error: function (xhr) {
                    Swal.fire('Lỗi!', xhr.responseJSON.error || 'Lỗi khi import file', 'error');
                }
            });
        });


        // Export file
        $('#exportBtn').on('click', function (e) {
            e.preventDefault();
            let searchParams = {
                name: $('#searchName').val(),
                email: $('#searchEmail').val(),
                status: $('#searchStatus').val(),
                address: $('#searchAddress').val()
            };
            console.log("Export parameters:", searchParams);

            $.ajax({
                url: 'api/customers/export',
                method: 'GET',
                data: searchParams,
                xhrFields: {
                    responseType: 'blob'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data, status, xhr) {
                    const contentType = xhr.getResponseHeader('Content-Type');
                    const disposition = xhr.getResponseHeader('Content-Disposition');
                    const filename = disposition
                        ? disposition.split('filename=')[1].replace(/"/g, '')
                        : `customers_${new Date().toISOString().replace(/[-:]/g, '').split('.')[0]}.xlsx`;

                    const blob = new Blob([data], { type: contentType });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    Swal.fire('Thành công!', 'File Excel đã được tải xuống.', 'success');
                },
                error: function (xhr) {
                    Swal.fire('Lỗi!', xhr.responseJSON?.error || 'Lỗi khi export file', 'error');
                }
            });
        });


    });

</script>
