<?php

declare(strict_types=1);

namespace Unit;

use PHPUnit\Framework\TestCase;
use Core\Translator\Translator;
use App\domain\Common\Domain\Exceptions\NotLoadFileTranslatorException;
use ReflectionClass;

class TranslatorTest extends TestCase
{
    /**
     * @var string
     */
    protected string $langPath;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->langPath = sys_get_temp_dir() . '/lang_test';

        if (!file_exists($this->langPath)) {
            mkdir($this->langPath, 0777, true);
        }

        file_put_contents($this->langPath . '/en.php', "<?php return ['hello' => 'Hello', 'greet_user' => 'Hello, :name!'];");
        file_put_contents($this->langPath . '/ru.php', "<?php return ['hello' => 'Привет', 'greet_user' => 'Привет, :name!'];");

        $ref = new ReflectionClass(Translator::class);
        $instanceProp = $ref->getProperty('instance');
        $instanceProp->setValue(null, null);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        array_map('unlink', array_filter((array)glob($this->langPath . '/*.php')));
        Translator::destroy();
    }

    /**
     * @return void
     */
    public function testCanLoadTranslationAndGetText()
    {
        Translator::init($this->langPath, 'en');
        $this->assertEquals('Hello', Translator::get('hello'));
    }

    /**
     * @return void
     */
    public function testReturnsKeyIfTranslationMissing()
    {
        Translator::init($this->langPath, 'en');
        $this->assertEquals('unknown_key', Translator::get('unknown_key'));
    }

    /**
     * @return void
     */
    public function testCanReplacePlaceholdersInTranslation()
    {
        Translator::init($this->langPath, 'en');
        $this->assertEquals('Hello, John!', Translator::get('greet_user', ['name' => 'John']));
    }

    /**
     * @return void
     */
    public function testCanSwitchLocaleDynamically()
    {
        Translator::init($this->langPath, 'en');
        $this->assertEquals('Привет', Translator::get('hello', [], 'ru'));
    }

    /**
     * @return void
     */
    public function testThrowsExceptionIfTranslationFileMissing()
    {
        $this->expectException(NotLoadFileTranslatorException::class);
        Translator::init($this->langPath, 'fr'); // fr.php не существует
    }
}