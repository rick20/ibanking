<?php

namespace Rick20\IBanking\Contracts;

interface Parser
{
    /**
     * Set the parser with a string of document
     *
     * @param  string  $document
     * @return \Rick20\IBanking\Contracts\Parser
     */
    public function make($document);

    /**
     * Parse the HTML page
     *
     * @param  string  $xpath
     * @return \Rick20\IBanking\Contracts\Parser
     */
    public function parse($xpath);

    /**
     * Check if this current node is exist
     *
     * @return bool
     */
    public function found();

    /**
     * Calls an anonymous function on each node of the list.
     *
     * The anonymous function receives the position and the node wrapped
     * in a Parser instance as arguments.
     *
     * @param \Closure $closure An anonymous function
     * @return array An array of values returned by the anonymous function
     */
    public function each(\Closure $closure);

    /**
     * Returns the attribute value of the first node of the list.
     *
     * @param string $attribute The attribute name
     * @return string|null The attribute value or null if the attribute does not exist
     * @throws \InvalidArgumentException When current node is empty
     */
    public function attr($attribute);

    /**
     * Returns the node value of the first node of the list.
     *
     * @return string The node value
     * @throws \InvalidArgumentException When current node is empty
     */
    public function text();

    /**
     * Returns the first node of the list as HTML.
     *
     * @return string The node html
     * @throws \InvalidArgumentException When current node is empty
     */
    public function html();
}
