<?php
namespace App\MainBundle\Twig;

class AssetsExtension extends \Symfony\Bridge\Twig\Extension\AssetExtension
{
    public function getAssetUrl($path, $packageName = null, $absolute = false, $version = null)
    {
        return parent::getAssetUrl('/' . $path, $packageName, $absolute, $version);
    }
}
