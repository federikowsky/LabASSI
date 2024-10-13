<div class="login-body">
	<div class="main">
		<section class="signin">
			<div class="container">
				<div class="signin-content">
					<form action="/auth/login" method="post" id="login_form" class="signin-form">
						<h2 class="form-title">Login</h2>
						<?= csrf_field() ?>
						<div class="form-group">
							<label for="username" class="form-label">Username:</label>
							<input type="text" name="username" id="username" class="form-input"
								value="<?= $inputs['username'] ?? '' ?>" placeholder="Your Username" required />
							<small>
								<?= $errors['username'] ?? '' ?>
							</small>
						</div>

						<div class="form-group">
							<label for="password" class="form-label">Password:</label>
							<input type="password" name="password" id="password" class="form-input"
								placeholder="Password" required />
							<span toggle="#password" class="field-icon toggle-password">
								<i class="fa fa-eye-slash"></i>
							</span>
							<small>
								<?= $errors['password'] ?? '' ?>
							</small>
						</div>

						<div>
							<label for="remember_me">
								<input type="checkbox" name="remember_me" id="remember_me" value="checked"
									<?=$inputs['remember_me'] ?? '' ?> />
								Remember Me
							</label>
							<small>
								<?= $errors['agree'] ?? '' ?>
							</small>
						</div>

						<div class="form-group" id="form-group-submit">
							<button type="submit" id="submit" class="form-submit mt-3">Sign In</button>
						</div>

					</form>
					<div class="other-options text-center">
						<p class="text-white pt-1"> Don't have already an account ? 
							<a href="/auth/register" class="registerhere-link">Register here</a>
						</p>
						<p class="text-white pt-1"> Did you forgot your password? 
							<a href="/auth/forgot_password" class="registerhere-link">Forgot Password</a>
						</p>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>