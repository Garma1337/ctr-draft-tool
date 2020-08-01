<?php

declare(strict_types = 1);

namespace DraftTool\Services;

use InvalidArgumentException;

/**
 * Service to handle translations
 * @author Garma
 */
class Translator
{
    /**
     * Available languages
     */
    public const LANGUAGE_ENGLISH   = 'english';
    public const LANGUAGE_GERMAN    = 'german';
    public const LANGUAGE_SPANISH   = 'spanish';
    
    /**
     * @var string
     */
    private string $translationDir;
    
    /**
     * @param string $translationDir
     */
    public function __construct(string $translationDir)
    {
        $this->translationDir = $translationDir;
    }
    
    /**
     * Translates a language variable
     * @param string $variable
     * @param string $language
     * @return string
     */
    public function translate(string $variable, ?string $language = null): string
    {
        if ($language !== null && !$this->languageExists($language)) {
            throw new InvalidArgumentException('Language "' . $language . '" does not exist.');
        }
        
        if ($language === null) {
            $language = $this->getCurrentLanguage();
        }
        
        $englishTexts = include $this->translationDir . 'english.php';
        $languageTexts = include $this->translationDir . $language . '.php';
        
        return $languageTexts[$variable] ?? $englishTexts[$variable];
    }
    
    /**
     * Checks if a language exists
     * @param string $language
     * @return bool
     */
    public function languageExists(string $language): bool
    {
        $languages = array_flip($this->getLanguages());
        
        return (isset($languages[$language]));
    }
    
    /**
     * Returns an array with all existing languages
     * @return array
     */
    public function getLanguages(): array
    {
        $languages = [];
        
        foreach (scandir($this->translationDir) as $translationFilename) {
            if ($translationFilename !== '.' && $translationFilename !== '..') {
                $splittedFilename = explode('.', $translationFilename);
                
                $languages[] = $splittedFilename[0];
            }
        }
        
        return $languages;
    }
    
    /**
     * Returns the current language
     * @return string
     */
    public function getCurrentLanguage(): string
    {
        $getCurrentLanguage = $_SESSION['language'] ?? '';
        
        if (!$this->languageExists($getCurrentLanguage)) {
            /* English is the default language */
            $getCurrentLanguage = self::LANGUAGE_ENGLISH;
        }
        
        return $getCurrentLanguage;
    }
}
