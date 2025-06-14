<?php

declare(strict_types=1);

namespace Unit;

use PHPUnit\Framework\TestCase;
use Core\Validator\Validator;

class ValidatorTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequiredRulePasses(): void
    {
        $validator = new Validator(['name' => 'John'], ['name' => 'required']);
        $this->assertFalse($validator->fails());
        $this->assertEmpty($validator->errors());
    }

    /**
     * @return void
     */
    public function testRequiredRuleFails(): void
    {
        $validator = new Validator([], ['name' => 'required']);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors());
    }

    /**
     * @return void
     */
    public function testEmailValidation(): void
    {
        $validator = new Validator(['email' => 'user@example.com'], ['email' => 'email']);
        $this->assertFalse($validator->fails());

        $invalidValidator = new Validator(['email' => 'not-an-email'], ['email' => 'email']);
        $this->assertTrue($invalidValidator->fails());
    }

    /**
     * @return void
     */
    public function testMinValidation(): void
    {
        $validator = new Validator(['username' => 'admin'], ['username' => 'min:3']);
        $this->assertFalse($validator->fails());

        $validator = new Validator(['username' => 'a'], ['username' => 'min:3']);
        $this->assertTrue($validator->fails());
    }

    /**
     * @return void
     */
    public function testMaxValidation(): void
    {
        $validator = new Validator(['username' => 'john'], ['username' => 'max:10']);
        $this->assertFalse($validator->fails());

        $validator = new Validator(['username' => 'verylongusername'], ['username' => 'max:5']);
        $this->assertTrue($validator->fails());
    }

    /**
     * @return void
     */
    public function testDateFormatValidation(): void
    {
        $validator = new Validator(['birthday' => '2024-01-01'], ['birthday' => 'dateFormat:Y-m-d']);
        $this->assertFalse($validator->fails());

        $validator = new Validator(['birthday' => '01-01-2024'], ['birthday' => 'dateFormat:Y-m-d']);
        $this->assertTrue($validator->fails());
    }

    /**
     * @return void
     */
    public function testMimesValidation(): void
    {
        $file = ['name' => 'document.pdf'];
        $validator = new Validator(['file' => $file], ['file' => 'mimes:pdf,jpg']);
        $this->assertFalse($validator->fails());

        $file = ['name' => 'image.bmp'];
        $validator = new Validator(['file' => $file], ['file' => 'mimes:pdf,jpg']);
        $this->assertTrue($validator->fails());
    }

    /**
     * @return void
     */
    public function testMimetypesValidation(): void
    {
        $tmpFile = tmpfile();
        $meta = stream_get_meta_data($tmpFile);
        $tmpFilePath = $meta['uri'];
        fwrite($tmpFile, 'test');

        $file = ['tmp_name' => $tmpFilePath];

        $validator = new Validator(['file' => $file], ['file' => 'mimetypes:text/plain']);
        $this->assertFalse($validator->fails());

        $validator = new Validator(['file' => $file], ['file' => 'mimetypes:image/png']);
        $this->assertTrue($validator->fails());

        fclose($tmpFile);
    }

    /**
     * @return void
     */
    public function testInValidation(): void
    {
        $validator = new Validator(['status' => 'active'], ['status' => 'in:active,inactive,pending']);
        $this->assertFalse($validator->fails());
        $this->assertEmpty($validator->errors());

        $validator = new Validator(['status' => 'archived'], ['status' => 'in:active,inactive,pending']);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors());
    }
}