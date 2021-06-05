<?php

namespace App\Model;

interface TreeInterface
{
    /**
     * Set Parent of Node
     *
     * @param TreeInterface $tree
     *
     */
    public function setParent($tree);
    
    /**
     * Get Parent of Node
     *
     * @return self
     */
    public function getParent();
    
    /**
     * Add children
     *
     * @param \Orange\MainBundle\Model\TreeInterface $tree
     * @return Structure
     */
    public function addChildren($children);
    
    /**
     * Remove children
     *
     * @param \Orange\MainBundle\Model\TreeInterface $children
     */
    public function removeChildren($children);
    
    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren();
}
