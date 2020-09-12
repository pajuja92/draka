<?php


class Options
{
    private $content;
    private $tip;
    private $score;
    private $scoreMultiplier;
    private $required;

    /**
     * Options constructor.
     * @param $content
     * @param $tip
     * @param $score
     * @param $required
     */
    public function __construct($content, $tip, $score, $required)
    {
        $this->content = $content;
        $this->tip = $tip;
        $this->score = $score;
        $this->required = $required;
    }

    /**
     * @return array
     */
    public function get_options() {
        return array(
            'content'   => $this->content,
            'tip'       => $this->tip,
            'score'     => $this->score,
            'required'   => $this->required
        );
    }

    /**
     * @return int
     */
    public function get_score() {
        return $this->score;
    }
}