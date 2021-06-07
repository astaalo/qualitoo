<?php

namespace App\Model;

use App\Entity\Structure;

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
     * @param \App\Model\TreeInterface $tree
     * @return Structure
     */
    public function addChildren($children);
    
    /**
     * Remove children
     *
     * @param \App\Model\TreeInterface $children
     */
    public function removeChildren($children);
    
    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren();
}
