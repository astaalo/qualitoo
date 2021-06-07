<?php

namespace App\QuickMakingBundle\Twig;

use App\Service\Block;
use Twig_Extension;
use Twig_Function_Method;

class BlockExtension extends Twig_Extension {
	
    protected $block;

    public function __construct(Block $block) {
        $this->block = $block;
    }

    /*public function getFunctions() {
        return array(
            'block_render' => new Twig_Function_Method( $this, 'renderBlock', array('is_safe' => array('html')))
        );
    }*/

    public function renderBlock($template, $block, $data = array()) {
        return $this->block->render($template, $block, $data);
    }

    public function getName() {
        return 'block_extension';
    }
}