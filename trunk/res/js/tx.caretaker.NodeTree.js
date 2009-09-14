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
	            click: this.navigateToNodeDetails,
	            contextmenu: this.showContextMenu
	        },
	        title: 'Navigation',
	        tools:[{
                id: 'minus',
                qtip: 'Collapse All',
                handler: function(event, toolEl, panel) {
            		panel.root.collapse(true);
            	}
            }, {
	        	id: 'plus',
				qtip: 'Expand All',
                handler: function(event, toolEl, panel) {
	        		panel.root.expand(true);
	        	}
            }, {
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
	
	    this.editUrl = config.editUrl;
	},
	showContextMenu: function(node, eventObj) {
		node.select(); 
		eventObj.stopEvent();

        if (!this.contextMenu) {
            this.contextMenu = new Ext.menu.Menu({
            	id: 'tree-contextmenu',
	            items: [{
                    id: 'tree-contextmenu-edit',
                    // itemId: 'edit',
                    text: 'Edit',
                    handler: function() {
	            		var node = this.getSelectionModel().getSelectedNode();
	            		this.editNode(node);
	            	}, 
                    scope: this
                }, {
                	id: 'tree-contextmenu-hide',
                	// itemId: 'hide',
                	text: 'Hide',
                    handler: function() {
	            		var node = this.getSelectionModel().getSelectedNode();
	            		this.hideNode(node);
	            	},
                    scope: this
                }, {
                	id: 'tree-contextmenu-unhide',
                	// itemId: 'unhide',
                	text: 'Unhide',
                    handler: function() {
	            		var node = this.getSelectionModel().getSelectedNode();
	            		this.unhideNode(node);
	            	},
                    scope: this
                }]
            }); 
        }

        if (node.attributes.type) {
        	var editItem = Ext.getCmp('tree-contextmenu-edit');
        	var hideItem = Ext.getCmp('tree-contextmenu-hide');
        	var unhideItem = Ext.getCmp('tree-contextmenu-unhide');
        	if (node.attributes.hidden) {
        		hideItem.disable();
        		unhideItem.enable();
        	} else {
        		hideItem.enable();
        		unhideItem.disable();
        	}
        	this.contextMenu.showAt(eventObj.getXY());
        }
	},
	openUrlInContent: function(url) {
		if (top.condensedMode)	{
			top.content.document.location = url;
		} else {
			parent.list_frame.document.location = url;
		}
	},
	navigateToNodeDetails: function(node) {
		var params = 'id=' + node.attributes.id;
		var url = top.TS.PATH_typo3 + top.currentSubScript + '?' + params;
		this.openUrlInContent(url);
	},
	editNode: function(node) {
		var url = this.editUrl.
			replace('###NODE_TYPE###', node.attributes.type).
			replace('###NODE_UID###', node.attributes.uid);
		url += "&returnUrl=" + encodeURIComponent(parent.list_frame.document.location);
		this.openUrlInContent(url);
	},
	hideNode: function(node) {
		var url = this.hideUrl.
			replace('###NODE_TYPE###', node.attributes.type).
			replace('###NODE_UID###', node.attributes.uid);
		Ext.Ajax.request({
			url: url,
			success: function() {
				tx.caretaker.fireEvent('nodeChanged', node);
				this.reloadTree();
			},
			failure: function() {
				Ext.Msg.show({
					title: 'Failure',
					text: 'Could not hide node',
					icon: Ext.MessageBox.WARNING
				});
			},
			scope: this
		});
	},
	unhideNode: function(node) {
		var url = this.unhideUrl.
			replace('###NODE_TYPE###', node.attributes.type).
			replace('###NODE_UID###', node.attributes.uid);
		Ext.Ajax.request({
			url: url,
			success: function() {
				this.reloadTree();
			},
			failure: function() {
				Ext.Msg.show({
					title: 'Failure',
					text: 'Could not unhide node',
					icon: Ext.MessageBox.WARNING
				});
			},
			scope: this
		});
	},
	reloadTree: function() {
		var state = this.getState();
		this.root.reload();
		this.applyState(state);
	}
});

Ext.reg('caretaker-nodetree', tx.caretaker.NodeTree);
