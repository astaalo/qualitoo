<?php
namespace App\MainBundle\Twig;

class AssetsExtension extends \Symfony\Bundle\TwigBundle\Extension\AssetsExtension
{
    public function getAssetUrl($path, $packageName = null, $absolute = false, $version = null)
    {
        return parent::getAssetUrl('/' . $path, $packageName, $absolute, $version);
    }
}
