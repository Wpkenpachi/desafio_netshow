<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class ContactMessageTest extends TestCase
{
    use WithoutMiddleware;

    public function test_postWithSuccess () {
        echo "\nTestando Caso de Sucesso!\n";
        $file = UploadedFile::fake()->create('public/asset/mycv.pdf', 450);

        $response = $this->withHeaders([
            "Content-Type" => "multipart/form-data",
        ])->json('POST', '/api/contact/send', [
            'name'          => 'Wesley',
            'email'         => 'wesley@email.com',
            'message'       => 'This is a test message',
            'phone'         => '(99)99999-9999',
            'attached_file' => $file
        ]);

        $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
        ]);
    }

    public function test_postWithOutName() {
        echo "Testando Caso de envio sem o campo Nome!\n";
        $file = UploadedFile::fake()->create('public/asset/mycv.pdf', 450);

        $response = $this->withHeaders([
            "Content-Type" => "multipart/form-data",
        ])->json('POST', '/api/contact/send', [
            'name'          => null,
            'email'         => 'wesley@email.com',
            'message'       => 'This is a test message',
            'phone'         => '(99)99999-9999',
            'attached_file' => $file
        ]);

        $response->assertJson([
            "name" => [
                "The name field is required."
            ]
        ]);
    }

    public function test_postWithOutEmail() {
        echo "Testando Caso de envio sem o campo Email!\n";
        $file = UploadedFile::fake()->create('public/asset/mycv.pdf', 450);

        $response = $this->withHeaders([
            "Content-Type" => "multipart/form-data",
        ])->json('POST', '/api/contact/send', [
            'name'          => 'Wesley',
            'email'         => null,
            'message'       => 'This is a test message',
            'phone'         => '(99)99999-9999',
            'attached_file' => $file
        ]);

        $response->assertJson([
            "email" => [
                "The email field is required."
            ]
        ]);
    }

    public function test_postWithOutMessage() {
        echo "Testando Caso de envio sem o campo Message!\n";
        $file = UploadedFile::fake()->create('public/asset/mycv.pdf', 450);

        $response = $this->withHeaders([
            "Content-Type" => "multipart/form-data",
        ])->json('POST', '/api/contact/send', [
            'name'          => 'Wesley',
            'email'         => 'wesley@email.com',
            'message'       => null,
            'phone'         => '(99)99999-9999',
            'attached_file' => $file
        ]);

        $response->assertJson([
            "message" => [
                "The message field is required."
            ]
        ]);
    }

    public function test_postWithOutPhone() {
        echo "Testando Caso de envio sem o campo Phone!\n";
        $file = UploadedFile::fake()->create('public/asset/mycv.pdf', 450);

        $response = $this->withHeaders([
            "Content-Type" => "multipart/form-data",
        ])->json('POST', '/api/contact/send', [
            'name'          => 'Wesley',
            'email'         => 'wesley@email.com',
            'message'       => 'This is a test message',
            'phone'         => null,
            'attached_file' => $file
        ]);

        $response->assertStatus(200)
        ->assertJson([
            "phone" => [
                "The phone field is required."
            ]
        ]);
    }

    public function test_postWithInvalidPhone() {
        echo "Testando Caso de envio com o campo Phone Inválido!\n";
        $file = UploadedFile::fake()->create('public/asset/mycv.pdf', 450);

        $response = $this->withHeaders([
            "Content-Type" => "multipart/form-data",
        ])->json('POST', '/api/contact/send', [
            'name'          => 'Wesley',
            'email'         => 'wesley@email.com',
            'message'       => 'This is a test message',
            'phone'         => '999999-99',
            'attached_file' => $file
        ]);

        $response->assertStatus(200)
        ->assertJson([
            "phone" => [
                "O campo phone está com valor inválido"
            ]
        ]);
    }

    public function test_postWithOutFile() {
        echo "Testando Caso de envio sem o campo File!\n";
        $response = $this->withHeaders([
            "Content-Type" => "multipart/form-data",
        ])->json('POST', '/api/contact/send', [
            'name'          => 'Wesley',
            'email'         => 'wesley@email.com',
            'message'       => 'This is a test message',
            'phone'         => '(99)99999-9999'
        ]);

        $response->assertStatus(200)
        ->assertJson([
            "attached_file" => [
                "The attached file field is required."
            ]
        ]);
    }

    public function test_postWithFileOverloadingSize() {
        echo "Testando Caso de envio do arquivo com tamanho maior que o permitido!\n";
        $file = UploadedFile::fake()->create('public/asset/mycv.pdf', 600);

        $response = $this->withHeaders([
            "Content-Type" => "multipart/form-data",
        ])->json('POST', '/api/contact/send', [
            'name'          => 'Wesley',
            'email'         => 'wesley@email.com',
            'message'       => 'This is a test message',
            'phone'         => '(99)99999-9999',
            'attached_file' => $file
        ]);

        $response->assertStatus(200)
        ->assertJson([
            "attached_file" => [
                "The attached file may not be greater than 500 kilobytes."
            ]
        ]);
    }

    public function test_postWithNotAllowedExt() {
        echo "Testando Caso de envio de arquivo com extrensão não permitida!\n";
        $file = UploadedFile::fake()->create('public/asset/image.png');

        $response = $this->withHeaders([
            "Content-Type" => "multipart/form-data",
        ])->json('POST', '/api/contact/send', [
            'name'          => 'Wesley',
            'email'         => 'wesley@email.com',
            'message'       => 'This is a test message',
            'phone'         => '(99)99999-9999',
            'attached_file' => $file
        ]);

        $response->assertStatus(200)
        ->assertJson([
            "attached_file" => [
                "The attached file must be a file of type: docx, doc, pdf, odt, txt."
            ]
        ]);
    }
}
