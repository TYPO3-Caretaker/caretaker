tx.caretaker.NodeTree = Ext.extend(Ext.tree.TreePanel, {
    constructor: function(config) {	
	    config = Ext.apply({
	    	stateful: true,
	    	stateEvents: ['collapsenode', 'expandnode'],
	        root: {
	            nodeType: 'async',
	    		expanded: true,
	            text: 'Caretaker',
	            draggable: false,
	            id: 'root',
	            cls: 'global'
	        },
	        listeners: {
	            click: this.navigateToNode
	        },
	        title: 'Navigation',
	        tools:[{
	            id:'refresh',
	            qtip: 'Refresh tree',
	            handler: function(event, toolEl, panel) {
	                panel.reloadTree();
	            }
	        }],
	        getState: function() {
	            var nodes = [];
	            this.getRootNode().eachChild(function (child) {
	                //function to store state of tree recursively
	                var storeTreeState = function (node, expandedNodes) {
	                    if(node.isExpanded() && node.childNodes.length > 0) {
	                        expandedNodes.push(node.getPath());
	                        node.eachChild(function (child) {
	                            storeTreeState(child, expandedNodes);
	                        });
	                    }
	                };
	                storeTreeState(child, nodes);
	            });
	            
	            return {expandedNodes: nodes};
	        },
	        applyState: function(state) {
	            this.getLoader().on('load', function() {
	                var nodes = state.expandedNodes;
	                for(var i = 0; i < nodes.length; i++) {
	                    if(typeof nodes[i] != 'undefined') {
	                        this.expandPath(nodes[i]);
	                    }
	                }
	            }, this, {single: true});
	        }
	    }, config);
	
	    tx.caretaker.NodeTree.superclass.constructor.call(this, config);
	
	//  Your postprocessing here
	
	},
	
	navigateToNode: function(node) {
		var params = 'id=' + node.attributes.id;
		var theUrl = top.TS.PATH_typo3 + top.currentSubScript + '?' + params;
		if (top.condensedMode)	{
			top.content.document.location = theUrl;
		} else {
			parent.list_frame.document.location = theUrl;
		}	
	},
	reloadTree: function() {
		var state = this.getState();
		this.root.reload();
		this.applyState(state);
	}
});

Ext.reg('caretaker-nodetree', tx.caretaker.NodeTree);
