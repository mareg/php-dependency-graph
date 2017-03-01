Dependency Graph
================
A dependency graph implementation.

Examples
--------
```php
<?php
use PlasmaConduit\DependencyGraph;
use PlasmaConduit\dependencygraph\DependencyGraphNode;
use PlasmaConduit\dependencygraph\DependencyGraphNodes;

// Initialize the graph and some stand alone nodes
$graph = new DependencyGraph();
$nodeA = new DependencyGraphNode("A");
$nodeB = new DependencyGraphNode("B");
$nodeC = new DependencyGraphNode("C");
$nodeD = new DependencyGraphNode("D");
$nodeE = new DependencyGraphNode("E");
$nodeF = new DependencyGraphNode("F");
$nodeG = new DependencyGraphNode("G");

// Add the root node A
$graph->addRoot($nodeA);
$graph->addDependency($nodeA, $nodeB);
$graph->addDependency($nodeA, $nodeC);
$graph->addDependency($nodeB, $nodeD);
$graph->addDependency($nodeC, $nodeE);
$graph->addDependency($nodeC, $nodeF);
// Tree Status:
//       A
//      / \
//     B   C
//    /   / \
//   D   E   F

echo json_encode($graph->toArray(), JSON_PRETTY_PRINT);
// Outputs:
//  [
//      {
//          "A": [
//              {
//                  "B": [
//                      "D"
//                  ]
//              },
//              {
//                  "C": [
//                      "D",
//                      "E"
//                  ]
//              }
//          ]
//      }
//  ]

// Try to create a circular dependency by making E dependent on A
// This should fail and refuse to fulfill the dependency
$graph->addDependency($nodeE, $nodeA);
echo json_encode($graph->toArray(), JSON_PRETTY_PRINT);
// Outputs:
//  [
//      {
//          "A": [
//              {
//                  "B": [
//                      "D"
//                  ]
//              },
//              {
//                  "C": [
//                      "D",
//                      "E"
//                  ]
//              }
//          ]
//      }
//  ]

// Add a node that already has dependencies to an adjacent branch
$graph->addDependency($nodeD, $nodeC);
// Tree Status:
//       A
//      / \
//     B   C
//    /   / \
//   D   E   F
//    \
//     C
//    / \
//   E   F

echo json_encode($graph->toArray(), JSON_PRETTY_PRINT);
// Outputs:
//  [
//      {
//          "A": [
//              {
//                  "B": [
//                      {
//                          "D": [
//                              {
//                                  "C": [
//                                      "E",
//                                      "F"
//                                  ]
//                              }
//                          ]
//                      }
//                  ]
//              },
//              {
//                  "C": [
//                      "E",
//                      "F"
//                  ]
//              }
//          ]
//      }
//  ]

echo implode(",", $graph->flatten());
```