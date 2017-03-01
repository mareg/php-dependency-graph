<?php
namespace PlasmaConduit\dependencygraph;
use PlasmaConduit\dependencygraph\DependencyGraphNodes;

class DependencyGraphNode {

    private $_name;
    private $_dependencies;

    public function __construct($name) {
        $this->_name         = $name;
        $this->_dependencies = new DependencyGraphNodes();
    }

    public function getName() {
        return $this->_name;
    }

    public function hasDependency(DependencyGraphNode $node) {
        return $this->_dependencies->hasDependency($node);
    }

    public function addDependency(DependencyGraphNode $node) {
        $this->_dependencies->addSibling($node);
    }

    public function getImmediateDependencyNodes() {
        $this->_dependencies->getAllSiblings();
    }

    public function getDependencyNodes(DependencyGraphNode $node) {
        return $this->_dependencies->getDependencyNodes($node);
    }

    public function toArray() {
        $dependencies = $this->_dependencies->toArray();
        if (count($dependencies)) {
            $memo = [];
            $memo[$this->_name] = $dependencies;
            return $memo;
        } else {
            return $this->_name;
        }
    }

    public function flatten() {
        $dependencies       = $this->_dependencies->flatten();
        $memo               = [];
        $memo[$this->_name] = 1;

        if (count($dependencies)) {
            return array_merge($dependencies, $memo);
        } else {
            return $memo;
        }
    }

}