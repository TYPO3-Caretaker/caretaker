tx.caretaker.NodeTree = Ext.extend(Ext.tree.TreePanel, {
    constructor: function(config) {	
	    config = Ext.apply({
	    	stateful: true,
			useArrows: false,
	    	stateEvents: ['collapsenode', 'expandnode'],
	        root: {
	            nodeType: 'async',
	    		expanded: true,
	            text: 'Caretaker',
	            draggable: false,
	            id: 'root',
	            cls: 'global',
	            iconCls: 'icon-caretaker-root'
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
	
	    this.editUrl    = config.editUrl;
		this.addUrl     = config.addUrl;
		
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
                    iconCls: 'icon-edit',
                    handler: function() {
	            		var node = this.getSelectionModel().getSelectedNode();
	            		this.editNode(node);
	            	}, 
                    scope: this
                }, {
                	id: 'tree-contextmenu-hide',
                	// itemId: 'hide',
                	text: 'Disable',
                	iconCls: 'icon-hide',
                    handler: function() {
	            		var node = this.getSelectionModel().getSelectedNode();
	            		this.hideNode(node);
	            	},
                    scope: this
                }, {
                	id: 'tree-contextmenu-unhide',
                	// itemId: 'unhide',
                	text: 'Enable',
                	iconCls: 'icon-unhide',
                    handler: function() {
	            		var node = this.getSelectionModel().getSelectedNode();
	            		this.unhideNode(node);
	            	},
                    scope: this
                },'-',  {
                	id: 'tree-contextmenu-add-test',
                	text: 'Create new test',
                	iconCls: 'icon-test',
                    handler: function() {
	            		var node = this.getSelectionModel().getSelectedNode();
						this.addTest(node);
	            	},
                    scope: this
                },  {
                	id: 'tree-contextmenu-add-testgroup',
                	text: 'Create new testgroup',
                	iconCls: 'icon-testgroup',
                    handler: function() {
	            		var node = this.getSelectionModel().getSelectedNode();
						this.addTestgroup(node);
	            	},
                    scope: this
                },  {
                	id: 'tree-contextmenu-add-instance',
                	text: 'Create new Instance',
                	iconCls: 'icon-instance',
                    handler: function() {
	            		var node = this.getSelectionModel().getSelectedNode();
						this.addInstance(node);
	            	},
                    scope: this
                },  {
                	id: 'tree-contextmenu-add-instancegroup',
                	text: 'Create new Instancegroup',
                	iconCls: 'icon-instancegroup',
                    handler: function() {
	            		var node = this.getSelectionModel().getSelectedNode();
	            		this.addInstancegroup(node);
	            	},
                    scope: this
                },
                '-',
				{
					id: 'tree-contextmenu-open-instance-url',
					text: 'Open Instance URL',
					iconCls: 'icon-instance',
					handler: function(){
	                	var node = this.getSelectionModel().getSelectedNode();
						window.open(node.attributes.url);
					},
					scope: this
				},
				{
					id: 'tree-contextmenu-open-instance-url-typo3',
					text: 'Open Instance TYPO3 Login',
					iconCls: 'icon-instance',
					handler: function(){
						var node = this.getSelectionModel().getSelectedNode();
						window.open(node.attributes.url + '/typo3/');
					},
					scope: this
				}
		]
            }); 
        }

        if (node.attributes.type || node.attributes.id == 'root') {
				// show && hide
        	var editItem   = Ext.getCmp('tree-contextmenu-edit');
        	var hideItem   = Ext.getCmp('tree-contextmenu-hide');
        	var unhideItem = Ext.getCmp('tree-contextmenu-unhide');

			if ( node.attributes.id != 'root') {
				if (node.attributes.disabled)  {
					hideItem.disable();
					unhideItem.enable();
				} else {
					hideItem.enable();
					unhideItem.disable();
				}
			} else {
				editItem.disable();
				hideItem.disable();
				unhideItem.disable();
			}

				// add test && testgroup
			var addTest = Ext.getCmp('tree-contextmenu-add-test');
			var addTestgroup = Ext.getCmp('tree-contextmenu-add-testgroup');
			if ( ( node.attributes.type == 'instance' || node.attributes.type == 'testgroup' )  && node.attributes.id != 'root') {
				addTest.enable();
				addTestgroup.enable();
			} else {
				addTest.disable();
				addTestgroup.disable();
			}

				// add instance && instancegroup
			var addInstance = Ext.getCmp('tree-contextmenu-add-instance');
			var addInstancegroup = Ext.getCmp('tree-contextmenu-add-instancegroup');
			if ( node.attributes.id == 'root' || node.attributes.type == 'instancegroup' ) {
				addInstance.enable();
				addInstancegroup.enable();
			} else {
				addInstance.disable();
				addInstancegroup.disable();
			}
				// open url
			var openInstanceUrl = Ext.getCmp('tree-contextmenu-open-instance-url');
			var openInstanceUrlTypo3 = Ext.getCmp('tree-contextmenu-open-instance-url-typo3');
			if ( node.attributes.type == 'instance' ) {
				openInstanceUrl.enable();
				openInstanceUrlTypo3.enable();
			} else {
				openInstanceUrl.disable();
				openInstanceUrlTypo3.disable();
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
	addTest: function(node) {
		var url = '';
		var add_record_type = 'tx_caretaker_test';

		if (node.attributes.type == 'instance'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
			url += '&defVals[' + add_record_type + '][instances]=' + node.attributes.uid ;

		}

		if (node.attributes.type == 'testgroup'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
			url += '&defVals[' + add_record_type + '][groups]=' + node.attributes.uid ;
		}

		if (url) {
			url += "&returnUrl=" + encodeURIComponent(parent.list_frame.document.location);
			this.openUrlInContent(url);
		}

	},
	
	addTestgroup: function(node) {
		var url = '';
		var add_record_type = 'tx_caretaker_testgroup';

		if (node.attributes.type == 'instance'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
			url += '&defVals[' + add_record_type + '][instances]=' + node.attributes.uid ;

		}

		if (node.attributes.type == 'testgroup'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
			url += '&defVals[' + add_record_type + '][parent_group]=' + node.attributes.uid ;
		}

		if (url) {
			url += "&returnUrl=" + encodeURIComponent(parent.list_frame.document.location);
			this.openUrlInContent(url);
		}

	},
	
	addInstance: function(node) {
		var url = '';
		var add_record_type = 'tx_caretaker_instance';

		if (node.attributes.type == 'instancegroup'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
			url += '&defVals[' + add_record_type + '][instancegroup]=' + node.attributes.uid ;
		}

		if (node.attributes.id == 'root'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
		}

		if (url) {
			url += "&returnUrl=" + encodeURIComponent(parent.list_frame.document.location);
			this.openUrlInContent(url);
		}
	},

	addInstancegroup: function(node) {
		var url = '';
		var add_record_type = 'tx_caretaker_instancegroup';

		if (node.attributes.type == 'instancegroup'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
			url += '&defVals[' + add_record_type + '][parent_group]=' + node.attributes.uid ;
		}

		if (node.attributes.id == 'root'){
			url = this.addUrl.replace('###NODE_TYPE###', add_record_type);
		}

		if (url) {
			url += "&returnUrl=" + encodeURIComponent(parent.list_frame.document.location);
			this.openUrlInContent(url);
		}

	},
	
	reloadTree: function() {
		this.temp_state = this.getState();
		this.root.reload( function(){ this.applyState( this.temp_state ); },  this  );
	},
	
	reloadTreeDeferred: function(defer){
		if (defer){
			this.reloadTree.defer(defer, this);
		}else{
			this.reloadTree();
		}
	}, 
	
	reloadTreePartial: function ( id ){
		var node = this.getNodeById( id );
		if (node ) {
			var parentNode = node.parentNode;
			this.temp_state = this.getState();
			if ( parentNode && parentNode.reload ) {
				parentNode.reload( function(){ this.applyState( this.temp_state ); },  this );
				return;
			}
		}
		this.reloadTree();
	} 
	
});

Ext.reg('caretaker-nodetree', tx.caretaker.NodeTree);
