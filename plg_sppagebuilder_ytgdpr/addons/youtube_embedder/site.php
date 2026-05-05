<?php

//no direct accees
defined('_JEXEC') or die('Restricted Aceess');

class SppagebuilderAddonYoutube_embedder extends SppagebuilderAddons
{

    public function render()
    {
        $youtube_url      = (isset($this->addon->settings->youtube_url) && $this->addon->settings->youtube_url) ? $this->addon->settings->youtube_url : '';

        $output = '';
        $output .= 'Hey Hello! I heard you want to render a YouTube video. But I am still under development. Please wait for the next update.';
        $output .= 'Url: ' . $youtube_url;

        return $output;
    }

    public function css()
    {
        $css = '';

        return $css;
    }
}
