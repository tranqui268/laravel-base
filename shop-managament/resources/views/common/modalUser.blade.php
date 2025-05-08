<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <input type="hidden" id="userId" name="id">
                    <div class="form-group">
                        <label for="newName">Tên:</label>
                        <input type="text" class="form-control" id="newName" name="name" required>
                        <small class="text-danger" id="nameError"></small>
                    </div>
                    <div class="form-group">
                        <label for="newEmail">Email:</label>
                        <input type="email" class="form-control" id="newEmail" name="email" required>
                        <small class="text-danger" id="emailError"></small>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">Mật khẩu:</label>
                        <input type="password" class="form-control" id="newPassword" name="password" required>
                        <small class="text-danger" id="passwordError"></small>
                    </div>
                    <div class="form-group">
                        <label for="passwordConfirm">Xác nhận:</label>
                        <input type="password" class="form-control" id="passwordConfirm" name="passwordConfirmName"
                            required>
                        <small class="text-danger" id="passwordConfirmError"></small>
                    </div>
                    <div class="form-group">
                        <label for="newGroupRole">Nhóm quyền:</label>
                        <select class="form-control" id="newGroupRole" name="group_role" required>
                            <option value="">-- Chọn nhóm quyền --</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                        <small class="text-danger" id="groupRoleError"></small>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveUserBtn">Lưu</button>
            </div>
        </div>
    </div>
</div>