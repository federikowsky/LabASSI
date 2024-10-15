<div class="register-body">
	<div class="main">
		<section class="signup">
			<div class="container">
				<div class="signup-content">
					<form method="POST" action="/auth/register" id="registration_form" class="signup-form">
						<h2 class="form-title">Create account</h2>
						<?= csrf_field() ?>
						
						<div class="form-group">
							<label for="username" class="form-label">Username:</label>
							<input type="text" name="username" id="username" placeholder="Your Name" value="<?= $inputs['username'] ?? '' ?>"
								class="<?= error_class($errors, 'username') ?> form-input" required />
							<small><?= $errors['username'] ?? '' ?></small>
						</div>
						<div class="form-group">
							<label for="email" class="form-label">Email:</label>
							<input type="email" name="email" id="email" placeholder="Your Email" value="<?= $inputs['email'] ?? '' ?>"
								class="<?= error_class($errors, 'email') ?> form-input" required />
							<small><?= $errors['email'] ?? '' ?></small>
						</div>
						<div class="form-group">
							<label for="password" class="form-label">Password:</label>
							<div class="input-wrapper">
								<input type="password" name="password" id="password" placeholder="Password" 
									class="<?= error_class($errors, 'password') ?> form-input" required />
								<span toggle="#password" class="field-icon toggle-password">
									<i class="fa fa-eye-slash"></i>
								</span>
							</div>
							<small><?= $errors['password'] ?? '' ?></small>
						</div>

						<div class="form-group">
							<label for="password2" class="form-label">Repeat Password:</label>
							<div class="input-wrapper">
								<input type="password" name="password2" id="password2" placeholder="Repeat your password" 
									class="<?= error_class($errors, 'password2') ?> form-input" required />
								<span toggle="#password2" class="field-icon toggle-password2">
									<i class="fa fa-eye-slash"></i>
								</span>
							</div>
							<small><?= $errors['password2'] ?? '' ?></small>
						</div>

						<div>
							<label for="agree" class="fs-6">
								<input type="checkbox" name="agree" id="agree" value="checked" <?= $inputs['agree'] ?? '' ?> /> I agree with the <a href="#" title="term of services">term of services</a>
							</label>
							<small><?= $errors['agree'] ?? '' ?></small>
						</div>

						<div class="form-group" id="form-group-submit">
							<button type="submit" id="submit" class="form-submit mt-3">Sign Up</button>
						</div>

					</form>
					<!-- Button for Google Sign Up -->
					<div class="d-flex align-items-center flex-column">
						<div class="pb-3">
							<p class="social-text text-white text fs-6">OR</p>
						</div>
						<div class="social-login d-flex flex-column flex-md-row align-items-center">
							<a href="/auth/google" class="provider-btn text-white text-center bg-danger mx-md-1 my-2 my-md-0 w-100 w-md-auto">
								<i class="fab fa-google"></i> Sign in with Google
							</a>
							<a href="#" class="provider-btn text-white text-center bg-primary mx-md-1 my-2 my-md-0 w-100 w-md-auto">
								<i class="fab fa-facebook"></i> Sign in with Facebook
							</a>
						</div>
					</div>
					<p class="loginhere">
						Have already an account ? <a href="/auth/login" class="loginhere-link">Login here</a>
					</p>
				</div>
			</div>
		</section>
	</div>
</div>