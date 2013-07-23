<?php

namespace Manhattan\PorterStemmerBundle\Tools;

use Porter;

/**
 * PorterStemmer
 *
 * @author James Rickard <james@frodosghost.com>
 */
class PorterStemmer
{
    /**
     * @var Porter
     */
    private $porterStemmer;

    private $exclusions = array(
        'i', 'me', 'my', 'myself', 'we', 'our', 'ours', 'ourselves', 'you', 'your', 'yours',
        'yourself', 'yourselves', 'he', 'him', 'his', 'himself', 'she', 'her', 'hers',
        'herself', 'it', 'its', 'itself', 'they', 'them', 'their', 'theirs', 'themselves',
        'what', 'which', 'who', 'whom', 'this', 'that', 'these', 'those', 'am', 'is', 'are',
        'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'having', 'do', 'does',
        'did', 'doing', 'a', 'an', 'the', 'and', 'but', 'if', 'or', 'because', 'as', 'until',
        'while', 'of', 'at', 'by', 'for', 'with', 'about', 'against', 'between', 'into',
        'through', 'during', 'before', 'after', 'above', 'below', 'to', 'from', 'up', 'down',
        'in', 'out', 'on', 'off', 'over', 'under', 'again', 'further', 'then', 'once', 'here',
        'there', 'when', 'where', 'why', 'how', 'all', 'any', 'both', 'each', 'few', 'more',
        'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so',
        'than', 'too', 'very'
    );

    public function __construct(Porter $porterStemmer)
    {
        $this->porterStemmer = $porterStemmer;
    }

    /**
     * Takes a phrase or a sentance, breaks it into words
     * then returns an array of stemmed words
     *
     * @param  string $phrase
     * @return array
     */
    public function stemPhrase($phrase)
    {
        $stemmedWords = array();

        // Split into Words
        $words = str_word_count(strtolower($phrase), 1);
        $words = $this->removeExcludedWords($words);

        foreach ($words as $word) {
            // Ignore words/characters with 1-2 characters
            if (strlen($word) <= 2) {
                continue;
            }

            $stemmedWords[] = $this->stemWord($word);
        }

        return $stemmedWords;
    }

    /**
     * Removes excluded words from given array
     *
     * @param  array $words Array of words to be parsed
     * @return array        Cleaned array with excluded words taken way
     */
    public function removeExcludedWords(array $words)
    {
        // Lowercase all words to ensure a match with excluded words
        $words = array_map('strtolower', $words);

        return array_diff($words, $this->exclusions);
    }

    /**
     * Stems the Word and corrects word to UTF-8 encoding
     *
     * @param  string $word
     * @return string
     */
    private function stemWord($word)
    {
        $stemmedWord = Porter::stem($word);

        if ((mb_detect_encoding($stemmedWord) !== "UTF-8") || !(mb_check_encoding($stemmedWord, "UTF-8"))) {
            $stemmedWord = utf8_encode($stemmedWord);
        }

        return $stemmedWord;
    }

}
