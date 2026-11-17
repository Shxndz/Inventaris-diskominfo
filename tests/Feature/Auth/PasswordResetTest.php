<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.username' => 'test-user',
            'mail.mailers.smtp.password' => 'test-password',
        ]);
    }

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
    }

    public function test_reset_password_token_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->from(route('password.request', absolute: false))
            ->post(route('password.email'), ['email' => $user->email]);

        $response->assertRedirect(route('password.request', absolute: false))
            ->assertSessionHas('status')
            ->assertSessionHas('password_reset_email', $user->email);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user): bool {
            $message = $notification->toMail($user);

            $this->assertSame('Kode Reset Password', $message->subject);
            $this->assertContains($notification->token, $message->introLines);

            return true;
        });
    }

    public function test_reset_request_reports_unconfigured_mailer_instead_of_claiming_delivery(): void
    {
        config(['mail.default' => 'log']);
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->from(route('password.request', absolute: false))
            ->post(route('password.email'), ['email' => $user->email]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_reset_request_reports_missing_smtp_credentials_instead_of_claiming_delivery(): void
    {
        config([
            'mail.mailers.smtp.username' => null,
            'mail.mailers.smtp.password' => null,
        ]);
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->from(route('password.request', absolute: false))
            ->post(route('password.email'), ['email' => $user->email]);

        $response->assertSessionHasErrors([
            'email' => 'Pengiriman email belum siap. Isi MAIL_USERNAME dan MAIL_PASSWORD pada konfigurasi SMTP.',
        ]);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_valid_reset_token_redirects_to_the_new_password_screen(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->from(route('password.request', absolute: false))
            ->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->post(route('password.token.verify'), [
                'token' => $notification->token,
            ]);

            $response->assertRedirect(route('password.reset', absolute: false));
            $this->get(route('password.reset'))->assertOk();

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->from(route('password.request', absolute: false))
            ->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $verifyResponse = $this->post(route('password.token.verify'), [
                'token' => $notification->token,
            ]);

            $verifyResponse->assertRedirect(route('password.reset', absolute: false));

            $response = $this->post(route('password.store'), [
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });

        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
        $this->assertTrue(Hash::check('new-password-123', $user->refresh()->password));
    }

    public function test_invalid_reset_token_returns_to_the_code_entry_screen(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->from(route('password.request', absolute: false))
            ->post(route('password.email'), ['email' => $user->email]);

        $response = $this->from(route('password.request', absolute: false))
            ->post(route('password.token.verify'), [
                'token' => 'invalid-token',
            ]);

        $response->assertRedirect(route('password.request', absolute: false))
            ->assertSessionHasErrors('token')
            ->assertSessionHas('password_reset_email', $user->email);
    }

    public function test_new_password_screen_requires_a_verified_reset_token(): void
    {
        $response = $this->get(route('password.reset'));

        $response->assertRedirect(route('password.request', absolute: false))
            ->assertSessionHasErrors('email');
    }
}
