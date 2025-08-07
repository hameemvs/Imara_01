<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Users.php';

$userModel = new User();
$table = $userModel->getTableName();
$data = $userModel->getAll();
// if ($Role != 'Admin') dd('Access Denied...!');
?>

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Users
        <button
            type="button"
            class="btn btn-primary float-end"
            data-bs-toggle="modal"
            data-bs-target="#modalCenter">
            Add New User
        </button>
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header">Users</h5>
        <div class="m-4">
            <div id="delete-alert-container">
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>User Name</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Permission</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    foreach ($data as $key => $user) {
                        // print_r($data);die;
                    ?>
                        <tr>
                            <td>
                                <?php if ($user['ID'] != $userId) { ?>
                                    <div>
                                            <a class=" btn btn-sm  btn-outline-dark m-2 edit-user-btn" data-id="<?= $user['ID']; ?>"><i class="bx bx-edit  btn-outline-dark"></i> </a>
                                            <a class=" btn btn-sm  btn-outline-dark m-2 delete-user-btn" data-permission="<?= $user['Role']; ?>" data-id="<?= $user['ID']; ?>">
                                                <i class="bx bx-trash btn-outline-dark"></i> </a>
                                    </div>
                                <?php } ?>
                            </td>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $user['Username'] ?? '' ?></strong></td>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $user['FullName'] ?? '' ?></strong></td>
                            <td><?= $user['Email'] ?? '' ?></td>
                            <td>
                                <span class="text-capitalize"> <?= $user['Role'] ?? '' ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->

    <hr class="my-5" />


</div>

<!-- Modal -->
<div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="create-form" action="<?= url('services/ajax_functions.php') ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Add New User</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_user">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">User Name</label>
                            <input
                                type="text"
                                required
                                id="nameWithTitle"
                                name="user_name"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Full Name</label>
                            <input
                                type="text"
                                required
                                id="nameWithTitle"
                                name="full_name"
                                class="form-control"
                                placeholder="Enter Full Name" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="emailWithTitle" class="form-label">Email</label>
                            <input
                                required
                                type="text"
                                name="email"
                                id="emailWithTitle"
                                class="form-control"
                                placeholder="xxxx@xxx.xx" />
                        </div>
                    </div>
                    <div class="row gy-2">
                        <div class="col orm-password-toggle">
                            <label class="form-label" for="basic-default-password1">Password</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    required
                                    name="password"
                                    class="form-control"
                                    id="passwordInput"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="basic-default-password1" />
                                <span id="basic-default-password1" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                        <div class="col form-password-toggle">
                            <label class="form-label" for="basic-default-password2">Confirm Password</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    required
                                    name="confirm_password"
                                    class="form-control"
                                    id="confirmPasswordInput"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="basic-default-password2" />
                                <span id="basic-default-password2" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Role</label>
                            <select class="form-select" id="role" aria-label="Default select example" name="role" required>
                                <option value="admin">admin</option>
                                <option value="member">member</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="additional-fields">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="alert-container"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="create">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Udpate Modal -->
<div class="modal fade" id="edit-user-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="update-form" action="<?= url('services/ajax_functions.php') ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Update User</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" id="id" name="id" value="">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">User Name</label>
                            <input
                                type="text"
                                required
                                id="user-name"
                                name="user_name"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Full Name</label>
                            <input
                                type="text"
                                required
                                id="FullName"
                                name="full_name"
                                class="form-control"
                                placeholder="Enter Full Name" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="emailWithTitle" class="form-label">Email</label>
                            <input
                                required
                                type="text"
                                name="email"
                                id="email"
                                class="form-control"
                                placeholder="xxxx@xxx.xx" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Role</label>
                            <select class="form-select" id="edit_Role" aria-label="Default select example" name="role" required>
                                <option value="admin">admin</option>
                                <option value="member">member</option>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="mb-3 mt-3">
                        <div id="edit-additional-fields">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="edit-additional-fields"></div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="update-user">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/user.js') ?>"></script>