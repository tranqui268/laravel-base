<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
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
                        <label for="newPhone">Điện thoại:</label>
                        <input type="tel" class="form-control" id="newPhone" name="phone" required>
                        <small class="text-danger" id="phoneError"></small>
                    </div>
                    <div class="form-group">
                        <label for="newAddress">Địa chỉ:</label>
                        <input type="text" class="form-control" id="newAddress" name="newAddress" required>
                        <small class="text-danger" id="addressError"></small>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveCustomerBtn">Lưu</button>
            </div>
        </div>
    </div>
</div>