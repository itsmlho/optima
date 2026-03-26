<?php

namespace Tests\Unit\Auth;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Auth form validation unit tests.
 *
 * These are pure unit tests — no database or HTTP calls required.
 * They verify that the CodeIgniter validation rules used by the
 * registration and login forms behave correctly for edge cases.
 */
final class AuthValidationTest extends CIUnitTestCase
{
    private \CodeIgniter\Validation\ValidationInterface $validation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validation = service('validation');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->validation->reset();
    }

    // -------------------------------------------------------------------------
    // Login form
    // -------------------------------------------------------------------------

    public function testLoginFailsWithEmptyUsername(): void
    {
        $this->validation->setRules(['username' => 'required', 'password' => 'required']);
        $result = $this->validation->run(['username' => '', 'password' => 'secret123']);

        $this->assertFalse($result, 'Validation should fail when username is empty');
        $this->assertArrayHasKey('username', $this->validation->getErrors());
    }

    public function testLoginFailsWithEmptyPassword(): void
    {
        $this->validation->setRules(['username' => 'required', 'password' => 'required']);
        $result = $this->validation->run(['username' => 'user@example.com', 'password' => '']);

        $this->assertFalse($result, 'Validation should fail when password is empty');
        $this->assertArrayHasKey('password', $this->validation->getErrors());
    }

    public function testLoginPassesWithValidCredentialShape(): void
    {
        $this->validation->setRules(['username' => 'required', 'password' => 'required']);
        $result = $this->validation->run(['username' => 'user@example.com', 'password' => 'secret123']);

        $this->assertTrue($result, 'Validation should pass with non-empty username and password');
    }

    // -------------------------------------------------------------------------
    // Registration form
    // -------------------------------------------------------------------------

    public function testRegistrationFailsWithInvalidEmail(): void
    {
        $this->validation->setRules(['email' => 'required|valid_email']);
        $result = $this->validation->run(['email' => 'not-an-email']);

        $this->assertFalse($result, 'Validation should fail for invalid email format');
        $this->assertArrayHasKey('email', $this->validation->getErrors());
    }

    public function testRegistrationFailsWithPasswordTooShort(): void
    {
        $this->validation->setRules(['password' => 'required|min_length[8]']);
        $result = $this->validation->run(['password' => 'short']);

        $this->assertFalse($result, 'Validation should fail when password is under 8 characters');
    }

    public function testRegistrationFailsWhenPasswordsDoNotMatch(): void
    {
        $this->validation->setRules([
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ]);
        $result = $this->validation->run([
            'password'         => 'securePass1',
            'password_confirm' => 'differentPass',
        ]);

        $this->assertFalse($result, 'Validation should fail when passwords do not match');
        $this->assertArrayHasKey('password_confirm', $this->validation->getErrors());
    }

    public function testRegistrationPassesWithValidData(): void
    {
        $this->validation->setRules([
            'first_name'       => 'required|min_length[2]|max_length[50]',
            'last_name'        => 'required|min_length[2]|max_length[50]',
            'username'         => 'required|min_length[3]|max_length[20]',
            'email'            => 'required|valid_email',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ]);

        $result = $this->validation->run([
            'first_name'       => 'John',
            'last_name'        => 'Doe',
            'username'         => 'johndoe',
            'email'            => 'john@example.com',
            'password'         => 'StrongPass1!',
            'password_confirm' => 'StrongPass1!',
        ]);

        $this->assertTrue($result, 'Validation should pass with all valid registration data');
    }

    public function testRegistrationFailsWithUsernameTooShort(): void
    {
        $this->validation->setRules(['username' => 'required|min_length[3]|max_length[20]']);
        $result = $this->validation->run(['username' => 'ab']);

        $this->assertFalse($result, 'Validation should fail when username is under 3 characters');
    }

    public function testRegistrationFailsWithUsernameTooLong(): void
    {
        $this->validation->setRules(['username' => 'required|min_length[3]|max_length[20]']);
        $result = $this->validation->run(['username' => str_repeat('a', 21)]);

        $this->assertFalse($result, 'Validation should fail when username exceeds 20 characters');
    }

    // -------------------------------------------------------------------------
    // Forgot password form
    // -------------------------------------------------------------------------

    public function testForgotPasswordFailsWithMissingEmail(): void
    {
        $this->validation->setRules(['email' => 'required|valid_email']);
        $result = $this->validation->run(['email' => '']);

        $this->assertFalse($result, 'Validation should fail when email is missing');
    }

    public function testForgotPasswordFailsWithInvalidEmail(): void
    {
        $this->validation->setRules(['email' => 'required|valid_email']);
        $result = $this->validation->run(['email' => 'plainaddress']);

        $this->assertFalse($result, 'Validation should fail with plainaddress as email');
    }

    public function testForgotPasswordPassesWithValidEmail(): void
    {
        $this->validation->setRules(['email' => 'required|valid_email']);
        $result = $this->validation->run(['email' => 'user@company.com']);

        $this->assertTrue($result, 'Validation should pass with a valid email address');
    }
}
