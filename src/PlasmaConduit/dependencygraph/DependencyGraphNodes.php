<?php
namespace PlasmaConduit\dependencygraph;
use PlasmaConduit\Map;
use PlasmaConduit\dependencygraph\DependencyGraphNode;

class DependencyGraphNodes {

    private $_siblings;

    public function __construct() {
        $this->_siblings = new Map();
    }

    public function hasSibling(DependencyGraphNode $node) {
        return $this->getSibling($node)->nonEmpty();
    }

    public function addSibling(DependencyGraphNode $node) {
        if (!$this->hasSibling($node)) {
            $this->_siblings->push($node);
            return true;
        } else {
            return false;
        }
    }

    public function getSibling(DependencyGraphNode $node) {
        return $this->_siblings->findValue(function($value, $key) use($node) {
            return $value->getName() == $node->getName();
        });
    }

    public function getAllSiblings() {
        return $this->_siblings;
    }

    public function hasDependency(DependencyGraphNode $node) {
        return $this->getDependencyNodes($node)->length() !== 0;
    }

    public function addDependency(DependencyGraphNode $parent,
                                  DependencyGraphNode $node)
    {
        $this->getDependencyNodes($parent)->each(function($val, $k) use($node) {
            $val->addDependency($node);
        });
    }

    public function getDependencyNodes(DependencyGraphNode $node) {
        $init = new Map();
        return $this->_siblings->reduce($init, function($memo, $n) use($node) {
            if ($n->getName() == $node->getName()) {
                $memo->push($node);
            } else {
                $memo = $memo->merge($n->getDependencyNodes($node));
            }
            return $memo;
        });
    }

    public function toArray() {
        return $this->_siblings->map(function($value, $key) {
            return $value->toArray();
        })->toArray();
    }

    public function flatten() {
        $siblings = $this->_siblings->map(function($value, $key) {
            return $value->flatten();
        })->toArray();
        return array_reduce($siblings, function($memo, $sibling) {
            return array_merge($memo, $sibling);
        }, []);
    }

}