<?php
namespace PlasmaConduit;
use PlasmaConduit\either\Left;
use PlasmaConduit\either\Right;
use PlasmaConduit\dependencygraph\DependencyGraphNode;
use PlasmaConduit\dependencygraph\DependencyGraphNodes;
use Exception;

class DependencyGraph {

    private $_roots;
    private $_registry;

    public function __construct() {
        $this->_roots    = new DependencyGraphNodes();
        $this->_registry = new Map();
    }

    public function addRoot(DependencyGraphNode $node) {
        $optionalNode  = $this->_registry->get($node->getName());
        $canonicalNode = $optionalNode->getOrElse($node);
        if ($this->_roots->addSibling($canonicalNode)) {
            if ($optionalNode->isEmpty()) {
                $this->_addNodeToRegistry($canonicalNode);
            }
            return true;
        } else {
            return false;
        }
    }

    public function addDependency(DependencyGraphNode $parent,
                                  DependencyGraphNode $node)
    {
        if (!$this->_hasCircularDependency($parent, $node)) {
            $canonicalParent = $this->_registry->get($parent->getName());
            if ($canonicalParent->nonEmpty()) {
                $optionalNode  = $this->_registry->get($node->getName());
                $canonicalNode = $optionalNode->getOrElse($node);
                $canonicalParent->get()->addDependency($canonicalNode);
                if ($optionalNode->isEmpty()) {
                    $this->_addNodeToRegistry($canonicalNode);
                }
                return new Right(true);
            } else {
                return new Left("Parent node not present in graph.");
            }
            return new Right(true);
        } else {
            return new Left("Refusing to add circular dependency.");
        }
    }

    public function toArray() {
        return $this->_roots->toArray();
    }

    public function flatten() {
        return array_keys($this->_roots->flatten());
    }

    private function _addNodeToRegistry(DependencyGraphNode $node) {
        $this->_registry->set($node->getName(), $node);
    }

    private function _hasCircularDependency(DependencyGraphNode $parent,
                                            DependencyGraphNode $node)
    {
        $node = $this->_registry->get($node->getName());
        return $node->nonEmpty() && $node->get()->hasDependency($parent);
    }

}