<script>
    $(document).ready(function () {
        fetchUsers();

        function fetchUsers(page = 1) {
            const name = $('#searchName').val();
            const email = $('#searchEmail').val();
            const group = $('#searchGroup').val();
            const status = $('#searchStatus').val();

            $.ajax({
                url: 'api/users',
                type: 'GET',
                data: { name, email, group, status, page },
                success: function (res) {
                    let tableBody = $('#userTableBody');
                    tableBody.empty();
                    if (res.data && res.data.length > 0) {
                        const startIndex = (page - 1) * res.pagination.page_size;
                        $.each(res.data, function (index, user) {
                            let row = '<tr>' +
                                '<td><input type="checkbox" class="user-checkbox" value="' + user.id + '"></td>' +
                                '<td>' + (startIndex + index + 1) + '</td>' +
                                '<td>' + user.name + '</td>' +
                                '<td>' + user.email + '</td>' +
                                '<td>' + (user.group_role ?? '-') + '</td>' +
                                '<td>' + (user.is_active == 1 ? '<span class="text-success">Đang hoạt động</span>' : '<span class="text-danger">Tạm khóa</span>') + '</td>' +
                                '<td>' +
                                '<a href="#" class="editUser text-info mr-2" data-id="' + user.id + '"><i class="bi bi-pencil-fill" style="color: blue"></i></a>' +
                                '<a href="#" class="deleteUser text-danger mr-2" data-id="' + user.id + '"><i class="bi bi-trash-fill" style="color: red;"></i></a>' +
                                '<a href="#" class="toggle-active text-dark" data-id="' + user.id + '" data-active="' + user.is_active + '"><i class="bi bi-person-x-fill"></i></a>' +
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
                        fetchUsers(page);
                    }
                });
            }
        }


        $('#searchForm').on('submit', function (e) {
            e.preventDefault();
            fetchUsers(1);
        });

        $('#clearSearchBtn').on('click', function (e) {
            e.preventDefault();
            $('#searchForm')[0].reset();
            fetchUsers(1);
        });

        // Event click change permission
        let selectedUserId = null;
        let selectedUserName = '';
        let newStatus = null;
        let $currentRow = null;
        $(document).on('click', '.toggle-active', function (e) {
            e.preventDefault();
            console.log('click');


            const $icon = $(this);
            selectedUserId = $icon.data('id');
            selectedUserName = $icon.closest('tr').find('td').eq(2).text();
            const currentStatus = $icon.data('active');
            newStatus = currentStatus == 1 ? 0 : 1;
            $currentRow = $icon.closest('tr');
            console.log(this);
            console.log($icon.data());



            const actionText = newStatus === 1 ? 'mở khóa' : 'khóa';
            console.log("actionText:", actionText);
            console.log("selectedUserName:", selectedUserName);
            console.log("Element:", $('#confirmModalContent').length);

            const modalData = { actionText, selectedUserName };

            $('#confirmStatusModal').modal({
                backdrop: 'static',
                keyboard: false
            });


            const htmlContent = 'Bạn có muốn <strong>' + actionText + '</strong> thành viên <strong>' + selectedUserName + '</strong> không?';
            $('#confirmModalContent').html(htmlContent);
            console.log("Content set before show:", $('#confirmModalContent').html());
            $('#confirmStatusModal').data('action', 'changePermission');
            $('#confirmStatusModal').modal('show');
        });

        // Event click  delete user
        $(document).on('click', '.deleteUser', function () {
            const $icon = $(this);
            selectedUserId = $icon.data('id');
            const name = $icon.closest('tr').find('td').eq(2).text();
            $('#confirmStatusModal').modal({
                backdrop: 'static',
                keyboard: false
            });


            const htmlContent = 'Bạn có muốn xóa thành viên <strong>' + name + '</strong> không?';
            $('#confirmModalContent').html(htmlContent);
            console.log("Content set before show:", $('#confirmModalContent').html());
            $('#confirmStatusModal').data('action', 'deleteUser');
            $('#confirmStatusModal').modal('show');
        });


        // Event button in confirm modal
        $('#confirmStatusBtn').on('click', function () {
            const action = $('#confirmStatusModal').data('action')
            console.log(action);

            if (action === 'changePermission') {
                updatePermission();
            }
            if (action === 'deleteUser') {
                deleteUser();
            }
        });

        function updatePermission() {
            $.ajax({
                url: `api/users/is-active/${selectedUserId}`,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log('Response:', response);
                    if (response.success) {
                        Swal.fire("Thành công!", "Cập nhật trạng thái thành công!", "success").then(() => {
                            fetchUsers();
                        });
                    } else {
                        Swal.fire("Thất bại!", "Cập nhật trạng thái thất bại!", "error");
                    }
                    $('#confirmStatusModal').modal('hide');
                },
                error: function () {
                    Swal.fire("Thất bại", "Lỗi server!", "error");
                    $('#confirmStatusModal').modal('hide');
                }
            });
        }

        function deleteUser() {
            $.ajax({
                url: `api/users/${selectedUserId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire("Thành công!", "Xóa thành công!", "success").then(() => {
                            fetchUsers();
                        });
                    } else {
                        Swal.fire("Thất bại!", "Xóa thất bại!", "error");
                        alert("Xóa thất bại!");
                    }
                    $('#confirmStatusModal').modal('hide');
                },
                error: function () {
                    Swal.fire("Thất bại", "Lỗi server!", "error");
                    $('#confirmStatusModal').modal('hide');
                }
            });
        }

        // add user
        $('#btnAdd').on('click', function () {
            $('#addUserForm')[0].reset();
            $('#userId').val(0);
            $('#addUserModalLabel').text('Thêm mới người dùng');

            $('#addUserModal').modal('show');
        });

        // Edit user
        $(document).on('click', '.editUser', function () {
            $('#addUserForm')[0].reset();
            const $icon = $(this);
            $('#addUserModalLabel').text('Sửa thông tin người dùng')

            const name = $icon.closest('tr').find('td').eq(2).text();
            const email = $icon.closest('tr').find('td').eq(3).text();
            const group = $icon.closest('tr').find('td').eq(4).text();

            $('#userId').val($icon.data('id'));
            $('#newName').val(name);
            $('#newEmail').val(email);
            $('#newGroupRole').val(group);

            $('#addUserModal').modal('show');
        });

        $('#addUserModal').on('show.bs.modal', function () {
            const errorIds = ['#nameError', '#emailError', '#passwordError', '#passwordConfirmError', '#groupRoleError'];
            errorIds.forEach(id => $(id).text(''));
        });


        $('#saveUserBtn').on('click', function () {
            let name = $('#newName').val().trim();
            let email = $('#newEmail').val().trim();
            let password = $('#newPassword').val().trim();
            let confirmPassword = $('#passwordConfirm').val().trim();
            let groupRole = $('#newGroupRole').val();
            let id = $('#userId').val();

            const errorIds = ['#nameError', '#emailError', '#passwordError', '#passwordConfirmError', '#groupRoleError'];
            errorIds.forEach(id => $(id).text(''));

            let specialCharRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
            let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
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

            let isEdit = id != 0 ? true : false;
            if (isEdit == false) {


                if (!password) {
                    $('#passwordError').text('Mật khẩu không được để trống.');
                    hasError = true;
                } else if (password.length < 6) {
                    $('#passwordError').text('Mật khẩu ít nhất 6 ký tự.');
                    hasError = true;
                }



                if (!confirmPassword) {
                    $('#passwordConfirmError').text('Xác nhận mật khẩu không được để trống.');
                    hasError = true;
                } else if (password !== confirmPassword) {
                    $('#passwordConfirmError').text('Mật khẩu và xác nhận mật khẩu không khớp.');
                    hasError = true;
                }

            } else {
                if (password || confirmPassword) {
                    if (password.length < 6) {
                        $('#passwordError').text('Mật khẩu phải có ít nhất 6 ký tự.');
                        hasError = true;
                    }


                    if (password !== confirmPassword) {
                        $('#passwordConfirmError').text('Mật khẩu và xác nhận mật khẩu không khớp.');
                        hasError = true;
                    }
                }
            }

            if (!groupRole) {
                $('#groupRoleError').text('Vui lòng chọn nhóm quyền.');
                hasError = true;
            }

            if (hasError) return;


            let method = id != 0 ? 'PUT' : 'POST';
            let url = method === 'PUT' ? `api/users/${id}` : 'api/users'

            // Check email existed
            $.ajax({
                url: 'api/users/check-email-id',
                method: 'POST',
                data: {
                    id: id,
                    email: email
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (response) {
                    if (response.exists) {
                        $('#emailError').text('Email đã tồn tại.');
                    } else {

                        $.ajax({
                            url: url,
                            method: method,
                            data: {
                                id: id,
                                name: name,
                                email: email,
                                password: password,
                                group_role: groupRole
                            },
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    alert(id != 0 ? "Sửa thành công" : "Thêm thành công");
                                    $('#addUserForm')[0].reset();
                                    fetchUsers();
                                } else {
                                    alert(id != 0 ? "Sửa thất bại" : "Thêm thất bại");
                                }
                                $('#addUserModal').modal('hide');
                            },
                            error: function () {
                                alert(id != 0 ? "Lỗi khi sửa" : "Lỗi khi thêm");
                                $('#addUserModal').modal('hide');
                            }

                        });
                    }
                }

            });
        });


    });

</script>