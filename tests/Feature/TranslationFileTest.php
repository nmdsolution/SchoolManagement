<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Illuminate\Support\Facades\Lang;

class TranslationFileTest extends TestCase
{
    public function test_translation_works_for_english_locale()
    {
        App::setLocale('en');

        $key = 'login';

        $expectedValue = 'Login';

        $translatedValue = __($key);

        $this->assertEquals($expectedValue, $translatedValue, 'English translation is incorrect.');
    }

    /**
     * Test that translations work correctly for French.
     *
     * @return void
     */
    public function test_translation_works_for_french_locale()
    {
        App::setLocale('fr');

        $key = 'login';
        $expectedValue = 'Connexion';

        $translatedValue = __($key);

        $this->assertEquals($expectedValue, $translatedValue, 'French translation is incorrect.');
    }

    /**
     * Test that all keys in en.json have corresponding translations in fr.json.
     *
     * @return void
     */
    public function test_translation_files_have_matching_keys()
    {
        $enFilePath = resource_path('lang/en.json');
        $frFilePath = resource_path('lang/fr.json');

        $this->assertFileExists($enFilePath, 'English translation file is missing.');
        $this->assertFileExists($frFilePath, 'French translation file is missing.');

        $enTranslations = json_decode(File::get($enFilePath), true);
        $frTranslations = json_decode(File::get($frFilePath), true);

        $this->assertIsArray($enTranslations, 'Invalid JSON structure in en.json.');
        $this->assertIsArray($frTranslations, 'Invalid JSON structure in fr.json.');

        $enKeys = array_keys($enTranslations);
        $frKeys = array_keys($frTranslations);

        $missingInFr = array_diff($enKeys, $frKeys);
        $this->assertEmpty($missingInFr, 'Missing keys in fr.json: ' . implode(', ', $missingInFr));

        $extraInFr = array_diff($frKeys, $enKeys);
        $this->assertEmpty($extraInFr, 'Extra keys in fr.json: ' . implode(', ', $extraInFr));
    }
}
