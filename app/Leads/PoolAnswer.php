<?php

namespace App\Leads;

use Str;

final class PoolAnswer
{
    /**
     * Answer raw array
     *
     * @var array
     */
    protected $answer;

    /**
     * PoolAnswer constructor.
     *
     * @param array $answer
     *
     * @return void
     */
    public function __construct($answer = [])
    {
        $this->answer = $answer;
    }

    /**
     * Get answered question
     *
     * @return string
     */
    public function getQuestion(): string
    {
        if (is_string($this->answer)) {
            return  Str::before($this->answer, 'Ответ:');
        }

        return $this->answer['q']
            ?? $this->answer['question']['title']
            ?? $this->answer['question_title']
            ?? $this->answer['name']
            ?? $this->answer['question_text']
            ?? $this->answer['question']['title']
            ?? 'Unavailable';
    }

    /**
     * Get answer on question
     *
     * @return string
     */
    public function getAnswer(): string
    {
        if (is_string($this->answer)) {
            return Str::after($this->answer, 'Ответ:');
        }

        return $this->answer['a']
        ?? $this->answer['answer']['title']
        ?? $this->answer['answer'][0]['title']
        ?? $this->answer['answer']['answer_value']
        ?? $this->answer['value']
        ?? $this->answer['answers_given'][0]['response_text']
        ?? $this->answer['answer']['title']
        ?? 'Unavailable';
    }
}
