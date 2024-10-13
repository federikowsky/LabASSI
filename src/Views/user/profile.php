<div class="top-bg">
    <div class="container" id="container">
        <h1 class="textCenter">Profilo</h1>
    </div>
    <div class="container rounded bg-white mt-5 mb-5">
        <div class="row">
            <div class="col-md-4 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <img class="rounded-circle mt-5" width="150px"
                        src="https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg">
                    <span class="font-weight-bold" id="user-profile"><?= $username ?></span>
                    <span class="text-black-50" id="email-profile"><?= $email ?></span>
                    <span> </span>
                </div>
                <div class="mb-5">
                    <div class="border-top d-flex align-items-center py-3 setting-side" id="reset-password">
                        <div class="px-1">
                            <i class="fas fa-cogs" style="color: #000;"></i>
                        </div>
                        <div class="px-1">
                            Reset Password
                        </div>
                    </div>
                    <div class="border-top border-bottom d-flex align-items-center py-3 setting-side" id="logout">
                        <div class="px-1">
                            <i class="fas fa-sign-out-alt" style="color: #000;"></i>
                        </div>
                        <div class="px-1">
                            <form action="/auth/logout" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" style="all: unset; cursor: pointer;">Logout</>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col d-flex justify-content-center setting-container">
                <div class="p-3 py-5">
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <h4 class="text-right">Reset Password</h4>
                    </div>
                    <form class="row mt-3 d-flex justify-content-center px-5" method="post" action="/auth/password/update">
                        <?= csrf_field() ?>
                        <div class="col-md-12">
                            <label class="labels">Old Password</label>
                            <input type="password" class="<?= error_class($errors, 'old_password') ?> form-control" placeholder="Enter Old Password" name="old_password" id="old_password" required>
                            <small><?= $errors['old_password'] ?? '' ?></small>
                        </div>
                        <div class="col-md-12">
                            <label class="labels">New Password</label>
                            <input type="password" class="<?= error_class($errors, 'password') ?> form-control" placeholder="Enter New Password" name="password" id="password" required>
                            <small><?= $errors['password'] ?? '' ?></small>
                        </div>
                        <div class="col-md-12">
                            <label class="labels">Repeat Password</label>
                            <input type="password" class="<?= error_class($errors, 'password2') ?> form-control" placeholder="Repeat Password" name="password2" id="password2" required>
                            <small><?= $errors['password2'] ?? '' ?></small>
                        </div>
                        <div class="mt-5 text-center save-button px-5">
                            <button class="btn btn-primary profile-button" type="submit">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>